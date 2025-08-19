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
    /** @var MockObject|Chart|null */
    private $mockChart;
    private ?MyChart $myChart;

    protected function setUp(): void
    {
        /** @var MockObject|ChartBuilderInterface|null */
        $this->chartBuilder = $this->createMock(ChartBuilderInterface::class);
        /** @var MockObject|Chart|null */
        $this->mockChart = $this->createMock(Chart::class);
        $this->myChart = new MyChart($this->chartBuilder, $this->mockChart);
    }

    protected function tearDown(): void
    {
        $this->chartBuilder = null;
        $this->myChart = null;
        $this->mockChart = null;
    }

    public function testCreateChart(): void
    {
        $type = 'line';
        $this->chartBuilder
            ->expects($this->once())
            ->method('createChart')
            ->with($type)
            ->willReturn($this->mockChart);

        $this->myChart->createChart($type);
    }

    public function testSetData(): void
    {
        $data = ['test'];

        $this->mockChart
            ->expects($this->once())
            ->method('setData')
            ->with($data)
            ->willReturnSelf();

        $this->mockChart
            ->method('getData')
            ->willReturn($data);

        $this->myChart->setData($data);

        $this->assertSame($data, $this->mockChart->getData());
    }

    public function testSetOptions(): void
    {
        $options = ['options'];

        $this->mockChart
            ->expects($this->once())
            ->method('setOptions')
            ->with($options)
            ->willReturnSelf();

        $this->mockChart
            ->method('getOptions')
            ->willReturn($options);

        $this->myChart->setOptions($options);

        $this->assertSame($options, $this->mockChart->getOptions());
    }
}
