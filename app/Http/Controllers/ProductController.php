<?php

namespace App\Http\Controllers;

use App\Services\SharedService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Services\ProductService;

class ProductController extends Controller
{
    public function __construct(
        private ProductService $productService,
        private SharedService $sharedService
    ) {
    }

    public function index(Request $request)
    {
        $response = $this->productService->getProducts(
            $request->query('pageSize'),
            $request->query('pageIndex'),
            $request->query('productIds')
        );

        return $this->sharedService->handleServiceResponse($response);
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id): JsonResponse
    {
        $response = $this->productService->getProduct($id);

        return $this->sharedService->handleServiceResponse($response);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        return response('edit works');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id): JsonResponse
    {
        $response = $this->productService->updateProduct($id, $request->all());

        return $this->sharedService->handleServiceResponse($response);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
    }
}
