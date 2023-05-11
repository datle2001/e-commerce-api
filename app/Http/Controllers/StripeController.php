<?php

namespace App\Http\Controllers;
use App\Models\Product;
use Illuminate\Http\Request;
use Stripe;

/**
 * Summary of StripeController
 */
class StripeController extends Controller
{
    public function checkout(Request $request)
    {
        try {
            $stripe = new \Stripe\StripeClient(env('STRIPE_SECRET'));
            $user = auth('sanctum')->user();
            $pms = $stripe->customers->allPaymentMethods(
                $user->stripe_id,
                ['type' => 'card']
            );

            if(sizeof($pms) > 0) {
                $stripe->paymentIntents->create(
                    ['amount' => $this->getTotal($request),
                    'currency' => 'usd', 
                    'payment_method_types' => ['card'],
                    'confirm' => true,
                    'customer' => $user->stripe_id,
                    'payment_method' => $pms['data']['0']->id
                    ]
                );
            }
            
            $order = auth('sanctum')->user()->orders()->create();
            foreach($request->all() as $product) {
                $prod = Product::find($product['id']);
                $prod->update(
                    array(
                        'quantity_available' => $prod['quantity_available'] - $product['quantity_purchase']
                    )
                );
                $order->products()->attach($product['id'], [
                    'quantity' => $product['quantity_purchase']
                ]);
            }
            
            return response(['message' => 'Checkout success'], 201);
            
        } catch (\Throwable $th) {
            return $th;
        }
    }

    /**
     * @param Request $request
     * 
     * @return \Illuminate\Http\Response 
     */
    public function addCard(Request $request) 
    {
        //get user with same token and card info
        $user = auth('sanctum')->user();
        $effDate = $request->input('effDate');
        $card = [
            'number' => $request->input('cardNumber'),
            'exp_month' => intval(substr($effDate, 0, 2)),
            'exp_year' => intval(substr($effDate, 3, 4)),
            'cvc' => $request->input('cvc'),
        ];

        //create a card on stripe
        $stripe = new \Stripe\StripeClient(env('STRIPE_SECRET'));
        $paymentMethod = $stripe->paymentMethods->create([
            'type' => 'card',
            'card' => $card,                
        ]);
        $stripe->paymentMethods->attach(
            $paymentMethod['id'],
            ['customer' => $user['stripe_id']]
        );
        return response(['card' => $card], 201); 
    }
    private function getTotal(Request $request) {
        $total = 0;
        for($i = 0; $request[$i] != null; $i++) {
            $total += $request[$i]['price']*$request[$i]['quantity_purchase'];
        }

        return $total*100;
    }
}
