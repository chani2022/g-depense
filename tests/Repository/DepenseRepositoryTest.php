<?php

namespace App\Tests\Repository;

use App\Entity\depense;
use App\Entity\User;
use App\Repository\DepenseRepository;
use App\Tests\Trait\UserAuthenticatedTrait;
use DateTime;
use Hautelook\AliceBundle\PhpUnit\RefreshDatabaseTrait;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class DepenseRepositoryTest extends KernelTestCase
{
    use RefreshDatabaseTrait;
    use UserAuthenticatedTrait;

    private ?DepenseRepository $depenseRepository;

    protected function setUp(): void
    {
        static::bootKernel();

        $this->depenseRepository = $this->getContainer()->get(depenseRepository::class);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->depenseRepository = null;
    }

    public function testGetDepenseBetweenDateWithCapital(): void
    {
        $depenses = $this->depenseRepository->getDepenseBetweenDateWithCapital();
        dd($depenses);
    }
}
