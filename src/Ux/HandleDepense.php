<?php

namespace App\Ux;

class HandleDepense
{
    /**
     * @param array<int, array{string, string|float}> $depenseAndCapitalMensuels
     * @return string[]
     */
    public function getLabels(array $depenseAndCapitalMensuels): array
    {
        $labels = [];
        foreach ($depenseAndCapitalMensuels as $depenseAndCapitalMensuel) {
            $labels[] = $depenseAndCapitalMensuel['label'];
        }

        return $labels;
    }
    /**
     * @param array<int, array{string, string|float}> $depenseAndCapitalMensuels
     * @return array<int, array{string, string|array}>
     */
    public function getDatasets(array $depenseAndCapitalMensuels): array
    {
        $datasets = [];
        foreach ($depenseAndCapitalMensuels as $depenseAndCapitalMensuel) {
            $dataDepense = [
                'label' => 'Depense mensuel',
                'data' => [$depenseAndCapitalMensuel['total_depense']],
                'borderColor' => MyChart::STYLE_BY_COMPTE_SALAIRE['depense']['border'],
                'backgroundColor' => MyChart::STYLE_BY_COMPTE_SALAIRE['depense']['background'],
            ];
            $dataCapital = [
                'label' => 'Capital mensuel',
                'data' => [$depenseAndCapitalMensuel['total_capital']],
                'borderColor' => MyChart::STYLE_BY_COMPTE_SALAIRE['capital']['border'],
                'backgroundColor' => MyChart::STYLE_BY_COMPTE_SALAIRE['capital']['background'],
            ];

            if (count($datasets) == 0) {
                $datasets[] = $dataDepense;
                $datasets[] = $dataCapital;
            } else {
                foreach ($datasets as $i => $dataset) {
                    if ($dataset['label'] == 'Depense mensuel') {
                        $datasets[$i]['data'][] = $depenseAndCapitalMensuel['total_depense'];
                    } else {
                        $datasets[$i]['data'][] = $depenseAndCapitalMensuel['total_capital'];
                    }
                }
            }
        }

        return $datasets;
    }
}
