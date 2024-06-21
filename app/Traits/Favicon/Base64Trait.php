<?php

namespace App\Traits\Favicon;

trait Base64Trait
{
  /**
   * Returned the logo as base64 string.
   *
   * @return string
   */
  public static function getEmailLogo()
  {
    return 'data:image/' . 'png' . ';base64,' . base64_encode(file_get_contents(public_path('favicons/logo.png')));
  }
}
