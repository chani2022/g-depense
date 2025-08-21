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

    public function getDatasets(): array
    {
        return $this->datasets;
    }

    public function setDatasets(array $datasets): static
    {
        $this->datasets = $datasets;

        return $this;
    }

    public function getDepenses(?array $dates = null): array
    {
        $user = $this->tokenStorage->getToken()->getUser();
        $depenses = $this->depenseRepository->findDepensesWithCapital($user, $dates);
        $depensesTotal = $this->depenseRepository->getTotalDepenseAndCapitalInDateGivingByUser($user, $dates);

        $mergeDepenses = $this->arrayHelper->merge($depenses, $depensesTotal);

        // $depenses = $this->handleDepense($mergeDepenses);
        return $mergeDepenses;
    }

    public function handleDepense(array $depenses): array
    {
        $labels = [];
        $datasets = [];
        foreach ($depenses as $valeur) {
            if (is_array($valeur)) {
                $labels[] = $valeur['label'];
            }
        }

        return [
            'labels' => $labels,
            'datasets' => $datasets
        ];
    }
}
