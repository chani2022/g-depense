<?php

namespace App\Tests\Flash;

use App\Flash\MessageFlash;
use LogicException;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBag;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpFoundation\Session\FlashBagAwareSessionInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class MessageFlashTest extends TestCase
{
    private MessageFlash|null $messageFlash;
    /** @var RequestStack&null */
    private $requestStack;
    /** @var MockObject&SessionInterface&null */
    private $session;

    protected function setUp(): void
    {
        $this->session = $this->createMock(FlashBagAwareSessionInterface::class);
        $this->requestStack = new RequestStack();
        $this->requestStack->push(new Request());
        $this->requestStack->getCurrentRequest()->setSession($this->session);

        $this->messageFlash = new MessageFlash($this->requestStack);
    }

    public function testAddFlashThrowException(): void
    {
        $this->expectException(LogicException::class);
        $this->messageFlash->addFlash('invalid', 'ce type est invalide');
    }
    /**
     * @dataProvider typeValide
     */
    public function testAddFlashSuccess(string $type, string $message): void
    {
        $flashBag = $this->createMock(FlashBagInterface::class);
        $this->session->expects($this->once())
            ->method('getFlashBag')
            ->willReturn($flashBag);

        $flashBag->expects($this->once())
            ->method('add')
            ->with($type, $message);

        $this->messageFlash->addFlash($type, $message);
    }

    public static function typeValide(): array
    {
        return [
            'success' => [
                "type" => "success",
                "message" => "Success"
            ],
            'danger' => [
                "type" => "danger",
                "message" => "Danger"
            ]
        ];
    }


    protected function tearDown(): void
    {
        $this->messageFlash = null;
        $this->requestStack = null;
        $this->session = null;
    }
}
