<?php

namespace Dflydev\EncryptedFigCookies;

use Dflydev\EncryptedFigCookies\Encryption\Adapter\Rot13Encryption;
use Dflydev\FigCookies\Cookie;
use Dflydev\FigCookies\Cookies;
use Dflydev\FigCookies\SetCookie;
use Dflydev\FigCookies\SetCookies;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class EncryptedFigCookiesMiddlewareTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     * @dataProvider provide_decrypts_and_encrypts_cookies_data
     */
    public function it_decrypts_and_encrypts_cookies(
        array $encryptedCookies
    ) {
        $request = $this->getRequest($encryptedCookies);
        $response = new FigCookieTestingResponse();
        $next = $this->getNext($encryptedCookies);

        $middleware = EncryptedFigCookiesMiddleware::createWithEncryption(
            new Rot13Encryption(),
            array_keys($encryptedCookies)
        );

        $response = $middleware($request, $response, $next);

        $this->checkResponse($response, $encryptedCookies);
    }

    public function provide_decrypts_and_encrypts_cookies_data()
    {
        $random = function () {
            return implode('', array_map(function () {
                return MD5(microtime());
            }, range(1,32)));
        };

        return [
            [
                [
                    'sessionToken' => [
                        'before' => 'ENCRYPTED',
                        'after'  => 'ENCRYPTED AND UPDATED',
                    ],
                ],
                [
                    'sessionToken' => [
                        'before' => 'ENCRYPTED',
                        'after'  => 'ENCRYPTED AND UPDATED',
                    ],
                    'random' => [
                        'before' => 'before'.$random(),
                        'after'  => 'after'.$random(),
                    ],
                ],
            ],
        ];
    }

    private function getRequest(array $encryptedCookies)
    {
        $cookies = (new Cookies())
            ->with(Cookie::create('theme', 'light'))
        ;

        foreach ($encryptedCookies as $name => $values) {
            // We are expecting the value before the middleware
            // to be encrypted.
            $storedValue = base64_encode(str_rot13($values['before']));
            $cookie = Cookie::create($name, $storedValue);
            $cookies = $cookies->with($cookie);
        }

        $cookies = $cookies
            ->with(Cookie::create('hello', 'world'))
        ;

        return $cookies->renderIntoCookieHeader(
            new FigCookieTestingRequest()
        );
    }

    private function getNext(array $encryptedCookies)
    {
        return function (
            RequestInterface $nextRequest,
            ResponseInterface $nextResponse
        ) use (
            $encryptedCookies
        ) {
            $cookies = Cookies::fromRequest($nextRequest);
            $this->assertEquals('light', $cookies->get('theme')->getValue());
            $this->assertEquals('world', $cookies->get('hello')->getValue());
            foreach ($encryptedCookies as $name => $values) {
                // We are expecting the value just inside
                // the middleware to be decrypted.
                $this->assertEquals(
                    $values['before'],
                    $cookies->get($name)->getValue()
                );
            }

            // Simulate the application setting cookies on the response
            // in plaintext.
            $nextResponse = $nextResponse
                ->withAddedHeader(SetCookies::SET_COOKIE_HEADER, SetCookie::create('theme', 'red'))
            ;

            foreach ($encryptedCookies as $name => $values) {
                // We are expecting to be able to write out
                // plaintext to our encrypted cookies from
                // within our application.
                $nextResponse = $nextResponse
                    ->withAddedHeader(
                        SetCookies::SET_COOKIE_HEADER,
                        SetCookie::create($name, $values['after'])
                    )
                ;
            }

            $nextResponse = $nextResponse
                ->withAddedHeader(SetCookies::SET_COOKIE_HEADER, SetCookie::create('hello', 'WORLD!'))
            ;

            return $nextResponse;
        };
    }

    private function checkResponse(ResponseInterface $response, array $encryptedCookies)
    {
        $setCookies = SetCookies::fromResponse($response);

        $this->assertEquals('red', $setCookies->get('theme')->getValue());

        foreach ($encryptedCookies as $name => $values) {
            // We are expecting the value after the
            // middleware to be encrypted.
            $storedValue = base64_encode(str_rot13($values['after']));
            $this->assertEquals(
                $storedValue,
                $setCookies->get($name)->getValue()
            );
        }

        $this->assertEquals('WORLD!', $setCookies->get('hello')->getValue());
    }
}
