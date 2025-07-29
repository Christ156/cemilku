<?php
namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Collection;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Snack;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        if (Auth::user()->role == 'admin') {
            $snackCount      = Snack::count();
            $collectionCount = Collection::count();
            $orderCount      = Order::count();
            $userCount       = User::count();

            // Collection terlaris minggu ini
            $topCollections = Collection::withSum(['orderDetails as total_sold' => function ($q) {
                $q->whereBetween('created_at', [now()->subWeek(), now()]);
            }], 'quantity')
                ->orderByDesc('total_sold')
                ->take(3)
                ->get();

            // Grafik penjualan 7 hari terakhir
            $salesChart = [
                'labels' => [],
                'data'   => [],
            ];

            foreach (range(6, 0) as $day) {
                $date                   = now()->subDays($day)->format('Y-m-d');
                $salesChart['labels'][] = now()->subDays($day)->format('d M');
                $salesChart['data'][]   = OrderDetail::whereDate('created_at', $date)->sum('quantity');
            }

            $latestOrders = Order::with([
                'user:id,name',
                'orderDetails.collection:id,name,image',
            ])
                ->orderByDesc('id') // urutkan berdasarkan ID terbaru
                ->take(5)
                ->get();

            return view('admin.dashboard', compact(
                'snackCount',
                'collectionCount',
                'orderCount',
                'userCount',
                'topCollections',
                'salesChart',
                'latestOrders'
            ));
        } else {
            return view('home');
        }
    }

}
