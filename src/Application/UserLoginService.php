<?php

namespace UserLoginService\Application;

use PHPUnit\Util\Exception;
use UserLoginService\Domain\User;
use UserLoginService\Infrastructure\FacebookSessionManager;

class UserLoginService
{
    private array $loggedUsers = [];
    private FacebookSessionManager $fsm;

    public function __construct(FacebookSessionManager $externalFsm)
    {
        $this->fsm= $externalFsm;
    }

    public function manualLogin(User $user): void
    {
        if (in_array($user, $this->getLoggedUsers())) {
            throw new Exception("User already logged in");
        }
        $this->loggedUsers[] = $user;
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
        if ($this->fsm->login($userName, $password)) {
            $this->manualLogin(new User($userName));
            return "Login correcto";
        }
        return "Login incorrecto";
    }
}


// Como buena practica deberiamos separar esta clase en dos ya que si no por el hecho de pasa fsm en el constructor
// estamos obligados a usar un dummy, la forma de ahorrarnos el dummy seria separar esta clase en dos, quitandonos getExternalSessions.