#!/usr/bin/env php
<?php
require __DIR__ . '/../src/JwtManager.php';
use FloWebDev\JwtManager;

class JwtManagerTest extends JwtManager
{
    /**
     * Tests the generation of a JWT
     *
     * @return bool Test result
     */
    public function getJwtTest(): bool
    {
        $jwt = $this->getJwt([
            'sub'   => '1234567890',
            'name'  => 'John Doe',
            'admin' => true
        ], 86400);

        return is_string($jwt) && count(explode('.', $jwt)) === 3;
    }

    /**
     * Checks the validity of a JWT without a validity period
     *
     * @return bool Test result
     */
    public function checkJwTWithoutExpTest(): bool
    {
        $check = $this->checkJwt($this->getJwt([
            'sub'   => '1234567890',
            'name'  => 'John Doe',
            'admin' => true
        ]));

        return is_array($check) && array_key_exists('header', $check) && array_key_exists('payload', $check)
            && array_key_exists('exp', $check['payload']) && is_null($check['payload']['exp']);
    }

    /**
     * Checks the validity of a JWT with a validity period OK
     *
     * @return bool Test result
     */
    public function checkJwTWithExpOkTest(): bool
    {
        $check = $this->checkJwt($this->getJwt([
            'sub'   => '1234567890',
            'name'  => 'John Doe',
            'admin' => true
        ], 10));

        return is_array($check) && array_key_exists('header', $check) && array_key_exists('payload', $check)
            && array_key_exists('exp', $check['payload']) && is_int($check['payload']['exp']);
    }

    /**
     * Checks the validity of a JWT with a validity period KO
     *
     * @return bool Test result
     */
    public function checkJwTWithExpKoTest(): bool
    {
        $ckeck = $this->checkJwt($this->getJwt([
            'sub'   => '1234567890',
            'name'  => 'John Doe',
            'admin' => true
        ], -1));

        return is_null($ckeck);
    }

    /**
     * Checks the refresh of a JWT which is still valid
     *
     * @return bool Test result
     */
    public function checkRefreshJwtWithExpOkTest(): bool
    {
        $jwt = $this->refreshJwt($this->getJwt([
            'sub'   => '1234567890',
            'name'  => 'John Doe',
            'admin' => true
        ], -1), 3600);

        return is_string($jwt) && count(explode('.', $jwt)) === 3;
    }

    /**
     * Checks the refresh of a JWT which is expired
     *
     * @return bool Test result
     */
    public function checkRefreshJwtWithExpKoTest(): bool
    {
        $jwt = $this->refreshJwt($this->getJwt([
            'sub'   => '1234567890',
            'name'  => 'John Doe',
            'admin' => true
        ], -1), 3600);

        return is_string($jwt) && count(explode('.', $jwt)) === 3;
    }

    /**
     * Checks the refresh of a JWT when period of validity is null
     *
     * @return bool Test result
     */
    public function checkRefreshJwtWithExpNullTest(): bool
    {
        $jwt = $this->refreshJwt($this->getJwt([
            'sub'   => '1234567890',
            'name'  => 'John Doe',
            'admin' => true
        ]), 3600);

        return is_string($jwt) && count(explode('.', $jwt)) === 3;
    }

    /**
     * Checks the refresh JWT when it's not an integrity JWT
     *
     * @return bool Test result
     */
    public function checkRefreshJwtWithWrongJwt(): bool
    {
        $res = $this->refreshJwt('WrOng.JSOn.WebToken', 3600);

        return $res === null;
    }

    /**
     * Checks the refresh JWT when it's not a good format JWT
     *
     * @return bool Test result
     */
    public function checkRefreshJwtWithWrongFormatJwt(): bool
    {
        $res = $this->refreshJwt('WrOngJSOnWebToken', 3600);

        return $res === null;
    }
}

// Run tests
$nbTest    = 0;
$nbSuccess = 0;
$f         = new ReflectionClass('JwtManagerTest');
foreach ($f->getMethods() as $m) {
    if ($m->class == 'JwtManagerTest') {
        $nbTest++;
        $method     = $m->name;
        $jwtManager = new JwtManagerTest('LaMUjgU0c6qiZBQb5ULx8zyLUJw/7chsCgMLAyn9xn8=');
        $res        = $jwtManager->$method();
        $nbSuccess  = $res ? $nbSuccess + 1 : $nbSuccess;

        echo "Test $nbTest " . ($res ? "OK" : "KO") . " - $method\n";
    }
}
echo "Tests: $nbTest, Success: $nbSuccess, Failures: " . ($nbTest - $nbSuccess) . "\n";
