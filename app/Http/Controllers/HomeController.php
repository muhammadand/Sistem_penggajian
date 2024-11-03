<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Order; // Pastikan Anda sudah membuat model Product
use Illuminate\Http\RedirectResponse;

class HomeController extends Controller
{
    public function index()
    {
        // Mengambil jumlah total produk dari database
        $totalProducts = Product::count();
        $totalOrders = Order::count();
        $products = Product::all();

        // Mengirimkan data total produk ke tampilan
        return view('home', compact('totalProducts','totalOrders','products'));
    }

    public function shop()
    {
        // Mengambil jumlah total produk dari database
        $totalProducts = Product::count();
        $totalOrders = Order::count();
        $products = Product::all();

        // Mengirimkan data total produk ke tampilan
        return view('shop', compact('totalProducts','totalOrders','products'));
    }

    public function contact()
    {
        // Mengambil jumlah total produk dari database
        $totalProducts = Product::count();
        $totalOrders = Order::count();
       
        $products = Product::all();

        // Mengirimkan data total produk ke tampilan
        return view('contact', compact('totalProducts','totalOrders','products'));
    }

    public function pembayaran()
    {
        // Mengambil jumlah total produk dari database
        $totalProducts = Product::count();
        $totalOrders = Order::count();
       
        $products = Product::all();

        // Mengirimkan data total produk ke tampilan
        return view('pembayaran', compact('totalProducts','totalOrders','products'));
    }


   
    public function destroy(Order $order): RedirectResponse
    {
        // Hapus pesanan dari database
        $order->delete();

        // Redirect ke halaman yang sesuai setelah pesanan berhasil dihapus
        return redirect()->route('orders.index')->with('success', 'Order deleted successfully!');
    }

    public function profil()
    {
        // Mengambil jumlah total produk dari database
        $totalProducts = Product::count();
        $products = Product::latest()->paginate(5);
        $totalOrders = Order::count();
        $username = Auth::user()->username;
        $name = Auth::user()->name;
        $password = Auth::user()->password;
        $totalRevenue = Order::sum('total_price');
        $orders = Order::latest()->paginate(10);

        // Mengirimkan data total produk ke tampilan
        return view('user.profil', compact('name','password','orders','products','totalProducts','totalOrders','username','totalRevenue'));
    }
    
}
