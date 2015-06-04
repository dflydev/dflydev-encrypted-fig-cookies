<?php

namespace Dflydev\EncryptedFigCookies\Validation;

class ValidationTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function it_extracts_successfully()
    {
        $algo = 'sha256';
        $key = 'E26m218TLqgJeY40ydCET10tMUD6qSlV';
        $nonce = 'Z6vsz6UqTtqYcPy4TRinVtb8ShsVvDvq';
        $value = 'hello world!';

        // We know how the hmac should be created.
        $hmac = hash_hmac($algo, $key, $nonce.$value);

        // We know what the signed value should be.
        $signedValue = $nonce.$value.'.'.$hmac;

        $validation = new Validation($key, $algo);

        $extractedValue = $validation->extract($signedValue);

        $this->assertEquals($value, $extractedValue);
    }

    /** @test */
    public function it_signs_successfully()
    {
        $algo = 'sha256';
        $key = 'E26m218TLqgJeY40ydCET10tMUD6qSlV';
        $value = 'hello world!';

        $validation = new Validation($key, $algo);

        $signedValue = $validation->sign($value);

        $extractedValue = $validation->extract($signedValue);

        $this->assertEquals($value, $extractedValue);
    }
}
