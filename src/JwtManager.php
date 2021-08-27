<?php

/*
 * No Copyright (free) 2021 Florian Mathevon <mathevon.florian@gmail.com>
 * JwtManager enables to generate and verify JWT (Json Web Token)
 */

namespace FloWebDev;

class JwtManager
{
    /**
     * @var string The secret key
     */
    private $key;

    /**
     * JwtManager Constructor
     *
     * @param string $key - Secret key
     */
    public function __construct(string $key)
    {
        $this->key = $key;
    }

    /**
     * Generates the JWT
     *
     * @param array $payload - Statements about an entity (typically, the user) and additional data
     * @param int $validity - The number of seconds the JWT is valid, if null it will be unlimited
     * @return string The JWT
     */
    public function getJwt(array $payload, ?int $validity = null): string
    {
        $header = [
            'typ' => 'JWT',
            'alg' => 'HS512'
        ];

        $payload['exp']     = !empty($validity) ? (time() + $validity) : null;
        $encodedJsonHeader  = $this->base64UrlEncode(json_encode($header));
        $encodedJsonPayload = $this->base64UrlEncode(json_encode($payload));

        return $encodedJsonHeader . '.' . $encodedJsonPayload . '.' . $this->generateSignature(
            $encodedJsonHeader,
            $encodedJsonPayload,
            $this->key
        );
    }

    /**
     * Used to check the integrity and validity of a JWT
     *
     * @param string $jwt - JWT
     * @return bool Returns true when the JWT and validity is OK, otherwise false
     */
    public function checkJwt(string $jwt): bool
    {
        return $this->checkJwtSignature($jwt) === true && $this->checkJwtValidity($jwt) === true;
    }

    /**
     * Used to generate a new JWT if integrity is OK (signature), the validity period is not checked there
     *
     * @param array $payload - Statements about an entity (typically, the user) and additional data
     * @param int $validity - The number of seconds the JWT is valid, if null it will be unlimited
     * @return string|null A new JWT if the current/former one is OK about integrity (signature), otherwise null
     */
    public function refreshJwt(string $jwt, ?int $validity = null): ?string
    {
        $explodedJwt = explode('.', $jwt);

        if (count($explodedJwt) === 3) {
            return $this->checkJwtSignature($jwt) ? $this->getJwt(json_decode($this->base64UrlDecode(($explodedJwt[1])), 1), $validity) : null;
        }

        return null;
    }

    /**
     * Generates the JWT signature
     *
     * @param string $header - The Jwt encoded header
     * @param string $payload - The JWT encoded payload
     * @return string The JWT signature
     */
    private function generateSignature(string $header, string $payload): string
    {
        return $this->base64UrlEncode((hash_hmac('sha512', $header . "." . $payload, $this->key, true)));
    }

    /**
     * Used to check the integrity of a JWT
     *
     * @param string $jwt - JWT
     * @return bool Returns true when the JWT integrity is OK, otherwise false
     */
    private function checkJwtSignature(string $jwt): bool
    {
        $explodedJwt = explode('.', $jwt);

        if (count($explodedJwt) === 3) {
            $comparativeSignature = $this->generateSignature($explodedJwt[0], $explodedJwt[1]);
            if ($comparativeSignature === $explodedJwt[2]) {
                return true;
            }
        }

        return false;
    }

    /**
     * Used to check the period of validity of a JWT
     *
     * @param string $jwt - JWT
     * @return bool Returns true when the JWT period of validity is OK, otherwise false
     */
    public function checkJwtValidity(string $jwt): bool
    {
        $explodedJwt = explode('.', $jwt);

        if (count($explodedJwt) === 3) {
            $payload = json_decode($this->base64UrlDecode($explodedJwt[1]), 1);
            if (array_key_exists('exp', $payload)) {
                if (is_null($payload['exp'])) {
                    return true;
                } elseif (intval($payload['exp']) > time()) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Encodes a string to base64 MIME string
     *
     * @link https://www.php.net/manual/fr/function.base64-encode.php#123098
     * @param string $str - The string to encode
     * @return string The encoded string
     */
    private function base64UrlEncode(string $str): string
    {
        return str_replace(['+','/','='], ['-','_',''], base64_encode($str));
    }

    /**
     * Decodes a base64 MIME string
     *
     * @link https://www.php.net/manual/fr/function.base64-encode.php#123098
     * @param string $str - The string to decode
     * @return string The decoded string
     */
    private function base64UrlDecode(string $str): string
    {
        return base64_decode(str_replace(['-','_'], ['+','/'], $str));
    }
}
