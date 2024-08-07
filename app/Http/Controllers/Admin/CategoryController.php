<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Repos\CategoryRepositoryController;
use App\Http\Requests\Admin\StoreCategoryRequest;
use App\Http\Requests\Admin\UpdateCategoryRequest;
use App\Models\Category;
use App\Models\Translation;
use App\Models\VersionManager;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;

final class CategoryController extends CategoryRepositoryController
{
    /**
     * The name of the custom authentication cookie used in the application.
     *
     * @var string
     */
    private string $cookieName = 'L_CD';

    /**
     * The "id" of the custom id cookie.
     *
     * @var string
     */
    private string $versionId = 'QMwMbD9y2Brej92G20240805192529';

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function allActive()
    {
        try {
            // Get all categories and subcatgeroies
            $data = Category::where('active', true)
                ->select('id', 'name')
                ->with([
                    'subcategories' => function ($query) {
                        $query->where('active', true)
                            ->select('id', 'name', 'category_id')
                            ->with([
                                'maincategories' => function ($query) {
                                    $query->where('active', true)
                                        ->select('id', 'name', 'subcategory_id');
                                }
                            ]);
                    }
                ])->get();

            // Get the versions hash
            $vm = VersionManager::findOrFail($this->versionId);

            // Set cookie for frontend hash (30 Days)
            $cookie = Cookie::make(
                $this->cookieName,
                $vm->hash,
                (60 * 24 * 30),
                '/',
                str_replace('www.', '', substr(URL::to('/'), strpos(URL::to('/'), '://') + 3)),
                false,
                false,
            );

            return response()->json([
                'status' => true,
                'data' => $data,
            ], 200)->cookie($cookie);
        } catch (Exception $e) {

            return response()->json([
                'status' => false,
                'message' => __('error.500'),
            ], 500);
        }
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $data = Category::with('subcategories')->get();

            return response()->json([
                'status' => true,
                'data' => $data,
            ], 200);
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
     * @param  \Illuminate\Http\StoreCategoryRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreCategoryRequest $request)
    {
        try {
            // Add Ranking
            $ranking = ['ranking' => Category::all()->count() + 1];

            // Save Category
            $category = Category::create(array_merge($ranking, $request->all()));

            // Is save successfully
            $status = isset($category->id) && !empty($category->id);

            return response()->json([
                'status' => $status,
            ], 200);
        } catch (Exception $e) {

            return response()->json([
                'status' => false,
                'message' => __('error.500'),
            ], 500);
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
            $data = Category::findOrFail($id);

            return response()->json([
                'status' => true,
                'data' => $data,
            ], 200);
        } catch (Exception $e) {

            return response()->json([
                'status' => false,
                'message' => __('error.500'),
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\UpdateCategoryRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateCategoryRequest $request, $id)
    {
        try {
            // Find this instance 
            $category = Category::findOrFail($id);

            // Update Translation
            if ($request->name) {
                $categoryDB = DB::table('categories')->where('id', $id)->first();
                $statusName = self::updateTranslation($categoryDB->name, $request->name);
            }

            // Update ranking other categories
            if ($request->new_ranking) {

                $allCategories = Category::whereNot('id', $id)->get();
                $i = 1;
                foreach ($allCategories as $cate) {
                    if ($i === (int) $request->new_ranking) ++$i;
                    $cate->update([
                        'ranking' => $i,
                        'updated_at' => Carbon::now(),
                    ]);
                    ++$i;
                }
            }

            // Preparation request values
            $values = $request->all();

            if ($request->new_ranking) {
                $values['ranking'] = $values['new_ranking'];
            }

            // Deletes unnecessary vars
            unset($values['new_ranking'], $values['name']);

            // Update Category
            $status = $category->update(array_merge(['updated_at' => Carbon::now()], $values));

            if ($request->name) {
                return response()->json([
                    'status' => $status && $statusName,
                    'message' => ($status && $statusName ? '' : __('error.500')),
                ], ($status && $statusName ? 200 : 500));
            }

            return response()->json([
                'status' => $status,
                'message' => ($status ? '' : __('error.500')),
            ], ($status ? 200 : 500));
        } catch (Exception $e) {

            return response()->json([
                'status' => false,
                'message' => __('error.500'),
            ], 500);
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
        try {
            $status = Category::findOrFail($id);
            $status->delete();

            return response()->json([
                'status' => $status,
            ], 200);
        } catch (Exception $e) {

            return response()->json([
                'status' => false,
                'message' => __('error.500'),
            ], 500);
        }
    }
}