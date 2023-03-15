<?php

declare(strict_types=1);

namespace UserLoginService\Tests\Application;

use PHPUnit\Framework\TestCase;
use PHPUnit\Util\Exception;
use UserLoginService\Application\SessionManager;
use UserLoginService\Application\UserLoginService;
use UserLoginService\Domain\User;
use UserLoginService\Infrastructure\FacebookSessionManager;
use Mockery;

use function PHPUnit\Framework\assertEquals;

final class UserLoginServiceTest extends TestCase
{
/**
     * @test
     */
    public function newUserShouldBeLogged()
    {
        $userLoginService = new UserLoginService(new FacebookSessionManager());

        $user = new User("paco");

        $userLoginService->manualLogin($user);

        $this->assertEquals(in_array($user, $userLoginService->getLoggedUsers()), true);
    }

    /**
     * @test
     */
    public function userIsAlreadyLoggedIn()
    {
        $this
            ->expectException(Exception::class);

        $this
            ->expectExceptionMessage("User already logged in");

        $userLoginService = new UserLoginService(new FacebookSessionManager());

        $user = new User("paco");

        $userLoginService->manualLogin($user);

        $user2 = new User("paco");

        $userLoginService->manualLogin($user2);
    }

    /**
     * @test
     */
    public function shouldGetTheExternalSession()
    {
        $stubFacebookManager = $this
            ->createStub(FacebookSessionManager::class);

        $stubFacebookManager
            ->method("getSessions")
            ->willReturn(10);

        $userLoginService = new UserLoginService($stubFacebookManager);

        $externalSession = $userLoginService->getExternalSessions();

        $this-> assertEquals($externalSession, 10);
    }
    /**
     * @test
     */
    public function shouldLoginWithFacebook()
    {
        $stubFacebookManager = $this
            ->createStub(FacebookSessionManager::class);

        $stubFacebookManager
            ->method("login")
            ->willReturn(true);

        $userLoginService = new UserLoginService($stubFacebookManager);

        $loginResult = $userLoginService
            ->login("Paco", "Secreto123");

        $expectedUser = new User("Paco");

        $this->assertEquals($loginResult, "Login correcto");

        $this->assertEquals(in_array($expectedUser, $userLoginService->getLoggedUsers()), true);
    }

    /**
     * @test
     */
    public function shouldntAllowLoginWithFacebook()
    {
        $stubFacebookManager = $this
            ->createStub(FacebookSessionManager::class);

        $stubFacebookManager->method("login")
            ->willReturn(false);

        $userLoginService = new UserLoginService($stubFacebookManager);

        $loginResult = $userLoginService->login("Alfredo", "123Secreto");

        $this->assertEquals($loginResult, "Login incorrecto");
    }

    /**
     * @test
     */
    public function shouldLogoutUser()
    {
        $mokery = new Mockery();
        $sessionManager = $mokery->spy(SessionManager::class);
        $userLoginService = new UserLoginService($sessionManager);
        $user = new User("pepe");

        $userLoginService->manualLogin($user);
        $response = $userLoginService->logout($user);
        $sessionManager->shouldHaveReceived()->logout("pepe");

        $this->assertEquals($response, "Ok");
    }

    /**
     * @test
     */
    public function shouldNotAllowLogoutUser()
    {
        $mokery = new Mockery();
        $sessionManager = $mokery->spy(SessionManager::class);
        $userLoginService = new UserLoginService($sessionManager);
        $user = new User("pepe");

        $response = $userLoginService->logout($user);
        $sessionManager->shouldHaveReceived()->logout("pepe");

        $this->assertEquals($response, "User not found");
    }
}
