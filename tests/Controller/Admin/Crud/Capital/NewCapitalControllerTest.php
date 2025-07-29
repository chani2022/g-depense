<?php

namespace App\Tests\Controller\Admin\Crud\Capital;

use EasyCorp\Bundle\EasyAdminBundle\Test\Trait\CrudTestFormAsserts;

class NewCapitalControllerTest extends AbstractCapitalCrudTest
{
    use CrudTestFormAsserts;

    protected function setUp(): void
    {
        parent::setUp();
    }

    public function testAccessDeniedPageNewCapitalIfUserNotAuthenticated(): void
    {
        $this->client->request('GET', $this->generateNewFormUrl());
        $this->assertResponseStatusCodeSame(302);
    }

    public function testPageNewCapitalSuccessfullyWithAdmin(): void
    {
        $this->simulateAdminAccessPageNewSuccessfully();
    }

    public function testPageNewCapitalSuccessfullyWithUser(): void
    {
        $this->simulateUserAccessPageNewSuccessfully();
    }

    /**
     * @dataProvider provideFormDataInvalid
     */
    public function testCreateCapitalWithFormDataInvalid(array $formData, int $expected): void
    {
        $this->simulateUserAccessPageNewSuccessfully();

        $this->crawler = $this->client->request('GET', $this->generateNewFormUrl());
        $nameForm = $this->getFormEntity();
        $form = $this->crawler->filter(sprintf('form[name="%s"]', $nameForm))
            ->form([
                $nameForm => $formData
            ]);
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
            'montant et ajout chaine de caractère' => [
                'data' => [
                    'montant' => '2024-01-02',
                    'ajout' => '2024-01-14'
                ],
                'expected' => 2
            ],
            'montant chaine de caractère' => [
                'data' => [
                    'montant' => '2024-01-02',
                    'ajout' => 25
                ],
                'expected' => 1
            ],
            'ajout chaine de caractère' => [
                'data' => [
                    'montant' => 15,
                    'ajout' => 'test'
                ],
                'expected' => 1
            ]
        ];
    }

    protected function tearDown(): void
    {
        parent::tearDown();
    }
}
