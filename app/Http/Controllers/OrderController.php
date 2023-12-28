<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\ProductOrder;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class OrderController extends Controller
{
    /**
     * Get all orders of the authenticated user
     * 
     * @return \Illuminate\Http\Response all history orders
     */
    public function index()
    {
        $user = auth('sanctum')->user();
        $orders = $user->orders;

        return response($orders, 201);
    }

    /**
     * Create an order in DB
     * 
     * @return \Stripe\PaymentLink link to checkout session
     */
    public function store(Request $request)
    {
        $user = auth('sanctum')->user();
        $order = Order::create([
            'user_id' => $user->id,
        ]);

        foreach ($request->all() as $product) {
            $productDB = Product::find($product['id']);

            $productOrderPair = [
                'product_id' => $product["id"],
                'quantity' => $product['quantityPick'],
                'order_id' => $order->id,
                'can_fulfill' => $productDB['quantity_available'] > $product['quantityPick']
            ];
            ProductOrder::create($productOrderPair);

            if ($productOrderPair['can_fulfill']) {
                $productDB->update([
                    'quantity_available' =>
                        $productDB['quantity_available'] - $product['quantityPick']
                ]);
            }
        }

        $origin = $request->header('origin');
        $redirectURL = str_contains($origin, 'github') ? $origin.'/e-commerce-client/' : $request->header('referer');

        return $this->getStripeCheckoutLinkFrom($order->products(), $order->id, $redirectURL);
    }

    public function update(Request $request)
    {

    }

    /**
     * Get an order's details
     *
     * @return \Illuminate\Http\Response all products in an order
     */
    public function show($id)
    {
        $rawOrderedProducts = Order::find($id)->products();
        $orderedProducts = [];

        foreach ($rawOrderedProducts as $index => $rawOrderedProduct) {
            $orderedProduct = [
                'product' => Product::find($rawOrderedProduct['pivot']['product_id']),
                'can_fulfill' => $rawOrderedProduct['can_fulfill'],
                'ordered_quantity' => $rawOrderedProduct['quantity'],
            ];

            $orderedProducts[] = $orderedProduct;
        }

        return response($orderedProducts, 201);
    }

    /**
     * @return \Stripe\PaymentLink link to checkout session
     */
    private function getStripeCheckoutLinkFrom($orderedProducts, $orderId, $originURL)
    {
        $stripe = new \Stripe\StripeClient(
            env('STRIPE_SECRET')
        );

        $lineItems = $this->createLineItemsFrom($orderedProducts, $stripe);

        $paymentLink = $stripe->paymentLinks->create([
            'line_items' => $lineItems,
            'phone_number_collection' => [
                'enabled' => false
            ],
            'submit_type' => 'pay',
            'after_completion' => [
                'type' => 'redirect',
                'redirect' => [
                    'url' => $originURL . "/confirmation/" . $orderId
                ]
            ],
        ]);

        return $paymentLink;
    }

    /**
     * @return array line items for payment link
     */
    private function createLineItemsFrom($orderedProducts, $stripe)
    {
        $lineItems = [];

        foreach ($orderedProducts as $orderedProduct) {
            $productDB = Product::find($orderedProduct['pivot']['product_id']);
            $productStripe = null;

            if ($productDB['stripe_id'] == '') {
                $productStripe = $stripe->products->create([
                    'name' => $productDB['name'],
                ]);

                $productDB->update(['stripe_id' => $productStripe->id]);
            } else {
                $productStripe = $stripe->products->retrieve($productDB['stripe_id']);
            }

            $priceStripe = $stripe->prices->create([
                'unit_amount' => $productDB['price'] * 100,
                'currency' => 'usd',
                'product' => $productStripe->id,
            ]);

            $lineItems[] = [
                'price' => $priceStripe->id,
                'quantity' => $orderedProduct['quantity'],
            ];
        }

        return $lineItems;
    }
}
