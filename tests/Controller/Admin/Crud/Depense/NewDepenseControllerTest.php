<?php

namespace App\Tests\Controller\Admin\Crud\Depense;

use App\Tests\Trait\CategoryTrait;
use App\Tests\Trait\UniteTrait;
use EasyCorp\Bundle\EasyAdminBundle\Test\Trait\CrudTestFormAsserts;

class NewDepenseControllerTest extends AbstractDepenseCrudTest
{
    use CrudTestFormAsserts;
    use CategoryTrait;
    use UniteTrait;

    protected function setUp(): void
    {
        parent::setUp();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
    }

    public function testAccessDeniedPageNewDepenseIfUserNotAuthenticated(): void
    {
        $this->client->request('GET', $this->generateNewFormUrl());
        $this->assertResponseStatusCodeSame(302);
    }

    public function testPageNewDepenseSuccessfullyWithAdmin(): void
    {
        $this->simulateAdminAccessPageNewSuccessfully();
    }

    public function testPageNewDepenseSuccessfullyWithUser(): void
    {
        $this->simulateUserAccessPageNewSuccessfully();
    }
    /**
     * @dataProvider provideFieldShowing
     */
    public function testOnlyFieldShowingInNewDepense(string $field): void
    {
        $this->simulateUserAccessPageNewSuccessfully();

        $this->assertFormFieldExists($field);
    }

    /**
     * @dataProvider provideFieldNotShowing
     */
    public function testOnlyFieldNotShowingInNewDepense(string $field): void
    {
        $this->simulateUserAccessPageNewSuccessfully();

        $this->assertFormFieldNotExists($field);
    }
    /**
     * @dataProvider providerFormNameSelect
     */
    public function testCountOptionsInSelectInNewDepenseIfUserAuthenticated(string $formNameSelect, int $expectedCount): void
    {
        $this->simulateUserAccessPageNewSuccessfully();
        $formName = $this->getFormEntity();
        $select = 'select[name="' . $formName . '[' . $formNameSelect . ']"]';
        $formSelect = $this->crawler->filter($select);

        $numberOptions = $formSelect->filter('option:not([value=""])')->count();

        $this->assertSame($expectedCount, $numberOptions);
    }

    /**
     * @dataProvider provideFormDataInvalid
     */
    public function testCreateDepenseWithFormDataInvalid(array $formData, int $expected): void
    {
        $this->simulateUserAccessPageNewSuccessfully();

        $this->crawler = $this->client->request('GET', $this->generateNewFormUrl());
        $formName = $this->getFormEntity();
        $form = $this->crawler->filter(sprintf('form[name="%s"]', $formName))
            ->form($formData);

        $this->crawler = $this->client->submit($form);

        $numberActual = $this->crawler->filter('.invalid-feedback')->count();
        $this->assertSame($expected, $numberActual);
    }

    public function testCreateNewDepenseSuccessfully(): void
    {
        $this->simulateUserAccessPageNewSuccessfully();

        $nameForm = $this->getFormEntity();
        $form = $this->crawler->filter(sprintf('form[name="%s"]', $nameForm))
            ->form([
                $nameForm => [
                    'nomDepense' => 'exemple',
                    'prix' => 15.25,
                    'quantite' => 12,
                    'category' => $this->getCategory()->getId(),
                    'unite' => $this->getUnite()->getId(),
                    'vital' => true
                ]
            ]);
        $this->crawler = $this->client->submit($form);
        $this->assertResponseStatusCodeSame(302);
    }

    private function simulateAdminAccessPageNewSuccessfully(): void
    {
        $this->client->loginUser($this->getAdminAuthenticated());

        $this->crawler = $this->client->request('GET', $this->generateNewFormUrl());
        $this->assertResponseIsSuccessful();
    }

    private function simulateUserAccessPageNewSuccessfully(): void
    {
        $this->client->loginUser($this->getSimpeUserAuthenticated());

        $this->crawler = $this->client->request('GET', $this->generateNewFormUrl());
        $this->assertResponseIsSuccessful();
    }

    public static function provideFormDataInvalid(): array
    {
        return [
            'nom de la depense , prix,  quantite, category required' => [
                'data' => [
                    'Depense' => [
                        'nomDepense' => null,
                        'prix' => null,
                        'quantite' => null,
                        'category' => 1,
                        'unite' => 1,
                        'vital' => false
                    ]
                ],
                'expected' => 3
            ],
        ];
    }

    public static function provideFieldShowing(): array
    {
        return [
            ['category'],
        ];
    }

    public static function provideFieldNotShowing(): array
    {
        return [
            ['compteSalaire.dateDebutCompte'],
            ['compteSalaire.dateFinCompte'],
            ['category.nom'],
            ['category.prix'],
            ['category.quantity.quantity'],
        ];
    }
    /**
     * @return array<string, array{string, string|int}>
     */
    public static function providerFormNameSelect(): array
    {
        return [
            'form category' => [
                'formName' => 'category',
                'expected' => 2
            ],
            'form unite' => [
                'formName' => 'unite',
                'expected' => 1
            ],
        ];
    }
}
