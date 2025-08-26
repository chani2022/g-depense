<?php

namespace App\Tests\Ux;

use App\Ux\HandleDepense;
use App\Ux\MyChart;
use PHPUnit\Framework\TestCase;

class HandleDepenseTest extends TestCase
{
    private ?HandleDepense $handleDepense;

    protected function setUp(): void
    {
        $this->handleDepense = new HandleDepense();
    }

    protected function tearDown(): void
    {
        $this->handleDepense = null;
    }

    /**
     * @dataProvider providerDepenseAndCapital
     */
    public function testGetLabels(array $depenseCapitalMensuel, array $labelExpected, array $datasetsExpected): void
    {
        $labelActual = $this->handleDepense->getLabels($depenseCapitalMensuel);

        $this->assertSame($labelExpected, $labelActual);
    }

    /**
     * @dataProvider providerDepenseAndCapital
     */
    public function testGetDatasets(array $depenseCapitalMensuel, array $labelExpected, array $datasetsExpected): void
    {
        $datasetsActual = $this->handleDepense->getDatasets($depenseCapitalMensuel);

        $this->assertSame($datasetsExpected, $datasetsActual);
    }

    /**
     * @return array<int, array{string, string|float}>
     */
    public static function providerDepenseAndCapital(): array
    {
        return [
            [
                'depense' => [
                    [
                        'label' => 'label1',
                        'total_depense' => 15.20,
                        'total_capital' => 30.50
                    ],
                    [
                        'label' => 'label2',
                        'total_depense' => 10.25,
                        'total_capital' => 32.75
                    ]
                ],
                'expectedLabels' => ['label1', 'label2'],
                'expectedDatasets' => [
                    [
                        'label' => 'Depense mensuel',
                        'data' => [15.2, 10.25],
                        'borderColor' => MyChart::STYLE_BY_COMPTE_SALAIRE['depense']['border'],
                        'backgroundColor' => MyChart::STYLE_BY_COMPTE_SALAIRE['depense']['background'],
                    ],
                    [
                        'label' => 'Capital mensuel',
                        'data' => [30.5, 30.75],
                        'borderColor' => MyChart::STYLE_BY_COMPTE_SALAIRE['capital']['border'],
                        'backgroundColor' => MyChart::STYLE_BY_COMPTE_SALAIRE['capital']['background'],
                    ],
                ]
            ]

        ];
    }
}
