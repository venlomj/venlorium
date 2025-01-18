<?php 

namespace Lib\Security;

use Exception;
use InvalidArgumentException;
use Lib\Utilities\Config;

class JWTCodec
{
    public function encode(array $payload): string
    {
        $header = json_encode([
            "typ" => "JWT",
            "alg" => "HS256",
        ]);
        $header = $this->base64urlEncode($header);

        $payload = json_encode($payload);
        $payload = $this->base64urlEncode($payload);

        $signature = hash_hmac(
            "sha256",
            $header . "." . $payload,
            Config::get("JWT_SECRET"),
            true
        );
        $signature = $this->base64urlEncode($signature);

        return $header . "." . $payload . "." . $signature;
    }

    public function decode(string $token): array
    {
        if (preg_match("/^(?<header>.+)\.(?<payload>.+)\.(?<signature>.+)$/",
        $token,
         $matches) !== 1) {
            throw new InvalidArgumentException("Invalid token format");
         }

         $signature = hash_hmac(
            "sha256",
            $matches["header"] . "." . $matches["payload"],
            Config::get("JWT_SECRET"),
            true
        );

        $signature_from_token = $this->base64urlDencode($matches["signature"]);

        if (!hash_equals($signature, $signature_from_token)) {
            throw new Exception("Signature doesn't match");
        }

        $payload = json_decode($this->base64urlDencode($matches["payload"]), true);

        if ($payload["exp"] < time()) {
            throw new Exception("Token expired");
        }
        return $payload;
    }

    public function base64urlEncode(string $text): string
    {
        return str_replace(
            ["+","/", "="],
            ["-", "_", ""],
            base64_encode($text));
    }

    public function base64urlDencode(string $text): string
    {
        return base64_decode(
            str_replace(
            ["-", "_"],
            ["+","/"],
            $text)
            );
    }
}