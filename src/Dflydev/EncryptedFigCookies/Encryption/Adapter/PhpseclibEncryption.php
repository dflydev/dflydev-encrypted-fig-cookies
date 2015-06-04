<?php

namespace Dflydev\EncryptedFigCookies\Encryption\Adapter;

use Dflydev\EncryptedFigCookies\Encryption\Encryption;
use Crypt_Base as Cipher;

class PhpseclibEncryption implements Encryption
{
    /**
     * @var Cipher
     */
    private $cypher;

    public function __construct(Cipher $cypher)
    {
        $this->cypher = $cypher;
    }

    public function decrypt($value)
    {
        return $this->cypher->decrypt($value);
    }

    public function encrypt($value)
    {
        return $this->cypher->encrypt($value);
    }
}
