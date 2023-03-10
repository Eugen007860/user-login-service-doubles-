<?php

namespace UserLoginService\Application;

use PHPUnit\Util\Exception;
use UserLoginService\Domain\User;
use UserLoginService\Infrastructure\FacebookSessionManager;

class UserLoginService
{
    private array $loggedUsers = [];

    public function manualLogin(User $user): void
    {
        if (in_array($user, $this->getLoggedUsers())) {
            throw new Exception("User already logged in");
        }
        else{
            $this->loggedUsers[] = $user;
        }
    }

    public function getLoggedUsers(): array
    {
        return $this->loggedUsers;
    }

    public function getExternalSessions(): int
    {
        $facebookManager = new FacebookSessionManager();
        return $facebookManager->getSessions();
    }

    public function login(string $userName, string $password): string
    {
        $facebookManager = new FacebookSessionManager();
        if ($facebookManager->login($userName, $password)){
            $this->manualLogin(new User($userName));
            return "Login correcto";
        }
        else{
            return "Login incorrecto";
        }
    }

}