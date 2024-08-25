<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\User;
use App\Models\VersionManager;
use App\Traits\Middleware\PermissionServiceTrait;
use App\Traits\Translation\TranslationMethodsTrait;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;

class ProductController extends Controller
{
    use PermissionServiceTrait, TranslationMethodsTrait;

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

            // Exclude routes
            // if ($request->routeIs('all_active_categories')) {
            //     return $next($request);
            // }

            $this->permisssionService($request, $next, $this->permissionName);
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
            // Custom function returned all active categories | cache time 24 h
            // $data = Cache::remember("categories", 60 * 24, function () {
            //     // return Category::loadActiveCategoriesByLvl();
            // });

            // // Get the versions hash
            // $vm = VersionManager::findOrFail($this->versionId);

            // // Set cookie for frontend hash (30 Days)
            // $cookie = Cookie::make(
            //     $this->cookieName,
            //     $vm->hash,
            //     (60 * 24 * 30),
            //     '/',
            //     str_replace('www.', '', substr(URL::to('/'), strpos(URL::to('/'), '://') + 3)),
            //     false,
            //     false,
            //     false,
            //     'none',
            // );

            // return response()->json([
            //     'status' => true,
            //     'data' => $data,
            // ], 200)->cookie($cookie);
        } catch (Exception $e) {

            return response()->json([
                'status' => false,
                'message' => __('error.500'),
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
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
        //
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
                Cache::forget("product_{$id}");
                DB::commit();
            }

            return response()->json([
                'status' => $status,
            ], 200);
        } catch (Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => false,
                'message' => __('error.500'),
            ], 500);
        }
    }
}
