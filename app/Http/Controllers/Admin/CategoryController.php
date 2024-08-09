<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Repos\CategoryRepositoryController;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreCategoryRequest;
use App\Http\Requests\Admin\UpdateCategoryRequest;
use App\Models\Category;
use App\Models\VersionManager;
use App\Traits\Translation\TranslationMethodsTrait;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    use TranslationMethodsTrait;

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
    private string $versionId = 'd9ikZlc9i4aZvgZC20240809151433';

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function allActive()
    {
        try {
            // Get all categories and subcatgeroies
            // $data = Category::where('active', true)
            //     ->select('id', 'name')
            //     ->with([
            //         'subcategories' => function ($query) {
            //             $query->where('active', true)
            //                 ->select('id', 'name', 'category_id')
            //                 ->with([
            //                     'maincategories' => function ($query) {
            //                         $query->where('active', true)
            //                             ->select('id', 'name', 'subcategory_id');
            //                     }
            //                 ]);
            //         }
            //     ])->get();
            // $data = Category::all();
            // $data = Category::loadActiveCategoriesByLvl();
            $data = Category::loadAllCategoriesByLvl();
            // dd($data);
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
            $ranking = [];
            $categories = Category::where('level', $request->level ?? 1)->get();

            // Add Ranking only request ranking does not exist
            if (!$request->ranking) {
                $resRank = 1;

                // if have datasets
                if ($categories?->count() > 1) {

                    $testRank = 1;

                    // looks for gaps
                    for ($i = 0; $i < $categories->last()->ranking; $i++) {
                        
                        if (isset($categories[$i]) && $categories[$i]?->ranking !== $testRank) {
                            $resRank = $testRank;
                            break;
                        }
                        ++$testRank;
                    }

                    // if no gaps present
                    if ($resRank === 1) {
                        $resRank = $categories?->count() + 1;
                    }
                }

                $ranking = [
                    'ranking' => $resRank,
                ];
            }

            // Request ranking exist in DB, gives present values new ranking
            if ($request->ranking) {
                $exist = false;
                $newRank = (int) $request->ranking;

                foreach ($categories as $cat) {
                    if ($cat?->ranking === (int) $request->ranking) {
                        $exist = true;
                    }
                    if ($exist) {
                        ++$newRank;
                        $cat->update(['ranking' => $newRank]);
                    }
                }
            }

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
