<?php

namespace Dflydev\EncryptedFigCookies;

use Dflydev\EncryptedFigCookies\Encryption\Decryptor;
use Dflydev\FigCookies\Cookies;
use Psr\Http\Message\RequestInterface;

class RequestCookieDecryptor
{
    /**
     * @var Decryptor
     */
    private $decryptor;

    public function __construct(Decryptor $decryptor)
    {
        $this->decryptor = $decryptor;
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
        $encryptedValue = $cookie->getValue();
        $decryptedValue = $this->decryptor->decrypt(base64_decode($encryptedValue));
        $decryptedCookie = $cookie->withValue($decryptedValue);

        return $cookies->with($decryptedCookie);
    }
}
