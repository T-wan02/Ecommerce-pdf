<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\CompanyDetail;
use App\Models\Payment;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Log;
use Stripe\Charge;
use Stripe\Stripe;

class OrderController extends Controller
{
    public function generateUniqueId()
    {
        // Generate a unique identifier, for example, using Laravel's Str::uuid() method.
        return \Illuminate\Support\Str::uuid();
    }

    public function addToCart(Request $request)
    {
        $uniqueId = $request->cookie('unique_id');

        if ($uniqueId) {
            $uniqueId = $this->generateUniqueId(); // Generate a unique identifier.

            // Define the number of minutes you want the cookie to persist.
            $minutes = 60 * 24 * 7; // 7 days

            Cookie::queue('unique_id', $uniqueId, $minutes);
        }

        // Retrieve product slug from the request
        $productSlug = $request->slug;

        $product = Product::where('slug', $productSlug)->first();
        $cart = Cart::where('unique_id', $uniqueId)->get();

        // Check if the product exists in the cart by comparing 'id' values.
        $productExistsInCart = $cart->contains('product_id', $product->id);

        if (!$productExistsInCart) {
            Cart::create([
                'product_id' => $product->id,
                'unique_id' => $uniqueId
            ]);
        }

        $allCartForThisUser = Cart::where('unique_id', $uniqueId)->get();

        // Respond with a success message or updated cart data
        return response()->json([
            'status' => 200,
            'stage' => 'success',
            'message' => 'Product added to cart',
            'data' => $allCartForThisUser,
            'count' => count($allCartForThisUser)
        ]);

        // $cartToken = session()->get('cartToken');
        // if (!$cartToken) {
        //     $cartToken = uniqid();
        //     session(['cartToken' => $cartToken]);
        // }

        // // Initialize an empty cart in the session if it doesn't exist
        // if (!session("cartData_$cartToken")) {
        //     $cart = [];
        // } else {
        //     $cart = session()->get("cartData_$cartToken");
        // }
        // // $cart = [];

        // // Check if the product slug is not already in the cart
        // if (!in_array($productSlug, $cart)) {
        //     // Add the product slug to the cart array
        //     $cart[] = $productSlug;

        //     // Update the cart data in the session
        //     session(["cartData_$cartToken" => $cart]);

        //     // Respond with a success message or updated cart data
        //     return response()->json([
        //         'status' => 200,
        //         'stage' => 'success',
        //         'message' => 'Product added to cart',
        //         'data' => $cart,
        //         'count' => count($cart)
        //     ]);
        // }

        // // If the product slug is already in the cart, respond with a message
        // return response()->json([
        //     'status' => 200,
        //     'stage' => 'success',
        //     'message' => 'Product is already in the cart',
        //     'data' => $cart,
        //     'count' => count($cart)
        // ]);
    }

    public function removeCart(Request $request)
    {
        $cartToken = session()->get('cartToken');

        $company = CompanyDetail::find(1);

        $cart = session("cartData_$cartToken");

        $removedCartItem = array_search($request->slug, $cart);
        if ($removedCartItem !== false) {
            // Remove the slug from the array
            array_splice($cart, $removedCartItem, 1);

            // Store the updated cart back in the session
            session(["cartData_$cartToken" => $cart]);

            $updatedCart = session("cartData_$cartToken");

            $carts = [];
            foreach ($updatedCart as $uc) {
                $carts[] = Product::where('slug', $uc)->first();
            }

            $subTotal = 0;
            foreach ($carts as $c) {
                $subTotal += $c->price;
            }
            $total = $subTotal + $company->tax;

            return response()->json([
                'status' => 202,
                'stage' => 'success',
                'message' => 'Cart has been removed',
                'cartCount' => count($updatedCart),
                'subTotal' => $subTotal,
                'tax' => $company->tax,
                'total' => $total
            ]);
        }
        return response()->json([
            'status' => 404,
            'stage' => 'failed',
            'message' => 'Cart not found. Please try again or contact us.',
        ], 404);
    }

    public function showCheckout(Request $request)
    {
        $company = CompanyDetail::find(1);

        $cartToken = $request->cartToken;

        if ($cartToken) {
            // Retrieve the cart data associated with the cartToken
            // $cartData = session("cartData_$cartToken");
            $cartData = session("cartData_$cartToken");

            if ($cartData) {
                $cartCount = 0;
                if (session("cartData_$cartToken")) {
                    $cartCount = count(session("cartData_$cartToken"));
                }
                $cartDataInsideSession = session("cartData_$cartToken");

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
                return view('checkout', compact('cartCount', 'cart', 'subTotal', 'company', 'total', 'cartToken'));
                // Process and use the cart data as needed
                // Example: $cart = $cartData['cart'];
            } else {
                return redirect('/');
            }
        }
    }

    public function checkout(Request $request)
    {
        Stripe::setApiKey(env('STRIPE_SECRET'));

        // return $request->stripeToken;
        $email = $request->email;
        $paymentInfo = json_decode($request->paymentInfo);
        $token = $request->stripeToken;

        return $request->all();

        try {
            $charge = Charge::create([
                'amount' => $request->total * 100,
                'curency' => 'usd',
                'source' => $token,
                'description' => 'Purchase pdf'
            ]);

            Payment::create([
                'order_id' => session('cartToken'),
                'name' => $paymentInfo->fName + $paymentInfo->lName,
                'transaction_id' => $charge->id,
                'status' => Charge::retrieve($charge->id, Stripe::setApiKey(env('STRIPE_SECRET')))->status,
                'card_last_four' => $charge->payment_method_details->card->last4,
                'card_brand' => $charge->payment_method_details->card->brand,
                'currency' => 'usd',
                'total' => $request->total,
                'giving_date' => Carbon::now()->format('d-m-Y')
            ]);
        } catch (\Stripe\Exception\CardException $e) {
            // Payment failed message to user with reason
            return redirect()->back()->with('error', 'Payment failed: ' . $e->getError()->message);
        } catch (\Stripe\Exception\RateLimitException $e) {
            // Too many requests made to the API too quickly
            return redirect()->back()->with('error', 'Payment failed: ' . $e->getError()->message);
        } catch (\Stripe\Exception\InvalidRequestException $e) {
            // Invalid parameters were supplied to Stripe's API
            return redirect()->back()->with('error', 'Payment failed: ' . $e->getError()->message);
        } catch (\Stripe\Exception\AuthenticationException $e) {
            // Authentication with Stripe's API failed
            Log::error('Stripe Payment Error: ' . $e->getMessage());
            return redirect()->back();
            // return redirect()->back()->with('error', 'Payment failed: ' . $e->getError()->message);
        } catch (\Stripe\Exception\ApiConnectionException $e) {
            // Network communication with Stripe failed
            return redirect()->back()->with('error', 'Payment failed: ' . $e->getError()->message);
        } catch (\Stripe\Exception\ApiErrorException $e) {
            // Display a very generic error to the user, and maybe send
            // yourself an email
            return redirect()->back()->with('error', 'Payment failed: ' . $e->getError()->message);
        }
    }

    public function getCartToken()
    {
        $cartToken = session()->get('cartToken');
        if ($cartToken) {
            return response()->json([
                'status' => 202,
                'stage' => 'success',
                'message' => 'Got cart token. Redirect to checkout.',
                'data' => $cartToken
            ]);
        }

        return response()->json([
            'status' => 404,
            'stage' => 'failed',
            'message' => 'Session expired. Please add to cart again.'
        ], 404);
    }
}
