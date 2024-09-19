<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreCategoryRequest;
use App\Http\Requests\Admin\UpdateCategoryRequest;
use App\Models\Category;
use App\Models\CategoryTranslation;
use App\Models\User;
use App\Traits\Middleware\PermissionServiceTrait;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

final class CategoryController extends Controller
{
    use PermissionServiceTrait;

    /**
     * The permission name for permissionService.
     *
     * @var string
     */
    private string $permissionName = 'category';

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

            // Permission
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
            $data = Category::loadAllCategoriesByLvl();

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
     * @param  \App\Http\Requests\Admin\StoreCategoryRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreCategoryRequest $request)
    {
        DB::beginTransaction();

        try {
            $ranking = [];
            $level = [];
            $levelCheck = $request->level ?? 1;

            // Check if have level but no parent id
            if ($request->has('level') && !$request->has('parent_id')) {
                DB::rollBack();

                return response()->json([
                    'status' => false,
                ], 400);
            }

            // Add level if have parent category
            if ($request->has('parent_id')) {
                $parent = Category::findOrFail($request->input('parent_id'))->only('level');
                $parentLvl = $parent['level'] + 1;

                $level['level'] = $parentLvl;

                // Check if level exist 
                if (!$request->has('level')) {
                    $levelCheck = $parent['level'] + 1;
                }
            }


            $categories = Category::where('level', $levelCheck)->orderBy('ranking')->get();

            $resRank = 1;
            // Add Ranking only request ranking does not exist
            if (!$request->has('ranking')) {

                // if have datasets
                if ($categories?->count() >= 1) {

                    $testRank = 1;

                    // looks for gaps and korrection
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
            }

            // Request ranking exist in DB, gives present values new ranking
            if ($request->has('ranking') && $categories->isNotEmpty()) {

                $exist = false;
                $newRank = (int) $request->ranking;
                // Check if new ranking bigger then all catgeories
                $resRank = $newRank >= $categories->count() ? $categories->count() + 1 : $newRank;

                // looks for gaps and korrection
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

            $ranking = [
                'ranking' => $resRank,
            ];

            // Save category with validated data and dynamic ranking
            $category = Category::create(array_merge($request->validated(), $ranking, $level));

            if (isset($category->id) && !empty($category->id)) {

                // Get translations and save it
                $translations = $request->input('translations', []);
                $status = $category->createTranslation($translations);

                // Cache invalid & db save
                if ($status) {
                    Cache::forget("categories");
                    DB::commit();

                    return response()->json([
                        'status' => true,
                    ], 201);
                }
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
            $data = Category::loadAllChildsById($id, []);

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
     * @param  \App\Http\Requests\Admin\UpdateCategoryRequest $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateCategoryRequest $request, $id)
    {
        DB::beginTransaction();

        try {
            // Find the category instance or throw a 404 error
            $category = Category::findOrFail($id);

            // Update the category's name if provided
            if ($request->filled('translations')) {
                // Update the translation
                // Assuming you have a method to handle translations
                $translations = $request->input('translations', []);
                $status = $category->updateTranslation($translations);

                if (!$status) {
                    Log::warning('Translation update failed', ['request_all' => $request->all()]);

                    return response()->json([
                        'status' => false,
                    ], 500);
                }
            }

            // Update rankings for other categories if new ranking is provided
            if ($request->filled('new_ranking')) {
                $newRanking = (int) $request->input('new_ranking');
                $i = 1;

                Category::where('id', '!=', $id)
                    ->get()
                    ->each(function ($cate) use (&$newRanking, &$i) {
                        if ($i === $newRanking) ++$i;
                        $cate->update([
                            'ranking' => $i,
                        ]);
                        ++$i;
                    });
            }

            // Prepare and update category values
            $values = $request->validated();

            if ($request->filled('new_ranking')) {
                $values['ranking'] = (int) $request->input('new_ranking');
            }

            // Remove unnecessary fields from the update array
            unset($values['new_ranking'], $values['name'], $values['description']);

            // Update the category (dont delete Carbon! Should only translation become not an update)
            $status = $category->update(array_merge($values, ['updated_at' => Carbon::now()]));

            if ($status) {
                // Clear the cache
                Cache::forget("categories");
                Cache::forget("category_$id");
                DB::commit();

                return response()->json([
                    'status' => true,
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
            $category = Category::findOrFail($id);

            $status = $category->delete();

            $statusTranslation = CategoryTranslation::where('category_id', $id)->delete();

            // Cache invalid & db saved
            if ($status && $statusTranslation) {
                Cache::forget("categories");
                Cache::forget("category_$id");
                DB::commit();

                return response()->json([
                    'status' => true,
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
