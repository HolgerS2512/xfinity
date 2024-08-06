<?php

namespace App\Models;

final class AesCryptographer
{
    private $method = 'AES-256-CBC';
    private $key;

    public function __construct($key)
    {
        // Schlüssel auf 32 Bytes für AES-256 bringen
        $this->key = substr(hash('sha256', $key, true), 0, 32);
    }

    public function encrypt($data)
    {
        $ivLength = openssl_cipher_iv_length($this->method);
        $iv = openssl_random_pseudo_bytes($ivLength);
        $encrypted = openssl_encrypt($data, $this->method, $this->key, OPENSSL_RAW_DATA, $iv);

        // Kombiniere IV und verschlüsselten Text und kodieren als Base64
        return base64_encode($iv . $encrypted);
    }

    public function decrypt($encryptedData)
    {
        // Base64-dekodieren
        $data = base64_decode($encryptedData);
        $ivLength = openssl_cipher_iv_length($this->method);

        // IV und verschlüsselten Text trennen
        $iv = substr($data, 0, $ivLength);
        $encrypted = substr($data, $ivLength);

        // Entschlüsseln
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
