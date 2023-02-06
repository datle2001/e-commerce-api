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
                'photoUrl' => './assets/gummy.png',
                'rating' => 4,
                'quantityInStock' => 4,
                'quantityPick' => 0,
                'code' => '1'
            ],
            [
                'name' => 'Gummy',
                'price' => 10,
                'description' => 'sleep',
                'photoUrl' => './assets/gummy.png',
                'rating' => 4,
                'quantityInStock' => 4,
                'quantityPick' => 0,
                'code' => '1'
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
        return response('it works');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return response('it works');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        return response('it works');
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
        return response('it works');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        return response('it works');
    }
}
