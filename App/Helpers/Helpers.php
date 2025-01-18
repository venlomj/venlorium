<?php

namespace App\Helpers;

class Helpers
{
    public static function getAuthorizationHeader()
    {
        $header = null;
        if (isset($_SERVER["Authorization"])) {
            $header = trim($_SERVER["Authorization"]);
        }
        elseif (isset($_SERVER["HTTP_AUTHORIZATION"])) {
            $header = trim($_SERVER["HTTP_AUTHORIZATION"]);
        }
        elseif (isset($_SERVER["apache_request_headers"])) {
            $requestHeaders = apache_request_headers();
            $requestHeaders = array_combine(array_map('ucwords', array_keys($requestHeaders)), array_values($requestHeaders));

            if (isset($requestHeaders['Authorization'])) {
                $header = trim($requestHeaders['Authorization']);
            }
        }
        return $header;
    }

    public static function getBearerToken()
    {
        $header = self::getAuthorizationHeader();

        if (!empty($header)) {
            if (preg_match('/Bearer\s(\S+)/', $header, $matches)) {
                return $matches[1];
            }
        }
        return null;
    }
}