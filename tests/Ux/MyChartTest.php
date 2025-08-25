<?php

namespace App\Tests\Ux;

use App\Ux\MyChart;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class MyChartTest extends TestCase
{
    protected function setUp(): void {}

    protected function tearDown(): void {}

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
}
