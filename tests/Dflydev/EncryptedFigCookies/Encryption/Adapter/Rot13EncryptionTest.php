<?php

namespace Dflydev\EncryptedFigCookies\Encryption\Adapter;

class Rot13EncryptionTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function it_encrypts_and_decrypts()
    {
        $encryption = new Rot13Encryption();

        $plaintext = 'ENCRYPTED';
        $encrypted = $encryption->encrypt($plaintext);

        $this->assertEquals('RAPELCGRQ', $encrypted);
        $this->assertEquals('ENCRYPTED', $encryption->decrypt($encrypted));
    }
}
