<?php

namespace App\Ux;

use InvalidArgumentException;
use Symfony\UX\Chartjs\Model\Chart;

class MyChart
{
    private Chart $chart;
    private ?string $title;

    /**
     * initialisation de mychart
     * @param string $typeNotStandard   type de chart non standard e.g: line, horizontal-bar, vertical-bar etc
     * @param string $title             titre du chart
     */
    public function __construct(string $typeNotStandard, ?string $title = null)
    {
        $this->typethrowException($typeNotStandard);
        $typeStandard = $this->setTypeToStandard($typeNotStandard);
        $this->setTitle($title);
        $this->chart = (new Chart($typeStandard));
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
     * @return string|null  titre du chart
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * @param string|null $title titre du chart
     * @return static
     */
    public function setTitle(?string $title): static
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Type de chart
     * 
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
        $plugins = [
            'title' => [
                'display' => true,
                'text' => $this->title
            ]
        ];

        $suggest = [
            'suggestedMin' => 0,
            'suggestedMax' => 100
        ];

        $scales = $type == 'line' ? ['y' => $suggest] : ['x' => $suggest];

        $options = [
            'responsive' => true,
            'scales' => $scales
        ];

        if ($type == 'horizontal-bar') {
            $options['indexAxis'] = 'y';
        }

        if ($this->title) {
            $options['plugins'] = $plugins;
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
