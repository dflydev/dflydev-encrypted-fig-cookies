<?php

namespace Dflydev\EncryptedFigCookies;

use Dflydev\EncryptedFigCookies\Encryption\Decryptor;
use Dflydev\EncryptedFigCookies\Validation\Validation;
use Dflydev\FigCookies\Cookies;
use Psr\Http\Message\RequestInterface;

class RequestCookieDecryptor
{
    /**
     * @var Decryptor
     */
    private $decryptor;

    /**
     * @var Validation
     */
    private $validation;

    public function __construct(Decryptor $decryptor, Validation $validaton)
    {
        $this->decryptor = $decryptor;
        $this->validation = $validaton;
    }

    private static function resolveCookieNames($cookieNames)
    {
        return is_array($cookieNames) ? $cookieNames : [(string) $cookieNames];
    }

    private static function hasNoCookieNames(array $cookieNames)
    {
        return count($cookieNames) < 1;
    }

    public function decrypt(RequestInterface $request, $cookieNames)
    {
        $cookieNames = self::resolveCookieNames($cookieNames);

        if (self::hasNoCookieNames($cookieNames)) {
            return $request;
        }

        $cookies = Cookies::fromRequest($request);

        foreach ($cookieNames as $cookieName) {
            $cookies = $this->decryptCookie($cookies, $cookieName);
        }

        return $cookies->renderIntoCookieHeader($request);
    }

    private function decryptCookie(Cookies $cookies, $cookieName)
    {
        if (! $cookies->has($cookieName)) {
            return $cookies;
        }

        $cookie = $cookies->get($cookieName);
        $encodedValue = $cookie->getValue();
        $signedValue = base64_decode($encodedValue);
        $encryptedValue = $this->validation->extract($signedValue);
        $decryptedValue = $this->decryptor->decrypt($encryptedValue);
        $decryptedCookie = $cookie->withValue($decryptedValue);

        return $cookies->with($decryptedCookie);
    }
}
