<?php

namespace App\Ux;

use InvalidArgumentException;
use Symfony\UX\Chartjs\Model\Chart;

class MyChart
{
    private Chart $chart;

    /**
     * initialisation de mychart
     * @param string $type   type de chart non standard
     */
    public function __construct(string $typeNotStandard)
    {
        $this->typethrowException($typeNotStandard);

        $typeStandard = $this->setTypeToStandard($typeNotStandard);
        $this->chart = new Chart($typeStandard);
        $this->setOptionsByType($typeNotStandard);
    }
    /**
     * @throws InvalidException
     */
    private function typeThrowException(string $typeNotStandard): void
    {
        $listTypeValid = ['line', 'vertical-bar', 'horizontal-bar'];

        if (!in_array($typeNotStandard, $listTypeValid)) {
            throw new InvalidArgumentException(sprintf('Invalid type for "%s", availables type are %s', $typeNotStandard, implode(', ', $listTypeValid)));
        }
    }
    /**
     * @param string $type  type non standard e.g: line, vertical-bar etc
     */
    private function setTypeToStandard(string $typeNotStandard): string
    {
        return match ($typeNotStandard) {
            'line' => Chart::TYPE_LINE,
            'vertical-bar' => Chart::TYPE_BAR,
            'horizontal-bar' => Chart::TYPE_BAR
        };
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
        $options = [
            'responsive' => true,
            'scales' => []
        ];
        switch ($type) {
            case 'line':
                $options['scales']['y'] = [
                    'suggestedMin' => 0,
                    'suggestedMax' => 100,
                ];
                break;
            case 'vertical-bar':
                $options['scales']['x'] = [
                    'suggestedMin' => 0,
                    'suggestedMax' => 100,
                ];
                break;
            case 'horizontal-bar':
                $options['indexAxis'] = 'y';
                $options['scales']['x'] = [
                    'suggestedMin' => 0,
                    'suggestedMax' => 100,
                ];
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
