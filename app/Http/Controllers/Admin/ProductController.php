<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreProductRequest;
use App\Http\Requests\Admin\UpdateProductRequest;
use App\Models\Product;
use App\Traits\Middleware\PermissionServiceTrait;
use Exception;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

final class ProductController extends Controller
{
    use PermissionServiceTrait;

    /**
     * The permission name for permissionService.
     *
     * @var string
     */
    private string $permissionName = 'product';

    /**
     * 
     * Applies middleware to check user permissions before allowing access to
     * specific routes. Users without the appropriate permissions will receive
     * a 403 Unauthorized response.
     * 
     */
    public function __construct()
    {
        $this->middleware(function ($request, $next) {

            if ($this->permisssionService($request, $next, $this->permissionName)) {

                return response()->json([
                    'status' => false,
                ], 403);
            }

            return $next($request);
        });
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            Cache::flush();
            $data = Cache::remember("admin_products", 60 * 24, function () {
                return Product::all();
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
     * @param  \App\Http\Requests\Admin\StoreProductRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreProductRequest $request)
    {
        DB::beginTransaction();

        try {
            // Logik
            $status = true;

            // Cache invalid & db saved
            if ($status) {
                DB::commit();

                return response()->json([
                    'status' => $status,
                ], 200);
            }

            DB::rollBack();

            return response()->json([
                'status' => false,
            ], 500);
        } catch (Exception $e) {
            DB::rollBack();
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
            // Logik

            return response()->json([
                'status' => true,
            ], 200);
        } catch (Exception $e) {
            // Exception handling is managed in the custom handler
            throw $e; // Rethrow exception to be caught by the handler
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\Admin\UpdateProductRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateProductRequest $request, $id)
    {
        DB::beginTransaction();

        try {
            // Logik

            $status = true;

            // Cache invalid & db saved
            if ($status) {
                Cache::forget("product_$id");
                DB::commit();

                return response()->json([
                    'status' => $status,
                ], 200);
            }

            DB::rollBack();

            return response()->json([
                'status' => false,
            ], 500);
        } catch (Exception $e) {
            DB::rollBack();
            // Exception handling is managed in the custom handler
            throw $e; // Rethrow exception to be caught by the handler
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        DB::beginTransaction();

        try {
            $product = Product::findOrFail($id);
            $status = $product->delete();

            // Cache invalid & db saved
            if ($status) {
                Cache::forget("product_$id");
                DB::commit();

                return response()->json([
                    'status' => $status,
                ], 200);
            }

            DB::rollBack();

            return response()->json([
                'status' => false,
            ], 500);
        } catch (Exception $e) {
            DB::rollBack();
            // Exception handling is managed in the custom handler
            throw $e; // Rethrow exception to be caught by the handler
        }
    }
}
