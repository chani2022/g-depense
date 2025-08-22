<?php

namespace App\Tests\Ux;

use App\Ux\MyChart;
use Doctrine\Common\Cache\Psr6\InvalidArgument;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Symfony\UX\Chartjs\Model\Chart;

class MyChartTest extends TestCase
{
    protected function setUp(): void {}

    protected function tearDown(): void {}

    public function testTypeThrowException(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $myChart = new MyChart('exception');
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
    /**
     * @dataProvider providerGetType
     */
    public function testSetOptions(string $type, array $optionsExpected): void
    {
        $myChart = new MyChart($type);
        $optionsActual = $myChart->getOptions();
        $this->assertEquals($optionsExpected, $optionsActual);
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
            ],
            []
        ];
    }
}
