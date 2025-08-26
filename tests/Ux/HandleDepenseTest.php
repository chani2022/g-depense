<?php

namespace App\Tests\Ux;

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
    public function testGetLabels(array $depenseCapitalMensuel): void
    {
        $labelExpected = ['label1', 'label2'];

        $labelActual = $this->handleDepense->getLabels($depenseCapitalMensuel);

        $this->assertSame($labelExpected, $labelActual);
    }

    /**
     * @return array<int, array{string, string|float}>
     */
    public static function providerDepenseAndCapital(): array
    {
        return [

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

        ];
    }
}
