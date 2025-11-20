<?php

namespace App\Security;

class Csrf
{
    private const SESSION_KEY = 'csrf_tokens';

    public static function getToken(string $formName): string
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION[self::SESSION_KEY])) {
            $_SESSION[self::SESSION_KEY] = [];
        }

        if (empty($_SESSION[self::SESSION_KEY][$formName])) {
            $_SESSION[self::SESSION_KEY][$formName] = bin2hex(random_bytes(32));
        }

        return $_SESSION[self::SESSION_KEY][$formName];
    }

    public static function checkToken(string $formName, ?string $token): bool
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (
            empty($_SESSION[self::SESSION_KEY][$formName]) ||
            empty($token)
        ) {
            return false;
        }

        $expected = $_SESSION[self::SESSION_KEY][$formName];

        return hash_equals($expected, $token);
    }
}
