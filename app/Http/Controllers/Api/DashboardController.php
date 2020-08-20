<?php

namespace App\Http\Controllers\Api;

use App\Customer;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Order;
use App\Product;
use App\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Ambil total data produk, pesanan, pelanggan, karyawan
     *
     * @return Response
     */
    public function index()
    {
        $product = Product::count();
        $order = Order::count();
        $customer = Customer::count();
        $user = User::count();

        return response()->json(['product' => $product, 'order' => $order, 'customer' => $customer, 'user' => $user]);
    }

    /**
     * Generate data order 7 hari terakhir
     *
     * @return Response
     */
    public function getChart()
    {
        // mengambil tanggal 7 hari yang telah lalu dari hari ini
        $start = Carbon::now()->subWeek()->addDay()->format('Y-m-d') . ' 00:00:01';
        // mengambil tanggal hari ini
        $end = Carbon::now()->format('Y-m-d') . ' 23:59:00';

        // select kapan records dibuat dan juga total pesanan
        $order = Order::select(DB::raw('date(created_at) as order_date'), DB::raw('count(*) as total_order'))
            // kondisi antara tanggal yang ada di variabel $start dan $end
            ->whereBetween('created_at', [$start, $end])
            // dikelompokkan berdasarkan tanggal
            ->groupBy('created_at')
            ->get()->pluck('total_order', 'order_date')->all();

        // looping tanggal dengan interval 7 hari terakhir
        for ($i = Carbon::now()->subWeek()->subDay(); $i <= Carbon::now(); $i->addDay()) {
            // jika datanya ada
            if (array_key_exists($i->format('Y-m-d'), $order)) {
                // total pesanannya di push dengan key tanggal
                $data[$i->format('Y-m-d')] = $order[$i->format('Y-m-d')];
            } else {
                // jika tidak ada, masukkan nilai 0
                $data[$i->format('Y-m-d')] = 0;
            }
        }

        return response()->json(['status' => 'success', 'data' => $data], 200);
    }
}
