<?php

namespace App\Tests\Ux;

use App\Entity\User;
use App\Ux\ChartData;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use App\Repository\DepenseRepository;
use App\Helper\ArrayHelper;
use DateTime;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class ChartDataTest extends TestCase
{
    private ?ChartData $chartData;
    /** @var  MockObject|DepenseRepository|null */
    private $depenseRepository;
    /** @var MockObject|ArrayHelper|null */
    private $arrayHelper;

    protected function setUp(): void
    {
        /** @var  MockObject|DepenseRepository|null */
        $this->depenseRepository = $this->createMock(DepenseRepository::class);

        /** @var MockObject|ArrayHelper|null */
        $this->arrayHelper = $this->createMock(ArrayHelper::class);

        $tokenStorage = new TokenStorage();
        $token = new UsernamePasswordToken(new User, 'main');
        $tokenStorage->setToken($token);

        $this->chartData = new ChartData($this->depenseRepository, $tokenStorage, $this->arrayHelper);
    }

    protected function tearDown(): void
    {
        $this->chartData = null;
        $this->depenseRepository = null;
    }

    public function testGetLabels(): void
    {
        $labelsActual = $this->chartData->getLabels();

        $this->assertIsArray($labelsActual);
    }

    public function testSetLabels(): void
    {
        $this->chartData->setLabels(['label1', 'label2']);

        $labelsActual = $this->chartData->getLabels();

        $this->assertSame(['label1', 'label2'], $labelsActual);
    }

    public function testGetDatasets(): void
    {
        $datasets = $this->chartData->getDatasets();

        $this->assertIsArray($datasets);
    }

    public function testSetDatasets(): void
    {
        $this->chartData->setDatasets(['dataset']);

        $datasets = $this->chartData->getDatasets();

        $this->assertSame(['dataset'], $datasets);
    }

    public function testGetDepenses(): void
    {
        $depenses = [
            [
                'id' => 1,
                'label' => 'label',
                'total_capital' => 15.20,
                'total_depense' => 5.20
            ],
            [
                'id' => 1,
                'label' => 'label2',
                'total_capital' => 15.20,
                'total_depense' => 5.20
            ]
        ];

        $depensesTotal = [
            'total_depense_general' => 20.10,
            'total_capital_general' => 100.20
        ];

        $depenseExpected = array_merge($depenses, $depensesTotal);
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

        $this->arrayHelper
            ->expects($this->once())
            ->method('merge')
            ->with($depenses, $depensesTotal)
            ->willReturn($depenseExpected);

        $depenseActual = $this->chartData->getDepenses($dates);

        $this->assertSame($depenseExpected, $depenseActual);
    }
    /**
     * @dataProvider provideDepenses
     */
    public function testHandleDepense(array $depense, array $expected): void
    {
        $dataChart = $this->chartData->handleDepense($depense);

        $this->assertSame($expected, $dataChart);
    }

    public static function provideDepenses(): array
    {
        $depenses = [
            'total_depense_general' => 20.10,
            'total_capital_general' => 100.20,
            [
                'id' => 1,
                'label' => 'label',
                'total_capital' => 15.20,
                'total_depense' => 5.20
            ],
            [
                'id' => 1,
                'label' => 'label2',
                'total_capital' => 15.20,
                'total_depense' => 5.20
            ]
        ];
        return [
            [
                'only label' => [
                    'test' => 1,
                    'test2' => 2,
                    [
                        'label' => 'label1'
                    ],
                    [
                        'label' => 'label2'
                    ]
                ],
                'expected' => [
                    'labels' => ['label1', 'label2'],
                    'datasets' => []
                ]
            ],
            [
                'only datasets' => [
                    'total_depense_general' => 20.10,
                    'total_capital_general' => 100.20,
                    [
                        'id' => 1,
                        'total_capital' => 15.20,
                        'total_depense' => 5.20
                    ],
                    [
                        'id' => 1,
                        'total_capital' => 100.20,
                        'total_depense' => 8
                    ]
                ],
                'expected' => [
                    'labels' => [],
                    'datasets' => [
                        [
                            'label' => 'depense',
                            'data' => [5.20, 8],
                            'borderColor' => ChartData::STYLE['depense']['border'],
                            'backgroundColr' => ChartData::STYLE['depense']['background'],
                        ],
                        [
                            'label' => 'capital',
                            'data' => [15.20, 100.20],
                            'borderColor' => ChartData::STYLE['capital']['border'],
                            'backgroundColr' => ChartData::STYLE['capital']['background'],
                        ],
                    ]
                ]
            ]
        ];
    }
}
