<?php

namespace App\Http\Controllers\Api;

use App\Customer;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Order;
use App\Product;
use App\User;
use Barryvdh\DomPDF\Facade as PDF;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class OrderController extends Controller
{
    /**
     *
     */
    public function index(Request $request)
    {
        // mengambil data customer
        $customers = Customer::orderBy('name', 'asc')->get();

        // mengambil data user
        $users = User::orderBy('name', 'asc')->get();

        // mengambil data transaksi
        $orders = Order::orderBy('created_at', 'desc')->with(['order_detail', 'customer', 'user']);

        // jika memilih pelanggan
        if (!empty($request->customer_id)) {
            $orders = $orders->where('customer_id', $request->customer_id);
        }

        // jika memilih user
        if (!empty($request->user_id)) {
            $orders = $orders->where('user_id', $request->user_id);
        }

        if (!empty($request->start_date) && !empty($request->end_date)) {
            $validator = Validator::make($request->all(), [
                'start_date' => 'nullable|date',
                'end_date' => 'nullable|date'
            ]);

            if ($validator->fails()) {
                return response()->json(['status' => 'invalid', 'message' => $validator->errors()]);
            }

            //START & END DATE DI RE-FORMAT MENJADI Y-m-d H:i:s
            $start_date = Carbon::parse($request->start_date)->format('Y-m-d') . ' 00:00:01';
            $end_date = Carbon::parse($request->end_date)->format('Y-m-d') . ' 23:59:59';

            $orders = $orders->whereBetween('created_at', [$start_date, $end_date])->get();
        } else {
            $orders = $orders->take(10)->skip(0)->get();
        }

        return response()->json([
            'orders' => $orders,
            'sold' => $this->countItem($orders),
            'total' => $this->countTotal($orders),
            'total_customer' => $this->countCustomer($orders),
            'customers' => $customers,
            'users' => $users
        ]);
    }

    /**
     * Menghitung total pelanggan
     *
     * @param array $orders
     * @return integer
     */
    private function countCustomer($orders)
    {
        // definisi array kosong
        $customer = [];

        // jika orders tidak kosong
        if ($orders->count() > 0) {
            // looping untuk menyimpan email kedalam array
            foreach ($orders as $row) {
                $customer[] = $row->customer->email;
            }
        }
        // menghitung total data yang ada didalam array
        // data yang duplicate akan dihapus menggunakan array_unique
        return count(array_unique($customer));
    }

    /**
     * Menghitung total
     *
     * @param array $orders
     * @return integer
     */
    private function countTotal($orders)
    {
        // set default total 0
        $total = 0;
        // jika ada data
        if ($orders->count() > 0) {
            // ambil value dari total menggunakan pluck dan hasilnya akan menjadi array
            $sub_total = $orders->pluck('total')->all();
            // menjumlahkan data yang ada didalam array
            $total = array_sum($sub_total);
        }
        return $total;
    }

    /**
     * Menghitung total item
     *
     * @param array $order
     * return integer
     */
    private function countItem($order)
    {
        // default data 0
        $data = 0;
        // jika ada data
        if ($order->count() > 0) {
            // lakukan looping
            foreach ($order as $row) {
                // mengambil qty
                $qty = $row->order_detail->pluck('qty')->all();
                // menjumlahkan qty
                $val = array_sum($qty);
                $data += $val;
            }
        }
        return $data;
    }

    public function invoicePdf($invoice)
    {
        // ambil data transaksi berdasarkan invoice
        $order = Order::where('invoice', $invoice)->with('customer', 'order_detail', 'order_detail.product')->first();

        // set config pdf menggunakan font sans-serif dan load view invoice.blade.php
        $pdf = PDF::setOptions(['dpi' => 150, 'defaultFont' => 'sans-serif'])
            ->loadView('orders.report.invoice', compact('order'));

        return $pdf->output();
    }

    public function invoiceExcel($invoice)
    {
    }

    /**
     * Menambahkan produk kedalalam keranjang
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function addToCart(Request $request)
    {
        // validasi request
        $validator = Validator::make($request->all(), [
            'product_id' => 'required|exists:products,id',
            'qty' => 'required|integer'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'invalid', 'message' => $validator->errors()]);
        }

        try {
            $product = Product::findOrFail($request->product_id);

            $getCart = json_decode($request->cookie('cart'), true);

            if ($getCart) {
                if (array_key_exists($request->product_id, $getCart)) {
                    $getCart[$request->product_id]['qty'] += $request->qty;

                    return response()->json($getCart, 200)->cookie('cart', json_encode($getCart), 120);
                }
            }

            $getCart[$request->product_id] = [
                'code' => $product->code,
                'name' => $product->name,
                'price' => $product->price,
                'qty' => $request->qty
            ];

            return response()->json($getCart, 200)->cookie('cart', json_encode($getCart), 120);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()]);
        }
    }

    /**
     * Menampilkan data didalam keranjang
     *
     * @return @return \Illuminate\Http\Response
     */
    public function getCart()
    {
        try {
            $cart = json_decode(request()->cookie('cart'), true);

            return response()->json($cart, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()]);
        }
    }

    /**
     * Menghapus produk dari keranjang
     *
     * @param $id
     * @return @return \Illuminate\Http\Response
     */
    public function removeProductFromCart($id)
    {
        try {
            $cart = json_decode(request()->cookie('cart'), 200);

            unset($cart[$id]);

            return response()->json(['status' => 'success', 'data' => $cart], 200)->cookie('cart', json_encode($cart), 120);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()]);
        }
    }

    /**
     * Membuat Transaksi
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // validasi request
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'name' => 'required|string|max:100',
            'address' => 'required',
            'phone' => 'required|numeric'
        ]);

        $cart = json_decode($request->cookie('cart'), true);

        $result = collect($cart)->map(function ($val) {
            return [
                'code' => $val['code'],
                'name' => $val['name'],
                'qty' => $val['qty'],
                'price' => $val['price'],
                'result' => $val['price'] * $val['qty']
            ];
        })->all();

        //database transaction
        DB::beginTransaction();
        try {
            //menyimpan data ke table customers
            $customer = Customer::firstOrCreate([
                'email' => $request->email
            ], [
                'name' => $request->name,
                'address' => $request->address,
                'phone' => $request->phone
            ]);

            // menyimpan data ke tabel orders
            $order = Order::create([
                'invoice' => $this->generateInvoice(),
                'customer_id' => $customer->id,
                'user_id' => Auth::id(),
                'total' => array_sum(array_column($result, 'result'))
                //array_sum untuk menjumlahkan value dari result
            ]);

            //looping cart untuk disimpan ke tabel order_details
            foreach ($result as $key => $row) {
                $order->order_detail()->create([
                    'product_id' => $key,
                    'qty' => $row['qty'],
                    'price' => $row['price']
                ]);
            }
            // jika tidak ada error, penyimpanan diverifikasi
            DB::commit();

            // mengirim status dan pesan berupa kode invoice, dan menghapus cookie
            return response()->json(['status' => 'success', 'message' => $order->invoice], 200)->cookie(Cookie::forget('cart'));
        } catch (\Exception $e) {
            // jika error, maka database akan dirollback sehingga tidak ada yang disimpan
            DB::rollback();
            // kirim pesan gagal
            return response()->json(['status' => 'failed', 'message' => $e->getMessage()]);
        }
    }

    /**
     * Membuat Invoice
     *
     * @return string
     */
    public function generateInvoice()
    {
        //mengambil data dari table orders
        $order = Order::orderBy('created_at', 'DESC');
        //jika sudah terdapat records
        if ($order->count() > 0) {
            //mengambil data pertama yang sdh dishort DESC
            $order = $order->first();
            //explode invoice untuk mendapatkan angkanya
            $explode = explode('-', $order->invoice);
            //angka dari hasil explode di +1
            return 'INV-' . ($explode[1] + 1);
        }
        //jika belum terdapat records maka akan me-return INV-1
        return 'INV-1';
    }
}
