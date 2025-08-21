<?php

namespace App\Ux;

use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Symfony\UX\Chartjs\Model\Chart;

class MyChart
{
    private Chart $chart;

    /**
     * initialisation de mychart
     * @param ChartBuilderInterface $chartBuilder       constructeur de chart
     * @param string $type                              type de chart
     */
    public function __construct(private ChartBuilderInterface $chartBuilder, string $type)
    {
        $this->chart = $this->chartBuilder->createChart($type);
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->chart->getType();
    }
    /**
     * modification des données pour le chart
     * 
     * @param array<string, string[]|array{int, array{string, string|float[]}}>
     * @return self
     */
    public function setData(array $data): self
    {
        $this->chart->setData($data);

        return $this;
    }

    /**
     * @return array<string, string[]|array{int, array{string, string|float[]}}>
     */
    public function getData(): array
    {
        return $this->chart->getData();
    }

    /**
     * @return self
     */
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
