<?php

namespace Dflydev\EncryptedFigCookies\Validation;

class Message
{
    private $nonce;
    private $hmac;
    private $value;

    private function __construct($nonce, $hmac, $value)
    {
        $this->nonce = $nonce;
        $this->hmac = $hmac;
        $this->value = $value;
    }

    public function getNonce()
    {
        return $this->nonce;
    }

    public function getHmac()
    {
        return $this->hmac;
    }

    public function getValue()
    {
        return $this->value;
    }

    public static function fromString($value)
    {
        $nonce = substr($value, 0, 32);
        $hmac = substr($value, strrpos($value, '.') + 1);
        $value = substr($value, 32, strlen($value) - strlen($hmac) - 33);

        return new self($nonce, $hmac, $value);
    }
}
