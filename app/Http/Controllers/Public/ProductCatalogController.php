<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

final class ProductCatalogController extends Controller
{
    /**
     * How many products are present in the page numbering.
     * 
     */
    public $perPage = 8;

    /**
     * Contains attributes for products set hidden before response.
     * 
     */
    protected $productHiddenAttr = [
        'active',
        'popular',
        'pivot',
        'updated_at',
        'created_at',
        'deleted_at',
    ];

    /**
     * Display a listing of the resource.
     * 
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $page = 1;
            $sort = 'topseller';

            Cache::flush();
            $entries = Cache::remember("products", 60 * 24, function () {
                $categories = Category::select('id')->active()
                    ->whereDoesntHave('subcategories')
                    ->whereHas('products')
                    ->get();

                $paginatedData = [];

                foreach ($categories as $category) {
                    $paginatedData[$category->id] = $category->products
                        ->limit($this->perPage)
                        // ->with([
                        //     'images' => function ($q) {
                        //         $q->select('id', 'is_primary', 'product_id', 'ranking', 'url', 'ext');
                        //     },
                        // ])
                        ->get()
                        ->makeHidden($this->productHiddenAttr);
                }

                return $paginatedData;
            });

            return response()->json([
                'status' => true,
                'entries' => $entries,
            ], 200);
        } catch (Exception $e) {
            // Exception handling is managed in the custom handler
            throw $e; // Rethrow exception to be caught by the handler
        }
    }

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

                $product->setHidden($this->productHiddenAttr);

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
}
