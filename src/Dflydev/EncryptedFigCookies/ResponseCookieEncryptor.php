<?php

namespace Dflydev\EncryptedFigCookies;

use Dflydev\EncryptedFigCookies\Encryption\Encryptor;
use Dflydev\FigCookies\SetCookies;
use Psr\Http\Message\ResponseInterface;

class ResponseCookieEncryptor
{
    /**
     * @var Encryptor
     */
    private $encryptor;

    public function __construct(Encryptor $encryptor)
    {
        $this->encryptor = $encryptor;
    }

    private static function resolveCookieNames($cookieNames)
    {
        return is_array($cookieNames) ? $cookieNames : [(string) $cookieNames];
    }

    private static function hasNoCookieNames(array $cookieNames)
    {
        return count($cookieNames) < 1;
    }

    public function encrypt(ResponseInterface $response, $cookieNames)
    {
        $cookieNames = self::resolveCookieNames($cookieNames);

        if (self::hasNoCookieNames($cookieNames)) {
            return $response;
        }

        $setCookies = SetCookies::fromResponse($response);

        foreach ($cookieNames as $cookieName) {
            $setCookies = $this->encryptCookie($setCookies, $cookieName);
        }

        return $setCookies->renderIntoSetCookieHeader($response);
    }

    private function encryptCookie(SetCookies $setCookies, $cookieName)
    {
        if (! $setCookies->has($cookieName)) {
            return $setCookies;
        }

        $cookie = $setCookies->get($cookieName);
        $decryptedValue = $cookie->getValue();
        $encryptedValue = $this->encryptor->encrypt($decryptedValue);
        $encryptedCookie = $cookie->withValue(base64_encode($encryptedValue));

        return $setCookies->with($encryptedCookie);
    }
}
