<?php

namespace App\Ux;

use Symfony\UX\Chartjs\Model\Chart;

class MyChart
{
    private Chart $chart;

    /**
     * initialisation de mychart
     * @param string $type                              type de chart
     */
    public function __construct(string $type)
    {
        $this->chart = new Chart($type);

        $this->setOptionsByType($type);
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
     * @return void
     */
    private function setOptionsByType(string $type): void
    {
        $defaultOptions = [
            'scales' => [
                'responsive' => true
            ]
        ];
        $options = [];
        switch ($type) {
            case Chart::TYPE_LINE:
                $defaultOptions['scales']['y'] = [
                    'suggestedMin' => 0,
                    'suggestedMax' => 100,
                ];
                $options = $defaultOptions;
                break;
        }

        $this->chart->setOptions($options);
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
