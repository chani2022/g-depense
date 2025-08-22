<?php

namespace App\Tests\Ux;

use App\Ux\MyChart;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Symfony\UX\Chartjs\Model\Chart;

class MyChartTest extends TestCase
{
    private ?MyChart $myChart;

    protected function setUp(): void
    {
        $this->myChart = new MyChart('line');
    }

    protected function tearDown(): void
    {
        $this->myChart = null;
    }

    public function testGetType(): void
    {
        $typeExpected = 'line';
        $typeActual = $this->myChart->getType();

        $this->assertSame($typeExpected, $typeActual);
    }

    public function testSetDataChart(): void
    {
        $data = ['test'];

        $this->myChart->setData($data);

        $this->assertSame($data, $this->myChart->getData());
    }
    /**
     * @dataProvider providerGetType
     */
    public function testSetOptions(string $type, array $optionsExpected): void
    {
        $myChart = new MyChart($type);
        $optionsActual = $myChart->getOptions();
        $this->assertSame($optionsExpected, $optionsActual);
    }

    public static function providerGetType(): array
    {
        return [
            [
                Chart::TYPE_LINE,
                [
                    'scales' => [
                        'y' => [
                            'suggestedMin' => 0,
                            'suggestedMax' => 100,
                        ],
                        'responsive' => true
                    ],
                ]
            ]
        ];
    }
}
