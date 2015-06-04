<?php

namespace Dflydev\EncryptedFigCookies\Encryption\Adapter;

use Zend\Filter\Decrypt;
use Zend\Filter\Encrypt;

class ZendFilterEncryptionTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        if (! class_exists('Zend\Crypt\BlockCipher')) {
            $this->markTestSkipped('Missing zendframework/zend-crypt');
        }

        parent::setUp();
    }

    /** @test */
    public function it_encrypts_and_decrypts_successfully()
    {

        $encryption = new ZendCryptEncryption(
            new Decrypt(['key' => 'asdf']),
            new Encrypt(['key' => 'asdf'])
        );

        $size = 10 * 1024;
        $plaintext = str_repeat('a', $size);
        $encrypted = $encryption->encrypt($plaintext);

        $this->assertEquals($plaintext, $encryption->decrypt($encrypted));
    }

    /** @test */
    public function it_does_not_encrypt_and_decrypt_successfully()
    {
        $encryption = new ZendCryptEncryption(
            new Decrypt(['key' => 'asdf']),
            new Encrypt(['key' => 'ASDF'])
        );

        $size = 10 * 1024;
        $plaintext = str_repeat('a', $size);
        $encrypted = $encryption->encrypt($plaintext);

        $this->assertNotEquals($plaintext, $encryption->decrypt($encrypted));
    }
}
