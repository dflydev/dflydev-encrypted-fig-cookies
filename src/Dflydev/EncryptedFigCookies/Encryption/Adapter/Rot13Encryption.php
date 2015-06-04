<?php

namespace Dflydev\EncryptedFigCookies\Encryption\Adapter;

use Dflydev\EncryptedFigCookies\Encryption\Encryption;

class Rot13Encryption implements Encryption
{
    public function decrypt($value)
    {
        return str_rot13($value);
    }

    public function encrypt($value)
    {
        return str_rot13($value);
    }
}
