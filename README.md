# JwtManager - Json Web Token Manager

JwtManager is a class which enables to generate and verify JWT (Json Web Token).

## Example

```
<?php

namespace App;

use FloWebDev\JwtManager;

class Security
{
    const SECRET_KEY = 'VlDoHDj6SRNffHixOgXtiei1dObCqYEGniueB5/LHbk=';

    public function generateJwk()
    {
        $jwtManager = new JwtManager(self::SECRET_KEY);

        // Do not use the "exp" key which is directly managed by JwtManager
        $payload = [
            'sub'   => '1234567890',
            'name'  => 'John Doe',
            'admin' => true
        ];

        // 86400 seconds: 24 hours validity
        return $jwtManager->getJwt($payload, 86400);
    }

    public function checkJwt($jwk)
    {
        $jwtManager = new JwtManager(self::SECRET_KEY);
        return $jwtManager->checkJWT($jwk);

    }
}
```