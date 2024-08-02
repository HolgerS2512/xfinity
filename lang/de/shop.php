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
$table = DB::table('translations')->select('hash', 'de')->get();

foreach ($table as $val) {
    $item[$val->hash] = $val->de;
}

return $item;