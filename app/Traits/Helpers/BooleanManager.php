<?php

namespace App\Traits\Helpers;

trait BooleanManager
{
  /**
   * Returns a Boolean value by evaluating an array filled with Boolean values ​​using the AND condition.
   *
   * @param array $booleans
   * @return bool
   */
  public function evaluateBoolByAnd(array $booleans): bool
  {
    return !in_array(false, $booleans, true);
  }

  /**
   * Returns a Boolean value by evaluating an array filled with Boolean values ​​using the OR condition.
   *
   * @param array $booleans
   * @return bool
   */
  public function evaluateBoolByOr(array $booleans): bool
  {
    foreach ($booleans as $bool) {
      if ($bool) {
        return true;
      }
    }
    return false;
  }

  /**
   * Returns a Boolean value by evaluating an array filled with Boolean values ​​if exactly ONE value is true.
   *
   * @param array $booleans
   * @return bool
   */
  public function evaluateBoolByOne(array $booleans): bool
  {
    $trueCount = 0;
    foreach ($booleans as $bool) {
      if ($bool) {
        $trueCount++;
      }
    }
    return $trueCount === 1;
  }
}
