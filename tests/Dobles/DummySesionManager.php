<?php

namespace UserLoginService\Tests\Dobles;

class DummySesionManager
{
    public function getSessions(): int
    {

    }

    public function login(string $userName, string $password): bool{

    }
}