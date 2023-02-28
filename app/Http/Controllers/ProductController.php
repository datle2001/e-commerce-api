<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
        $products = [
            [
                'name' => 'Gummy',
                'price' => 10,
                'description' => 'sleep',
                'photoUrl' => './assets/products/gummy.png',
                'rating' => 4,
                'quantityInStock' => 4,
                'code' => '1'
            ],
            [
                'name' => 'Shampoo',
                'price' => 20,
                'description' => 'wash',
                'photoUrl' => './assets/products/shampoo.jpg',
                'rating' => 3,
                'quantityInStock' => 5,
                'code' => '2'
            ],
            [
                'name' => 'Twist Tubes',
                'price' => 15,
                'description' => 'health',
                'photoUrl' => './assets/products/twist_tubes.png',
                'rating' => 4.5,
                'quantityInStock' => 10,
                'code' => '3'
            ],
            [
                'name' => 'Vitamin B',
                'price' => 17,
                'description' => 'health',
                'photoUrl' => './assets/products/vitaminB.png',
                'rating' => 4.6,
                'quantityInStock' => 4,
                'code' => '4'
            ],
            [
                'name' => 'Vitamin C',
                'price' => 23,
                'description' => 'health',
                'photoUrl' => './assets/products/vitaminC.png',
                'rating' => 4.8,
                'quantityInStock' => 9,
                'code' => '5'
            ]
        ];
        return response()->json($products, 200);
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        return response('store works');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return response('show works');
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
    public function update(Request $request, $id)
    {
        return response('update works');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        return response('destroy works');
    }
}
