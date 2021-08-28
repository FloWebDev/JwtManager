# JwtManager - Json Web Token Manager

JwtManager is a class which enables to generate and verify JWT (Json Web Token).
### Require

* php >= 7.2

## Features

From a secret key, the JWT Manager enables you to:
- generate a JWT,
- check the integrity and its validity period,
- if the JWT integrity and validity period are validated, header and payload informations are returned (>= v1.3.0),
- generate (>= v1.2.0) a new JWT from a valid or expired JWT as soon as the integrity is verified (signature).
## Example

```
<?php

namespace App;

use FloWebDev\JwtManager;

class Security
{
    const SECRET_KEY = 'VlDoHDj6SRNffHixOgXtiei1dObCqYEGniueB5/LHbk=';

    public function generateJwt()
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

    public function checkJwt($jwt)
    {
        $jwtManager = new JwtManager(self::SECRET_KEY);
        return $jwtManager->checkJwt($jwt);

    }

    public function newJwt($currentOrFormerJwt)
    {
        $jwtManager = new JwtManager(self::SECRET_KEY);

        // 3600 seconds: 1 hour validity
        return $jwtManager->refreshJwt($currentOrFormerJwt, 3600);
    }
}
```

## Tests

`php tests/JwtManagerTest.php `

**ouputs:**

```
Test 1 OK - getJwtTest
Test 2 OK - checkJwTWithoutExpTest
Test 3 OK - checkJwTWithExpOkTest
Test 4 OK - checkJwTWithExpKoTest
Test 5 OK - checkRefreshJwtWithExpOkTest
Test 6 OK - checkRefreshJwtWithExpKoTest
Test 7 OK - checkRefreshJwtWithExpNullTest
Test 8 OK - checkRefreshJwtWithWrongJwt
Test 9 OK - checkRefreshJwtWithWrongFormatJwt
Tests: 9, Success: 9, Failures: 0
```