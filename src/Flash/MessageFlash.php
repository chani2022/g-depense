<?php

namespace App\Flash;

use LogicException;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\FlashBagAwareSessionInterface;

class MessageFlash
{
    public function __construct(private RequestStack $requestStack) {}
    public function addFlash(string $type, string $message): void
    {
        $typeValid = ['success', 'danger'];
        if (!in_array($type, $typeValid)) {
            throw new LogicException(sprintf('type invalid %s, les type acceptés sont %s', $type, implode(', ', $typeValid)));
        }

        /** @var FlashBagAwareSessionInterface */
        $session = $this->requestStack->getSession();

        $session->getFlashBag()->add($type, $message);
    }
}
