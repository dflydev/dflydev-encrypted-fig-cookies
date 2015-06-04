<?php

namespace Dflydev\EncryptedFigCookies\Encryption\Adapter;

use Dflydev\EncryptedFigCookies\Encryption\Encryption;
use Zend\Filter\Decrypt;
use Zend\Filter\Encrypt;

class ZendFilterEncryption implements Encryption
{
    /**
     * @var Decrypt
     */
    private $decrypt;

    /**
     * @var Encrypt
     */
    private $encrypt;

    public function __construct(Decrypt $decrypt, Encrypt $encrypt)
    {
        $this->decrypt = $decrypt;
        $this->encrypt = $encrypt;
    }

    public function decrypt($value)
    {
        return $this->decrypt->filter($value);
    }

    public function encrypt($value)
    {
        return $this->encrypt->filter($value);
    }
}
