<?php

namespace App\Ux;

use App\Repository\DepenseRepository;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class ChartData
{
    public function __construct(
        private DepenseRepository $depenseRepository,
        private TokenStorageInterface $tokenStorage
    ) {}

    public function getLabels(array $dates)
    {
        $depenses = $this->depenseRepository->getDepenseBetweenDateWithCapital($dates, $this->tokenStorage->getToken()->getUser());
    }
}
