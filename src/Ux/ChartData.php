<?php

namespace App\Ux;

use App\Helper\ArrayHelper;
use App\Repository\DepenseRepository;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class ChartData
{
    const STYLE = [
        'depense' => [
            'border' => 'rgb(255, 99, 132)',
            'background' => 'rgb(255, 99, 132)'
        ],
        'capital' => [
            'border' => 'rgb(22, 157, 150)',
            'background' => 'rgb(22, 157, 150)'
        ],
    ];

    private array $labels = [];
    private array $datasets = [];

    public function __construct(
        private DepenseRepository $depenseRepository,
        private TokenStorageInterface $tokenStorage,
        private ArrayHelper $arrayHelper
    ) {}

    /**
     * @return string[]
     */
    public function getLabels(): array
    {
        return $this->labels;
    }
    /**
     * @param string[] $labels
     * @return static
     */
    public function setLabels(array $labels): static
    {
        $this->labels =  $labels;

        return $this;
    }
    /**
     * @return array<int, array{string, string|array}>
     */
    public function getDatasets(): array
    {
        return $this->datasets;
    }

    /**
     * @param array<int, array{string, string|array}> $datasets
     * @return static
     */
    public function setDatasets(array $datasets): static
    {
        $this->datasets = $datasets;

        return $this;
    }
    /**
     * Récuperation de depense et capital mensuel et depense et capital total par date donnée.
     * 
     * @param string[]|null         $dates
     * @return array<string|int, int|array{string, float|string}>
     */
    public function getDepenses(?array $dates = null): array
    {
        $user = $this->tokenStorage->getToken()->getUser();
        $depenses = $this->depenseRepository->findDepensesWithCapital($user, $dates);
        $depensesTotal = $this->depenseRepository->getTotalDepenseAndCapitalInDateGivingByUser($user, $dates);

        $mergeDepenses = [];
        foreach ($depensesTotal as $depenseTotal) {
            $mergeDepenses = $this->arrayHelper->merge($depenses, $depenseTotal);
        }

        return $mergeDepenses;
    }
    /**
     * Récuperation de label et données pour le chart.
     * 
     * @param array<string|int, int|array{string, float|string}>
     * @return array<string, string[]|array{int, array{int, array<string, string|float[]}}>
     */
    public function handleDepense(array $depenses): array
    {

        $labels = [];
        $datasets = [];
        foreach ($depenses as $depense) {
            if (is_array($depense)) {
                $labels[] = $depense['label'];
                $dataDepense = [
                    'label' => 'Depense mensuel',
                    'data' => [$depense['total_depense']],
                    'borderColor' => ChartData::STYLE['depense']['border'],
                    'backgroundColor' => ChartData::STYLE['depense']['background'],
                ];
                $dataCapital = [
                    'label' => 'Capital mensuel',
                    'data' => [$depense['total_capital']],
                    'borderColor' => ChartData::STYLE['capital']['border'],
                    'backgroundColor' => ChartData::STYLE['capital']['background'],
                ];

                if (count($datasets) == 0) {
                    $datasets[] = $dataDepense;
                    $datasets[] = $dataCapital;
                } else {
                    foreach ($datasets as $i => $dataset) {
                        if ($dataset['label'] == 'Depense mensuel') {
                            $datasets[$i]['data'][] = $depense['total_depense'];
                        } else {
                            $datasets[$i]['data'][] = $depense['total_capital'];
                        }
                    }
                }
            }
        }

        return [
            'labels' => $labels,
            'datasets' => $datasets
        ];
    }
}
