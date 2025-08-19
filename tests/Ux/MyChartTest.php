<?php

namespace App\Tests\Ux;

use App\Ux\MyChart;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Symfony\UX\Chartjs\Model\Chart;

class MyChartTest extends TestCase
{
    /** @var MockObject|ChartBuilderInterface|null */
    private  $chartBuilder;
    private ?MyChart $myChart;

    protected function setUp(): void
    {
        /** @var MockObject|ChartBuilderInterface|null */
        $this->chartBuilder = $this->createMock(ChartBuilderInterface::class);
        $this->myChart = new MyChart($this->chartBuilder, 'line');
    }

    protected function tearDown(): void
    {
        $this->chartBuilder = null;
        $this->myChart = null;
    }

    public function testGetType(): void
    {
        $typeExpected = 'line';
        $chart = new Chart($typeExpected);
        $this->myChart->setChart($chart);
        $typeActual = $this->myChart->getType();

        $this->assertSame($typeExpected, $typeActual);
    }

    public function testSetData(): void
    {
        $data = ['test'];
        $mockChart = new Chart('line');
        $this->myChart->setChart($mockChart);
        $this->myChart->setData($data);

        $this->assertSame($data, $this->myChart->getData());
    }

    public function testSetOptions(): void
    {
        $options = ['options'];
        $chart = new Chart('line');
        $this->myChart->setChart($chart);

        $this->myChart->setOptions($options);


        $this->assertSame($options, $this->myChart->getOptions());
    }
}
