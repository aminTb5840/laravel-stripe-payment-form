<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\Charge;

class PaymentController extends Controller
{
    public function showPaymentForm()
    {
        return view('payment',['debt'=>rand(10,1000)]);
    }

    public function processPayment(Request $request)
    {
        Stripe::setApiKey(config('services.stripe.secret'));

        try {
            $charge = Charge::create([
                'amount' => 1000,
                'currency' => 'usd',
                'source' => $request->stripeToken,
                'description' => 'test payment',
            ]);

            return back()->with('success', 'payment was successful');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
