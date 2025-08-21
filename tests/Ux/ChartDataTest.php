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
            [
                'total_depense_general' => 20.10,
                'total_capital_general' => 100.20
            ]
        ];

        $depenseExpected = array_merge($depenses, $depensesTotal[0]);
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
            ->with($depenses, $depensesTotal[0])
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
        return [
            [
                '2 data ' => [
                    'total_depense_general' => 20.10,
                    'total_capital_general' => 100.20,
                    [
                        'id' => 1,
                        'label' => 'label1',
                        'total_capital' => 15.2,
                        'total_depense' => 5.2
                    ],
                    [
                        'id' => 1,
                        'label' => 'label2',
                        'total_capital' => 100.2,
                        'total_depense' => 8
                    ]
                ],
                'expected' => [
                    'labels' => ['label1', 'label2'],
                    'datasets' => [
                        [
                            'label' => 'Depense mensuel',
                            'data' => [5.2, 8],
                            'borderColor' => ChartData::STYLE['depense']['border'],
                            'backgroundColor' => ChartData::STYLE['depense']['background'],
                        ],
                        [
                            'label' => 'Capital mensuel',
                            'data' => [15.2, 100.2],
                            'borderColor' => ChartData::STYLE['capital']['border'],
                            'backgroundColor' => ChartData::STYLE['capital']['background'],
                        ],
                    ]
                ]
            ],

            [
                '3 data ' => [
                    'total_depense_general' => 20.10,
                    'total_capital_general' => 100.20,
                    [
                        'id' => 1,
                        'label' => 'label1',
                        'total_capital' => 15.2,
                        'total_depense' => 5.2
                    ],
                    [
                        'id' => 1,
                        'label' => 'label2',
                        'total_capital' => 100.2,
                        'total_depense' => 8
                    ],
                    [
                        'id' => 1,
                        'label' => 'label3',
                        'total_capital' => 45.2,
                        'total_depense' => 8.25
                    ]
                ],
                'expected' => [
                    'labels' => ['label1', 'label2', 'label3'],
                    'datasets' => [
                        [
                            'label' => 'Depense mensuel',
                            'data' => [5.2, 8, 8.25],
                            'borderColor' => ChartData::STYLE['depense']['border'],
                            'backgroundColor' => ChartData::STYLE['depense']['background'],
                        ],
                        [
                            'label' => 'Capital mensuel',
                            'data' => [15.2, 100.2, 45.2],
                            'borderColor' => ChartData::STYLE['capital']['border'],
                            'backgroundColor' => ChartData::STYLE['capital']['background'],
                        ],
                    ]
                ]
            ]

        ];
    }
}
