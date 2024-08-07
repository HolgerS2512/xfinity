<?php

namespace App\Http\Controllers\Admin\Repos;

use App\Http\Controllers\Controller;
use App\Models\Translation;

class CategoryRepositoryController extends Controller
{
    /**
     * Update the translation in the Translation model.
     * 
     * @param string $hash
     * @param string $de
     * @return bool
     * @throws \Exception
     */
    public static function updateTranslation($hash, $de)
    {
        try {
            // Find the translation record by hash
            $translate = Translation::where('hash', $hash)->first();
            // Check if the translation exists
            if (!$translate) {
                throw new \Exception("Translation not found for hash: $hash");
            }
    
            // Update the translation record
            $status = $translate->update([
                'de' => $de,
            ]);
    
            // Return whether the update was successful
            return $status;
        } catch (\Throwable $th) {
            // Log the exception or handle it as needed
            throw $th;
        }
    }
}
