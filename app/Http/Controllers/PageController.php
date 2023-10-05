<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\CompanyDetail;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;

class PageController extends Controller
{
    public function getCartData()
    {
        $uniqueId = Cookie::get('unique_id');

        $cart = Cart::where('unique_id', $uniqueId)->get();

        return $cart;
    }
    public function getCartCount()
    {
        $cart = $this->getCartData();

        $cartCount = 0;
        if (count($cart) > 0) {
            $cartCount = count($cart);
        }

        return $cartCount;
    }
    public function home()
    {
        // $cartToken = session('cartToken');
        // if (session("cartData_$cartToken")) {
        //     $cartCount = count(session("cartData_$cartToken"));
        // }

        $uniqueId = Cookie::get('unique_id');

        $products = Product::all();

        $cartCount = $this->getCartCount();

        return view('index', compact('products', 'cartCount'));
    }

    public function showCart()
    {
        // $cartToken = session('cartToken');
        // $cartDataInsideSession = session("cartData_$cartToken");

        $company = CompanyDetail::find(1);

        $cartCount = $this->getCartCount();

        $cart = [];

        $subTotal = 0;
        $total = 0;

        if ($cartDataInsideSession) {
            foreach ($cartDataInsideSession as $c) {
                $cart[] = Product::where('slug', $c)->first();
            };
            foreach ($cart as $c) {
                $subTotal += $c->price;
            }
            $total += $subTotal + $company->tax;
        }
        return view('cart', compact('cartCount', 'cart', 'subTotal', 'company', 'total'));
    }

    public function productDetail($slug)
    {
        $cartToken = session('cartToken');

        $product = Product::where('slug', $slug)->first();

        $cartCount = 0;
        if (session("cartData_$cartToken")) {
            $cartCount = count(session("cartData_$cartToken"));
        }

        return view('product-detail', compact('product', 'cartCount'));
    }
}
