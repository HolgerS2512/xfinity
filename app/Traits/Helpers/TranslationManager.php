<?php

namespace App\Traits\Helpers;

trait TranslationManager
{
  /**
   * Create a translation for the category.
   *
   * @param array $data The data for the translation, including 'name' and 'description'.
   * @return bool Returns true if the update was successful, false otherwise.
   */
  public function createTranslation(array $data): bool
  {
    $check = [];

    foreach ($data as $translation) {
      // Use $this->translations() to create translations for the current category instance
      $check[] = $this->translations()->create([
        'locale' => $translation['locale'],
        'name' => $translation['name'],
        'description' => $translation['description'] ?? null,
      ]);
    }

    // Evaluate whether all translations were created successfully
    return !in_array(false, $check, true);
  }

  /**
   * Update or create a translation for a specific field.
   *
   * @param string $locale
   * @param array $data
   * @return bool
   */
  public function updateTranslation(array $data): bool
  {
    $check = [];

    foreach ($data as $translation) {

      // Check if there is an existing translation for this locale
      $currTlModel = $this->translations()->where('locale', $translation['locale'])->first();

      if ($currTlModel) {
        // Check if name exists
        if ($translation['name']) {
          $currTlModel->name = $translation['name'];
        }

        // Check if description exists
        if ($translation['description']) {
          $currTlModel->description = $translation['description'];
        }

        // Update the field with the new value
        $check[] = $currTlModel->save();
      } else {
        // Use $this->translations() to create translations for the current category instance
        $check[] = $this->translations()->create([
          'locale' => $translation['locale'],
          'name' => $translation['name'],
          'description' => $translation['description'] ?? null,
        ]);
      }
    }

    // Evaluate whether all translations were updated successfully
    return !in_array(false, $check, true);
  }
}
