<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Session;
use Stripe;

class StripePaymentController extends Controller
{
    public function stripe() {
        return view('stripe');
    }

    public function stripePost(Request $request)
    {
        try {
            Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));
            $charge = Stripe\Charge::create([
                'amount' => $this->getTotal($request),
                'currency' => 'usd',
                'customer' => 'cus_NPeoR5Dq71TTyQ'
            ]);

            return $charge;
            
        } catch (\Throwable $th) {
            return $th;
        }
    }

    private function getTotal(Request $request) {
        $total = 0;
        for($i = 0; $request[$i] != null; $i++) {
            $total += $request[$i]['price']*$request[$i]['quantityPick'];
        }

        return $total*100;
    }
}
