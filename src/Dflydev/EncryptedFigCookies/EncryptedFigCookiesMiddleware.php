<?php

namespace Dflydev\EncryptedFigCookies;

use Dflydev\EncryptedFigCookies\Encryption\Decryptor;
use Dflydev\EncryptedFigCookies\Encryption\Encryption;
use Dflydev\EncryptedFigCookies\Encryption\Encryptor;
use Dflydev\EncryptedFigCookies\Validation\Validation;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class EncryptedFigCookiesMiddleware
{
    private $requestCookieDecryptor;
    private $responseCookieEncryptor;
    private $cookieNames;

    public function __construct(
        RequestCookieDecryptor $requestCookieDecryptor,
        ResponseCookieEncryptor $responseCookieEncryptor,
        $cookieNames
    ) {
        $this->requestCookieDecryptor = $requestCookieDecryptor;
        $this->responseCookieEncryptor = $responseCookieEncryptor;
        $this->cookieNames = $cookieNames;
    }

    /**
     * @param RequestInterface $request
     * @param ResponseInterface $response
     * @param callable $next
     *
     * @return ResponseInterface
     */
    public function __invoke(
        RequestInterface $request,
        ResponseInterface $response,
        callable $next
    ) {
        $request = $this->requestCookieDecryptor->decrypt(
            $request,
            $this->cookieNames
        );

        return $this->responseCookieEncryptor->encrypt(
            $next($request, $response),
            $this->cookieNames
        );
    }

    public static function createWithDecryptorAndEncryptor(
        Decryptor $decryptor,
        Encryptor $encryptor,
        Validation $validation,
        $cookieNames
    ) {
        return new static(
            new RequestCookieDecryptor($decryptor, $validation),
            new ResponseCookieEncryptor($encryptor, $validation),
            $cookieNames
        );
    }

    public static function createWithEncryption(
        Encryption $encryption,
        Validation $validation,
        $cookieNames
    ) {
        return static::createWithDecryptorAndEncryptor(
            $encryption,
            $encryption,
            $validation,
            $cookieNames
        );
    }
}
