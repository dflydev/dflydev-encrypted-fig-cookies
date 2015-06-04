<?php

namespace Dflydev\EncryptedFigCookies;

use Dflydev\EncryptedFigCookies\Encryption\Decryptor;
use Dflydev\EncryptedFigCookies\Encryption\Encryption;
use Dflydev\EncryptedFigCookies\Encryption\Encryptor;
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
        $cookieNames
    ) {
        return new static(
            new RequestCookieDecryptor($decryptor),
            new ResponseCookieEncryptor($encryptor),
            $cookieNames
        );
    }

    public static function createWithEncryption(Encryption $encryption, $cookieNames)
    {
        return static::createWithDecryptorAndEncryptor(
            $encryption,
            $encryption,
            $cookieNames
        );
    }
}
