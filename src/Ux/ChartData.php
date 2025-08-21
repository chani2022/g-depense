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

    public function getLabels(?array $dates = null)
    {
        $user = $this->tokenStorage->getToken()->getUser();
        $depenses = $this->depenseRepository->findDepensesWithCapital($user, $dates);
        $depensesTotal = $this->depenseRepository->getTotalDepenseAndCapitalInDateGivingByUser($user, $dates);
    }
}
