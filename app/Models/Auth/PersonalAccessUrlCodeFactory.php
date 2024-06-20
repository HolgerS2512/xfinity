<?php

namespace App\Models\Auth;

class PersonalAccessUrlCodeFactory
{
    /**
     * The current access url code for authentication.
     *
     * @var $urlCode
     */
    public $urlCode;

    
    /**
     * Set @var $urlCode.
     *
     * @param $length
     */
    public function __construct($length = 35)
    {
        $chars = ['/', '.', '\\'];
        $array = str_split(base64_encode(random_bytes(rand(33, 999))));

        shuffle($array);
        shuffle($array);

        $result = str_replace($chars, '', password_hash(implode($array), PASSWORD_BCRYPT));

        if ($length > 35) {
            $result = implode([$result, str_replace($chars, '', password_hash(implode($array), PASSWORD_BCRYPT))]);
        }

        $this->urlCode = substr($result, 7, $length);
    }
}