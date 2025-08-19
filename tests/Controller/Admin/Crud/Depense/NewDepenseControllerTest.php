<?php

namespace App\Tests\Controller\Admin\Crud\Depense;

use EasyCorp\Bundle\EasyAdminBundle\Test\Trait\CrudTestFormAsserts;

class NewDepenseControllerTest extends AbstractDepenseCrudTest
{
    use CrudTestFormAsserts;

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
     * @dataProvider provideFormDataInvalid
     */
    public function testCreateDepenseWithFormDataInvalid(array $formData, int $expected): void
    {
        // dd($formData, $expected);
        $this->simulateUserAccessPageNewSuccessfully();

        $this->crawler = $this->client->request('GET', $this->generateNewFormUrl());
        $formName = $this->getFormEntity();
        $form = $this->crawler->filter(sprintf('form[name="%s"]', $formName))
            ->form($formData);

        $this->crawler = $this->client->submit($form);

        $numberActual = $this->crawler->filter('.invalid-feedback')->count();
        $this->assertSame($expected, $numberActual);
    }

    public function testCreateCapitalSuccessfully(): void
    {
        $this->simulateUserAccessPageNewSuccessfully();

        $this->crawler = $this->client->request('GET', $this->generateNewFormUrl());
        $nameForm = $this->getFormEntity();
        $form = $this->crawler->filter(sprintf('form[name="%s"]', $nameForm))
            ->form([
                $nameForm => [
                    'montant' => 25,
                    'ajout' => 15.25
                ]
            ]);
        $this->crawler = $this->client->submit($form);
        $this->assertResponseStatusCodeSame(302);
    }

    private function simulateAdminAccessPageNewSuccessfully(): void
    {
        $this->client->loginUser($this->getAdminAuthenticated());

        $this->client->request('GET', $this->generateNewFormUrl());
        $this->assertResponseIsSuccessful();
    }

    private function simulateUserAccessPageNewSuccessfully(): void
    {
        $this->client->loginUser($this->getSimpeUserAuthenticated());

        $this->client->request('GET', $this->generateNewFormUrl());
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
                        'unite' => 1
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
}
