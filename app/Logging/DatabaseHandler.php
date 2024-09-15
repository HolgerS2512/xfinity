<?php

namespace App\Logging;

use Monolog\Logger;
use Monolog\Handler\AbstractProcessingHandler;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DatabaseHandler extends AbstractProcessingHandler
{
    protected function write(array $record): void
    {
        try {
            DB::table('logs')->insert([
                'level' => $record['level_name'],
                'message' => $record['message'],
                'context' => json_encode($record['context']),
                'created_at' => Carbon::now(),
            ]);
        } catch (\Exception $e) {
            file_put_contents(storage_path('logs/db-debug.log'), 'Error inserting log: ' . $e->getMessage() . "\n", FILE_APPEND);
        }
    }
}

