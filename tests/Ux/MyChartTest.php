<?php

namespace App\Tests\Ux;

use App\Ux\MyChart;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Symfony\UX\Chartjs\Model\Chart;

class MyChartTest extends TestCase
{
    protected function setUp(): void {}

    protected function tearDown(): void {}

    public function testStyleByCompteSalaireValid(): void
    {
        $styleExpected = [
            'depense' => [
                'border' => 'rgb(255, 99, 132)',
                'background' => 'rgb(255, 99, 132)'
            ],
            'capital' => [
                'border' => 'rgb(22, 157, 150)',
                'background' => 'rgb(22, 157, 150)'
            ],
        ];
        $this->assertSame($styleExpected, MyChart::STYLE_BY_COMPTE_SALAIRE);
    }

    public function testTypeThrowException(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $myChart = new MyChart('exception');
    }

    /**
     * @dataProvider providerTypeValid
     */
    public function testTypeValid(string $typeNotStandard): void
    {
        $myChart = new MyChart($typeNotStandard);

        $this->assertTrue(true);
    }

    public function testGetType(): void
    {
        $typeExpected = 'line';
        $myChart = new MyChart($typeExpected);
        $typeActual = $myChart->getType();

        $this->assertSame($typeExpected, $typeActual);
    }

    public function testSetDataChart(): void
    {
        $data = ['test'];
        $typeExpected = 'line';
        $myChart = new MyChart($typeExpected);

        $myChart->setData($data);

        $this->assertSame($data, $myChart->getData());
    }

    public function testSetTitle(): void
    {
        $myChart = new MyChart('line');
        $myChart->setTitle('my title');

        $this->assertSame('my title', $myChart->getTitle());
    }
    /**
     * @dataProvider providerOptionsWithoutTitleShow
     */
    public function testSetOptionsWithoutTitle(string $type, array $optionsExpected): void
    {
        $myChart = new MyChart($type);
        $optionsActual = $myChart->getOptions();
        $this->assertEquals($optionsExpected, $optionsActual);
    }

    /**
     * @dataProvider providerOptionsWithTitleShow
     */
    public function testSetOptionsWithTitle(string $typeNotStandard, $optionsExpected): void
    {
        $myChart = (new MyChart($typeNotStandard, $typeNotStandard));
        $optionsActual = $myChart->getOptions();
        $this->assertEquals($optionsExpected, $optionsActual);
    }

    public static function providerOptionsWithTitleShow(): array
    {
        return [
            [
                'line',
                [
                    'scales' => [
                        'y' => [
                            'suggestedMin' => 0,
                            'suggestedMax' => 100,
                        ],
                    ],
                    'responsive' => true,
                    'plugins' => [
                        'title' => [
                            'display' => true,
                            'text' => 'line'
                        ]
                    ]
                ]
            ],
            [
                'vertical-bar',
                [
                    'scales' => [
                        'x' => [
                            'suggestedMin' => 0,
                            'suggestedMax' => 100,
                        ]
                    ],
                    'responsive' => true,
                    'plugins' => [
                        'title' => [
                            'display' => true,
                            'text' => 'vertical-bar'
                        ]
                    ]
                ]
            ],
            [
                'horizontal-bar',
                [
                    'indexAxis' => 'y',
                    'scales' => [
                        'x' => [
                            'suggestedMin' => 0,
                            'suggestedMax' => 100,
                        ],
                    ],
                    'responsive' => true,
                    'plugins' => [
                        'title' => [
                            'display' => true,
                            'text' => 'horizontal-bar'
                        ]
                    ]
                ]
            ],
            [
                'pie',
                [
                    'plugins' => [
                        'title' => [
                            'display' => true,
                            'text' => 'pie'
                        ]
                    ]
                ]
            ]

        ];
    }

    public static function providerOptionsWithoutTitleShow(): array
    {
        return [
            [
                'line',
                [
                    'scales' => [
                        'y' => [
                            'suggestedMin' => 0,
                            'suggestedMax' => 100,
                        ],
                    ],
                    'responsive' => true,
                ]
            ],
            [
                'vertical-bar',
                [
                    'scales' => [
                        'x' => [
                            'suggestedMin' => 0,
                            'suggestedMax' => 100,
                        ]
                    ],
                    'responsive' => true,
                ]
            ],
            [
                'horizontal-bar',
                [
                    'indexAxis' => 'y',
                    'scales' => [
                        'x' => [
                            'suggestedMin' => 0,
                            'suggestedMax' => 100,
                        ],
                    ],
                    'responsive' => true,
                ]
            ],
            [
                'pie',
                []
            ]
        ];
    }
    /**
     * @return array<int, array{int, string}>
     */
    public static function providerTypeValid(): array
    {
        return [
            ['line'],
            ['vertical-bar'],
            ['horizontal-bar'],
            ['pie']
        ];
    }

    public static function providerStyleByCompteSalaire(): array
    {
        return [
            [
                [
                    'depense' => [
                        'border' => 'rgb(255, 99, 132)',
                        'background' => 'rgb(255, 99, 132)'
                    ],
                    'capital' => [
                        'border' => 'rgb(22, 157, 150)',
                        'background' => 'rgb(22, 157, 150)'
                    ],
                ]
            ]
        ];
    }
}
