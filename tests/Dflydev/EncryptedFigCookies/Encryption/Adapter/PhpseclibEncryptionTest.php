<?php

namespace Dflydev\EncryptedFigCookies\Encryption\Adapter;

class PhpseclibEncryptionTest extends \PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        if (! class_exists('Crypt_Base')) {
            $this->markTestSkipped('Missing phpseclib/phpseclib');
        }

        parent::setUp();
    }

    /**
     * @test
     * @dataProvider provide_encrypts_and_decrypts_data
     */
    public function it_encrypts_and_decrypts(\Crypt_Base $cipher, $key)
    {
        $cipher->setKey($key);

        $encryption = new PhpseclibEncryption($cipher);

        $size = 10 * 1024;
        $plaintext = str_repeat('a', $size);
        $encrypted = $encryption->encrypt($plaintext);

        $this->assertEquals($plaintext, $encryption->decrypt($encrypted));
    }

    public function provide_encrypts_and_decrypts_data()
    {
        return [
            [
                new \Crypt_AES(CRYPT_AES_MODE_ECB),
                'abcdefghijklmnop'
            ],
        ];
    }
}
