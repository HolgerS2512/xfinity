<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\DB;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * TableB only keeps entries that match the entries in tableA.
     *
     * @param  string $tableA, $tableB, $tBuniqueColumn
     * 
     * @return void
     */
    public static function syncTableUniqueExcess($tableA, $tableB, $tBuniqueColumn = 'hash')
    {
        // Get the unique values ​​from TableA
        $tableAUniqueValues = DB::table($tableA['table'])->pluck($tableA['column']);

        // Get all entries from TableB
        $tableBEntries = DB::table($tableB['table'])->get();

        // Filter out the excess entries in Table
        $excessEntries = $tableBEntries->filter(function ($entry) use ($tableAUniqueValues, $tBuniqueColumn) {
            return !$tableAUniqueValues->contains($entry->{$tBuniqueColumn});
        });

        // Delete the excess entries from TableB
        DB::table($tableB['table'])->whereIn($tableB['column'], $excessEntries->pluck($tableB['column']))->delete();
    }
}

            // Delete excess Tupels
            // self::syncTableUniqueExcess([
            //     'table' => 'categories',
            //     'column' => 'name'
            // ], [
            //     'table' => 'translations',
            //     'column' => 'hash'
            // ]);