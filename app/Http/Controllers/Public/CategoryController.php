<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

final class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            // Cache::forget("categories");
            // Custom function returned all active categories | cache time 24 h
            $data = Cache::remember("categories", 60 * 24, function () {
                return Category::loadActiveByLvl([
                    'parent_id',
                    'ranking',
                    'active',
                    'level',
                    'popular',
                    'created_at',
                    'updated_at',
                    'deleted_at',
                    'translations',
                    'products',
                ]);
            });

            // Simulate timeout request
            // sleep(11);

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
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {}

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try {
            $category = Category::findOrFail($id);
            $hasSubs = $category->subcategories()->get()->isNotEmpty();

            $data = Cache::remember("category_$id", 60 * 24, function () use ($category, $hasSubs) {
                if ($hasSubs) {
                    return $category->loadActiveChildsHidden([
                        'parent_id',
                        'ranking',
                        'active',
                        'level',
                        'popular',
                        'created_at',
                        'updated_at',
                        'deleted_at',
                        'translations',
                    ]);
                } else {
                    $products = $category->products()->active()->get();

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
                }
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
     * @param  \Illuminate\Http\Request $request
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
