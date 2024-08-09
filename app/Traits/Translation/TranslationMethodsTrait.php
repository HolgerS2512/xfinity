<?php

namespace App\Traits\Translation;

use App\Models\TextTranslation;
use App\Models\Translation;

trait TranslationMethodsTrait
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
            // Find the translation record by id as hash
            $translate = Translation::where('id', $hash)->first();
            // Check if the translation exists
            if (!$translate) {
                throw new \Exception("Translation not found for id: $hash");
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

    /**
     * Update the translation in the Translation model.
     * 
     * @param string $hash
     * @param string $de
     * @return bool
     * @throws \Exception
     */
    public static function updateTextTranslation($hash, $de)
    {
        try {
            // Find the translation record by id as hash
            $translate = TextTranslation::where('id', $hash)->first();
            // Check if the translation exists
            if (!$translate) {
                throw new \Exception("Translation not found for id: $hash");
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
