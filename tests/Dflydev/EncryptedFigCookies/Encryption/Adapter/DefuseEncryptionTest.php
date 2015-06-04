<?php

namespace Dflydev\EncryptedFigCookies\Encryption\Adapter;

class DefuseEncryptionTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        if (! class_exists('Crypto')) {
            $this->markTestSkipped('Missing defuse/php-encryption');
        }

        parent::setUp();
    }

    /** @test */
    public function it_encrypts_and_decrypts()
    {
        $encryption = new DefuseEncryption('asdfasdfasdfasdf');

        $size = 10 * 1024;
        $plaintext = str_repeat('a', $size);
        $encrypted = $encryption->encrypt($plaintext);

        $this->assertEquals($plaintext, $encryption->decrypt($encrypted));
    }
}
