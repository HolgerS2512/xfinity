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
$table = DB::table('translations')->select('id', 'de')->get();

foreach ($table as $val) {
    $item[$val->id] = $val->de;
}

return $item;