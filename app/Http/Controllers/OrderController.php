<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\ProductOrder;
use App\Repositories\OrderRepository;
use App\Services\OrderService;
use App\Services\SharedService;
use App\Services\StripeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class OrderController extends Controller
{
    public function __construct(private OrderService $orderService, private SharedService $sharedService, private StripeService $stripeService, private OrderRepository $orderRepository)
    {
    }

    /**
     * Get all orders of the authenticated user
     * 
     * @return \Illuminate\Http\Response all history orders
     */
    public function index()
    {
    }

    public function store(Request $request)
    {
        $orderId = $this->orderService->createOrder($request->user()->id, $request->all());

        $response = $this->stripeService->getStripeCheckoutLinkFrom(
            $orderId,
            $request->header('origin'),
            $request->header('referer')
        );

        return $this->sharedService->handleServiceResponse($response);
    }

    public function update(Request $request)
    {
    }

    public function show($id): JsonResponse
    {
        $response = $this->orderService->getOrder($id);

        return $this->sharedService->handleServiceResponse($response);
    }
}
