<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Tax;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

final class CategoryCatalogController extends Controller
{
    /**
     * Contains attributes for categories set hidden before response.
     * 
     * @var int
     */
    protected $tax;

    /**
     * Contains attributes for categories set hidden before response.
     * 
     * @var array
     */
    protected $categoryHiddenAttr = [
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
    ];

    /**
     * Contains attributes for products set hidden before response.
     * 
     * @var array
     */
    protected $productHiddenAttr = [
        'active',
        // 'popular',
        'pivot',
        'manufacturer_id',
        'updated_at',
        'created_at',
        'deleted_at',
    ];

    public function __construct()
    {
        $this->tax = (int) Tax::where('country', 'DE')->first()?->vat;
    }

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
                return Category::loadActiveByLvl($this->categoryHiddenAttr);
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

            Cache::flush();
            $data = Cache::remember("category_$id", 60 * 24, function () use ($category, $hasSubs) {

                if ($hasSubs) {
                    // Load category with child categories.
                    return $category->loadActiveChildsHidden($this->categoryHiddenAttr);
                } else {
                    // Load products with childs.
                    $products = $category->products()->active()
                        ->with(['variants' => function ($q) {
                            $q->select('id', 'is_primary', 'product_id', 'sku', 'color')
                                ->with([
                                    'images' => function ($q) {
                                        $q->select('id', 'is_primary', 'product_variants_id', 'ranking', 'url', 'ext');
                                    },
                                    'prices' => function ($q) {
                                        $q->select('id', 'product_variants_id', 'price', 'locale', 'currency', 'price_type', 'start_date', 'end_date');
                                    },
                                    'inventory' => function ($q) {
                                        $q->select('id', 'product_variants_id', 'size', 'stock');
                                    }
                                ]);
                        }])
                        ->get();

                    // Attaches the primary variant to the main model and delete this variant from collection.
                    $deleteObj = (object) [];

                    $products->each(function ($product) use (&$deleteObj) {

                        $i = 0;
                        $product->variants->each(function ($variant) use ($product, &$deleteObj, &$i) {

                            if ($variant->is_primary) {
                                $product->sku = $product->sku . '-' . $variant->sku;
                                $product->images = $variant->images;
                                $product->fullPrice = number_format($variant->fullPrice() * (1 + $this->tax / 100), 2);
                                $product->currentPrice = number_format($variant->currentPrice() * (1 + $this->tax / 100), 2);
                                $product->currency = $variant->fullPriceCurrency();
                                $product->color = $variant->color;
                                $product->inventory = $variant->inventory->setHidden(['id', 'product_variants_id', 'ranking']);

                                $deleteObj->key = $i;
                                $deleteObj->productId = $product->id;
                            }

                            $variant->fullPrice = number_format($variant->fullPrice() * (1 + $this->tax / 100), 2);
                            $variant->currentPrice = number_format($variant->currentPrice() * (1 + $this->tax / 100), 2);
                            $variant->currency = $variant->fullPriceCurrency();
                            $variant->inventory->setHidden(['id', 'product_variants_id', 'ranking']);
                            unset($variant->prices);
                            ++$i;
                        });

                        // Set attributes hidden which not need.
                        $product->setHidden($this->productHiddenAttr);
                    });

                    // Delete superfluous variant (primary variant)
                    if (isset($deleteObj->productId)) {
                        $products->find($deleteObj->productId)->variants->forget($deleteObj->key);
                    }

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
}
