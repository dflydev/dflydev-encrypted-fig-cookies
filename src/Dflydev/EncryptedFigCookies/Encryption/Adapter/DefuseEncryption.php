<?php

namespace Dflydev\EncryptedFigCookies\Encryption\Adapter;

use Crypto;
use Dflydev\EncryptedFigCookies\Encryption\Encryption;

class DefuseEncryption implements Encryption
{
    /**
     * @var string
     */
    private $key;

    /**
     * @param string $key
     */
    public function __construct($key)
    {
        $this->key = $key;
    }

    public function decrypt($value)
    {
        return Crypto::Decrypt($value, $this->key);
    }

    public function encrypt($value)
    {
        return Crypto::Encrypt($value, $this->key);
    }
}
