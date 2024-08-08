<?php

namespace App\Models;

final class AesCryptographer
{
    private $method = 'AES-256-CBC';
    private $key;

    public function __construct($key)
    {
        $this->key = substr(hash('sha256', $key, true), 0, 32);
    }

    public function encrypt($data)
    {
        $ivLength = openssl_cipher_iv_length($this->method);
        $iv = openssl_random_pseudo_bytes($ivLength);
        $encrypted = openssl_encrypt($data, $this->method, $this->key, OPENSSL_RAW_DATA, $iv);
        return base64_encode($iv . $encrypted);
    }

    public function decrypt($encryptedData)
    {
        $data = base64_decode($encryptedData);
        $ivLength = openssl_cipher_iv_length($this->method);
        $iv = substr($data, 0, $ivLength);
        $encrypted = substr($data, $ivLength);
        return openssl_decrypt($encrypted, $this->method, $this->key, OPENSSL_RAW_DATA, $iv);
    }
}

// EXAMPLE
// $key = 'your-32-char-key-for-encryption!';
// $data = 'Sensitive Data';

// $cryptographer = new AesCryptographer($key);

// $encrypted = $cryptographer->encrypt($data);
// $decrypted = $cryptographer->decrypt($encrypted);

// echo 'Encrypted: ' . $encrypted . "\n";
// echo 'Decrypted: ' . $decrypted . "\n";
