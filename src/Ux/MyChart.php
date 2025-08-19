<?php

namespace App\Ux;

use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Symfony\UX\Chartjs\Model\Chart;

class MyChart
{
    public function __construct(private ChartBuilderInterface $chartBuilder, private Chart $chart) {}

    public function createChart(string $type): self
    {
        $this->chart = $this->chartBuilder->createChart($type);

        return $this;
    }

    public function setData(array $data): self
    {
        $this->chart->setData($data);

        return $this;
    }

    public function setOptions(array $options): self
    {
        $this->chart->setOptions($options);

        return $this;
    }
}
