<?php

namespace App\Ux;

use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Symfony\UX\Chartjs\Model\Chart;

class MyChart
{
    private Chart $chart;

    public function __construct(private ChartBuilderInterface $chartBuilder, string $type)
    {
        $this->chart = $this->chartBuilder->createChart($type);
    }

    public function getType(): string
    {
        return $this->chart->getType();
    }

    public function setData(array $data): self
    {
        $this->chart->setData($data);

        return $this;
    }

    public function getData(): array
    {
        return $this->chart->getData();
    }

    public function setOptions(array $options): self
    {
        $this->chart->setOptions($options);

        return $this;
    }

    public function getOptions(): array
    {
        return $this->chart->getOptions();
    }

    public function getChart(): Chart
    {
        return $this->chart;
    }

    public function setChart(Chart $chart): self
    {
        $this->chart = $chart;

        return $this;
    }
}
