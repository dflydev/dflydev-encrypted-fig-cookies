<?php

namespace Dflydev\EncryptedFigCookies\Validation;

class Validation
{
    const DEFAULT_ALGO = 'sha256';
    const NONCE_LENGTH = 32;

    public function __construct($key, $algo = null)
    {
        $this->key = $key;
        $this->algo = $algo ?: static::DEFAULT_ALGO;
    }

    public function extract($value)
    {
        $message = Message::fromString($value);

        if (!$this->verify($message)) {
            throw new \RuntimeException('Invalid message.');
        }

        return $message->getValue();
    }

    public function sign($value)
    {
        $nonce = $this->generateNonce();

        $hmac = hash_hmac($this->algo, $this->key, $nonce.$value);

        return $nonce.$value.'.'.$hmac;
    }

    private function verify(Message $message)
    {
        $calcualtedHmac = hash_hmac(
            $this->algo,
            $this->key,
            $message->getNonce().$message->getValue()
        );

        return self::hashCompare($calcualtedHmac, $message->getHmac());
    }

    private static function generateNonce()
    {
        $result = '';
        for ($i = 0; $i < static::NONCE_LENGTH; $i++) {
            $result .= chr((mt_rand() ^ mt_rand()) % 256);
        }
        return $result;
    }

    private static function hashCompare($hash1, $hash2)
    {
        if (function_exists('hash_equals')) {
            return hash_equals($hash1, $hash2);
        }

        return self::hashCompareFallback($hash1, $hash2);
    }

    private static function hashCompareFallback($hash1, $hash2)
    {
        if (function_exists('hash_equals')) {
            return hash_equals($hash1, $hash2);
        }
        if (strlen($hash1) !== strlen($hash2)) {
            return false;
        }
        $res = 0;
        $len = strlen($hash1);
        for ($i = 0; $i < $len; ++$i) {
            $res |= ord($hash1[$i]) ^ ord($hash2[$i]);
        }
        return $res === 0;
    }
}
