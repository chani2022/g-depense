<?php

namespace App\Tests\Ux;

use App\Ux\MyChart;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Symfony\UX\Chartjs\Model\Chart;

class ChartTest extends TestCase
{
    private  $chartBuilder;
    private ?MyChart $myChart;

    protected function setUp(): void
    {
        /** @var MockObject|ChartBuilderInterface|null */
        $this->chartBuilder = $this->createMock(ChartBuilderInterface::class);
        $this->myChart = new MyChart($this->chartBuilder);
    }

    public function testCreateChart(): void
    {
        $type = 'line';
        $mockChart = $this->createMock(Chart::class);
        $this->chartBuilder
            ->expects($this->once())
            ->method('createChart')
            ->with($type)
            ->willReturn($mockChart);

        $this->myChart->createChart($type);
    }

    protected function tearDown(): void
    {
        $this->chartBuilder = null;
        $this->myChart = null;
    }
}
