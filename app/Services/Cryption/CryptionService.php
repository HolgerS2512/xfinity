<?php

namespace App\Services\Cryption;

use App\Models\AesCryptographer;

final class CryptionService
{
    /**
     * The cryptographer instance used for encryption and decryption.
     *
     * @var \App\Models\AesCryptographer
     */
    private $cryptographer;

    /**
     * Create a new instance of the class.
     *
     * @param \App\Models\AesCryptographer $cryptographer
     */
    public function __construct()
    {
        $this->setAttributes();
    }

    /**
     * Initialize the cryptographer with the encryption password from the application configuration.
     *
     * @param \App\Models\AesCryptographer $cryptographer
     */
    private function setAttributes()
    {
        $this->cryptographer = new AesCryptographer(config('app.encryption_password'));
    }

    /**
     * Encrypt the given data.
     *
     * @param mixed $data The data to be encrypted.
     * @return string The encrypted data.
     */
    public function encrypt($data)
    {
        return $this->cryptographer->encrypt(json_encode($data));
    }

    /**
     * Decrypt the given encrypted string.
     *
     * @param string $encryptedData The encrypted data.
     * @return mixed The decrypted data.
     */
    public function decrypt($encryptedData)
    {
        return json_decode($this->cryptographer->decrypt($encryptedData), true);
    }

    public function __clone() {}
}
