<?php
namespace App\Http\Controllers;

use App\Exports\OrderExport;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->input('status');
        $search = $request->input('search');
        $user   = Auth::user();

        // Base query
        $query = Order::with([
            'orderDetails' => function ($q) {
                $q->orderBy('id'); // Urutkan detail pesanan
            },
            'orderDetails.collection',
            'orderDetails.customize',
            'user.mainAddress',
        ]);

        // Filter berdasarkan role
        if ($user->role !== 'admin') {
            $query->where('user_id', $user->id);
        }

        // Filter berdasarkan status
        if ($status && $status !== 'all') {
            $query->where('status', $status);
        }

        // Filter berdasarkan pencarian
        if ($request->filled('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('id', 'like', "%{$search}%")
                    ->orWhereHas('orderDetails.collection', function ($q2) use ($search) {
                        $q2->where('name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('orderDetails.customize', function ($q3) use ($search) {
                        $q3->where('name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('user', function ($q4) use ($search) {
                        $q4->where('name', 'like', "%{$search}%");
                    });
            });
        }

        // Ambil data terbaru
        $orders = $query->latest()->get();

        // Tampilkan view sesuai role
        if ($user->role === 'admin') {
            return view('admin.order.index', compact('orders', 'status'));
        } else {
            return view('orders', compact('orders', 'status'));
        }
    }

    public function pay(Order $order)
    {
        if ($order->status !== 'pending') {
            return redirect()->back()->with('error', 'Pesanan ini tidak dapat dibayar.');
        }

        // Update status jadi "paid"
        $order->status = 'paid';
        $order->save();

        return redirect()->route('orders.index')->with('success', 'Pembayaran berhasil diproses.');
    }

    public static function getStatusColor($status)
    {
        return match ($status) {
            'pending' => '#FDC607',   // Belum Bayar
            'paid' => '#52282A',      // Diproses
            'shipped' => '#00D9F5',   // Dikirim
            'completed' => '#28a745', // Selesai
            'cancelled' => '#dc3545', // Dibatalkan
            default => '#6c757d',     // Default abu-abu
        };
    }

    public function edit(Order $order)
    {
        // Pastikan hanya admin yang bisa mengakses
        if (Auth::user()->role !== 'admin') {
            abort(403);
        }

        return view('admin.order.edit', compact('order'));
    }

    public function update(Request $request, Order $order)
    {
        // Validasi input
        $validated = $request->validate([
            'status' => 'required|in:pending,paid,shipped,completed,cancelled',
        ]);

        // Cek apakah status saat ini adalah 'paid' dan status yang diminta adalah 'shipped'
        if ($order->status === 'paid' && $validated['status'] === 'shipped') {
            $order->status = 'shipped';
            $order->save();

            return redirect()->route('adminorder.index')->with('success', 'Order updated successfully.');
        }

        // Jika tidak sesuai aturan, tolak perubahan
        return redirect()->back()->with('error', 'Status hanya bisa diubah dari Paid ke Shipped.');
    }

    public function export()
    {
        return Excel::download(new OrderExport, 'orders.xlsx');
    }

}
