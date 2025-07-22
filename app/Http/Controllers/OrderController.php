<?php
namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->input('status');
        $userId = Auth::user()->id;

        $query = Order::with([
            'orderDetails.collection',
            'orderDetails.customize',
            'user.mainAddress',
        ])->where('user_id', $userId);

        // Filter status jika ada
        if ($status && $status !== 'all') {
            $query->where('status', $status);
        }

        // Filter berdasarkan keyword pencarian
        if ($request->filled('search')) {
            $search = $request->input('search');

            $query->where(function ($q) use ($search) {
                $q->where('id', 'like', "%{$search}%")
                    ->orWhereHas('orderDetails.collection', function ($q2) use ($search) {
                        $q2->where('name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('orderDetails.customize', function ($q3) use ($search) {
                        $q3->where('name', 'like', "%{$search}%");
                    });
            });
        }

        // Eksekusi query
        $orders = $query->latest()->get();

        return view('orders', compact('orders', 'status'));
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

}
