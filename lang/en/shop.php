<?php

use Illuminate\Support\Facades\DB;

/*
|--------------------------------------------------------------------------
| Shop Language Lines
|--------------------------------------------------------------------------
|
|
*/

$item = [];
$table = DB::table('translations')->select('id', 'en')->get();

foreach ($table as $val) {
    $item[$val->id] = $val->en;
}

return $item;