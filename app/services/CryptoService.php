<?php

namespace App\services;

use Random\RandomException;

class CryptoService
{
    /**
     * Create a new class instance.
     */
    private $method = 'aes-256-gcm';

    private $key;
    public function __construct()
    {
        $this->key = env('WORKING_KEY');
    }

    /**
     * @throws RandomException
     */
    public function encrypt($plainText)
    {
        $initVector =  random_bytes(16);
        $cipherText = openssl_encrypt($plainText, $this->method, $this->key, OPENSSL_RAW_DATA, $initVector, $tag);
        return bin2hex($initVector) . bin2hex($cipherText . $tag);
    }

    public function decrypt($encryptedText)
    {
        $encryptedText = hex2bin($encryptedText);
        $iv_len = $tag_length = 16;
        $iv = substr($encryptedText, 0, $iv_len);
        $tag = substr($encryptedText, -$tag_length);
        $ciphertext = substr($encryptedText, $iv_len, -$tag_length);
        return openssl_decrypt($ciphertext, $this->method, $this->key, OPENSSL_RAW_DATA, $iv, $tag);
    }
}
