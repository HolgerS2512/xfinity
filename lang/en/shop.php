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
$table = DB::table('translations')->select('hash', 'en')->get();

foreach ($table as $val) {
    $item[$val->hash] = $val->en;
}

return $item;