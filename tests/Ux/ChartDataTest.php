<?php

namespace App\Tests\Ux;

use App\Entity\User;
use App\Ux\ChartData;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use App\Repository\DepenseRepository;
use DateTime;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class ChartDataTest extends TestCase
{
    private ?ChartData $chartData;
    /** @var  MockObject|DepenseRepository|null */
    private $depenseRepository;

    protected function setUp(): void
    {
        /** @var  MockObject|DepenseRepository|null */
        $this->depenseRepository = $this->createMock(DepenseRepository::class);
        $tokenStorage = new TokenStorage();
        $token = new UsernamePasswordToken(new User, 'main');
        $tokenStorage->setToken($token);
        $this->chartData = new ChartData($this->depenseRepository, $tokenStorage);
    }

    protected function tearDown(): void
    {
        $this->chartData = null;
        $this->depenseRepository = null;
    }

    public function testGetLabels(): void
    {
        $depenses = ['test'];
        $depensesTotal = ['depenseTotal'];

        $dates = [new DateTime('- 7 days'), new DateTime('+ 7 days')];
        $this->depenseRepository
            ->expects($this->once())
            ->method('findDepensesWithCapital')
            ->with(new User, $dates)
            ->willReturn($depenses);

        $this->depenseRepository
            ->expects($this->once())
            ->method('getTotalDepenseAndCapitalInDateGivingByUser')
            ->with(new User, $dates)
            ->willReturn($depensesTotal);

        $labelsExpected = array_merge($depenses, $depensesTotal);
        $labelsActual = $this->chartData->getLabels($dates);

        $this->assertSame($labelsExpected, $labelsActual);
    }
}
