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
     * Checks the validity of a token without a validity period
     *
     * @return bool Test result
     */
    public function checkJwTWithoutExpTest(): bool
    {
        $check = $this->checkJWT($this->getJwt([
            'sub'   => '1234567890',
            'name'  => 'John Doe',
            'admin' => true
        ]));

        return $check === true;
    }

    /**
     * Checks the validity of a token with a validity period OK
     *
     * @return bool Test result
     */
    public function checkJwTWithExpOkTest(): bool
    {
        $check = $this->checkJWT($this->getJwt([
            'sub'   => '1234567890',
            'name'  => 'John Doe',
            'admin' => true
        ], 10));

        return $check === true;
    }

    /**
     * Checks the validity of a token with a validity period KO
     *
     * @return bool Test result
     */
    public function checkJwTWithExpKoTest(): bool
    {
        $ckeck = $this->checkJWT($this->getJwt([
            'sub'   => '1234567890',
            'name'  => 'John Doe',
            'admin' => true
        ], -1));

        return $ckeck === false;
    }
}

// Tests
$jwtManager = new JwtManagerTest('LaMUjgU0c6qiZBQb5ULx8zyLUJw/7chsCgMLAyn9xn8=');
echo $jwtManager->getJwtTest() ? "Test 1 OK\n" : "Test 1 KO\n";
echo $jwtManager->checkJwTWithoutExpTest() ? "Test 2 OK\n" : "Test 2 KO\n";
echo $jwtManager->checkJwTWithExpOkTest() ? "Test 3 OK\n" : "Test 3 KO\n";
echo $jwtManager->checkJwTWithExpKoTest() ? "Test 4 OK\n" : "Test 4 KO\n";