# JwtManager - Json Web Token Manager

JwtManager is a class which enables to generate and verify JWT (Json Web Token).
### Require

* php >= 7.2

## Features

From a secret key, the JWT Manager enables you to:
- generate a JWT,
- check the integrity and its validity period,
- get header and payload informations (>= v1.3.0) if the JWT integrity and validity period are validated,
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
**Output JWT generation example:**

`eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzUxMiJ9.eyJzdWIiOiIxMjM0NTY3ODkwIiwibmFtZSI6IkpvaG4gRG9lIiwiYWRtaW4iOnRydWUsImV4cCI6MTYzMDMwNTU0OX0.J7qxtEOGkgsbQKoduqgUhZ9Y6DHc8Vm-XlWDFCWgc6Dp5TwVh8MZ54_3pqp_vxIru3JKvIr8undToYdlJWbKYg`

**Output when JWT OK example:**

```
array(2) {
  ["header"]=>
  array(2) {
    ["typ"]=>
    string(3) "JWT"
    ["alg"]=>
    string(5) "HS512"
  }
  ["payload"]=>
  array(4) {
    ["sub"]=>
    string(10) "1234567890"
    ["name"]=>
    string(8) "John Doe"
    ["admin"]=>
    bool(true)
    ["exp"]=>
    int(1630222751)
  }
}
```

**Output when JWT KO example:**

`NULL`

## Tests

`php tests/JwtManagerTest.php `

**Ouputs:**

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