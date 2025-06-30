<?php

namespace App\Tests\Security;

use App\Entity\User;
use App\Security\AppAuthenticator;
use Exception;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;

class AppAuthenticatorTest extends TestCase
{
    private MockObject|UrlGeneratorInterface|null $urlGenerator;
    private AppAuthenticator|null $appAuthenticator;

    protected function setUp(): void
    {
        $this->urlGenerator = $this->createMock(UrlGeneratorInterface::class);

        $this->appAuthenticator = new AppAuthenticator($this->urlGenerator);
    }

    public function testAuthenticateSuccess(): void
    {
        $request = new Request([], [
            'username' => 'username',
            'password' => 'password',
            '_csrf_token' => 'csrf_token'
        ]);

        $session = new Session(new MockArraySessionStorage());
        $request->setSession($session);

        $expected = $this->appAuthenticator->authenticate($request);
        $this->assertInstanceOf(Passport::class, $expected);
    }

    public function testOnAuthenticationSuccessWithTargetPath(): void
    {

        $request = new Request();
        $firewallName = 'main';
        $uri = '/test';
        $session = new Session(new MockArraySessionStorage());
        $session->set('_security.' . $firewallName . '.target_path', $uri);
        $request->setSession($session);

        $token = new UsernamePasswordToken(new User(), $firewallName);

        /** @var RedirectResponse */
        $response = $this->appAuthenticator->onAuthenticationSuccess($request, $token, $firewallName);

        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertEquals($uri, $response->getTargetUrl());
    }

    public function testOnAuthenticationSuccessWithOutTargetPath(): void
    {
        $request = new Request();
        $session = new Session(new MockArraySessionStorage());
        $request->setSession($session);

        $route_name = 'app_dashboard';
        $expected = '/dashboard';
        $this->urlGenerator->expects($this->once())
            ->method('generate')
            ->with($route_name)
            ->willReturn($expected);

        $firewallName = 'main';
        $token = new UsernamePasswordToken(new User(), $firewallName);

        /** @var RedirectResponse */
        $response = $this->appAuthenticator->onAuthenticationSuccess($request, $token, $firewallName);
        $this->assertEquals($expected, $response->getTargetUrl());
    }

    public function testGetLoginUrl(): void
    {
        $route_name = $this->appAuthenticator::LOGIN_ROUTE;
        $expected = '/login';
        $this->urlGenerator->expects($this->once())
            ->method('generate')
            ->with($route_name)
            ->willReturn($expected);

        $reflection = new \ReflectionClass(AppAuthenticator::class);
        $method = $reflection->getMethod('getLoginUrl');
        $method->setAccessible(true); // rend la méthode accessible

        $request = new Request();
        $url = $method->invoke($this->appAuthenticator, $request);

        $this->assertEquals($expected, $url);
    }

    protected function tearDown(): void
    {
        $this->appAuthenticator = null;
        $this->urlGenerator = null;
    }
}
