<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreSubcategoryRequest;
use App\Http\Requests\Admin\UpdateSubcategoryRequest;
use App\Models\Subcategory;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;

class SubcategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\StoreSubcategoryRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreSubcategoryRequest $request)
    {
        try {
            // Add Ranking
            $ranking = ['ranking' => Subcategory::where('category_id', $request->category_id)->get()->count() + 1];

            // Save Category
            $subcategory = Subcategory::create(array_merge($ranking, $request->all()));

            // Is save successfully
            $status = isset($subcategory->id) && !empty($subcategory->id);

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
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\UpdateSubcategoryRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateSubcategoryRequest $request, $id)
    {
        // Find this instance 
        $subcat = Subcategory::findOrFail($id);

        // Update ranking other subcategories
        $allSubcategories = Subcategory::whereNot('id', $id)->get();
        $i = 1;
        foreach ($allSubcategories as $cate) {
            if ($i === (int) $request->new_ranking) ++$i;
            $cate->update([
                'ranking' => $i,
                'updated_at' => Carbon::now(),
            ]);
            ++$i;
        }

        // Preparation request values
        $values = $request->all();
        $values['ranking'] = $values['new_ranking'];
        unset($values['new_ranking']);

        // Update Category
        $status = $subcat->update(array_merge(['updated_at' => Carbon::now()], $values));

        // Delete excess Tupels
        self::syncTableUniqueExcess([
            'table' => 'subcategories',
            'column' => 'name'
        ], [
            'table' => 'translations',
            'column' => 'hash'
        ]);

        return response()->json([
            'status' => $status,
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
