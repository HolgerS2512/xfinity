<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

final class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            Cache::flush();
            $data = Cache::remember("products", 60 * 24, function () {
                $products = Product::active()->get();

                $products->each(function ($product) {
                    $product->setHidden([
                        'active',
                        'popular',
                        'pivot',
                        'updated_at',
                        'created_at',
                        'deleted_at',
                    ]);
                });
                
                return $products;
            });

            return response()->json([
                'status' => true,
                'data' => $data,
            ], 200);
        } catch (Exception $e) {
            // Exception handling is managed in the custom handler
            throw $e; // Rethrow exception to be caught by the handler
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request){}

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try {
            Cache::flush();
            $data = Cache::remember("product_$id", 60 * 24, function () use ($id) {
                $product = Product::findOrFail($id);

                $product->setHidden([
                    'active',
                    'popular',
                    'pivot',
                    'updated_at',
                    'created_at',
                    'deleted_at',
                ]);

                return $product;
            });

            return response()->json([
                'status' => true,
                'data' => $data,
            ], 200);
        } catch (Exception $e) {
            // Exception handling is managed in the custom handler
            throw $e; // Rethrow exception to be caught by the handler
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) {}

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {}
}
