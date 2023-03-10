<?php

declare(strict_types=1);

namespace UserLoginService\Tests\Application;

use PHPUnit\Framework\TestCase;
use PHPUnit\Util\Exception;
use UserLoginService\Application\UserLoginService;
use UserLoginService\Domain\User;
use UserLoginService\Infrastructure\FacebookSessionManager;

use function PHPUnit\Framework\assertEquals;

final class UserLoginServiceTest extends TestCase
{
    /**
     * @test
     */
    public function newUserShouldBeLogged()
    {
        $userLoginService = new UserLoginService();

        $user = new User("paco");

        $userLoginService->manualLogin($user);

        $this->assertEquals(in_array($user, $userLoginService->getLoggedUsers()), true);
    }

    /**
     * @test
     */
    public function userIsAlreadyLoggedIn()
    {
        $this->expectException(Exception::class);

        $this->expectExceptionMessage("User already logged in");

        $userLoginService = new UserLoginService();

        $user = new User("paco");

        $userLoginService->manualLogin($user);

        $user2 = new User("paco");

        $userLoginService->manualLogin($user2);
    }

    /**
     * @test
     */
    public function shouldLoginWithFacebook()
    {
        $stubFacebookManager = $this->createStub(FacebookSessionManager::class);

        $stubFacebookManager->method("login")->willReturn(true);

        $userLoginService = new UserLoginService();

        $loginResult = $userLoginService->login("Paco", "Secreto123", $stubFacebookManager);

        assertEquals($loginResult, "Login correcto");
    }

    /**
     * @test
     */
    public function shouldntAllowLoginWithFacebook()
    {
        $stubFacebookManager = $this->createStub(FacebookSessionManager::class);

        $stubFacebookManager->method("login")->willReturn(false);

        $userLoginService = new UserLoginService();

        $loginResult = $userLoginService->login("Alfredo", "123Secreto", $stubFacebookManager);

        assertEquals($loginResult, "Login incorrecto");
    }
}
