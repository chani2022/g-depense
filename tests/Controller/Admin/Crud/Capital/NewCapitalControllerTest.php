<?php

namespace App\Tests\Controller\Admin\Crud\Capital;


class NewCapitalControllerTest extends AbstractCapitalCrudTest
{
    public function testPageNewCapitalSuccessfullyWithAdmin(): void
    {
        $this->simulateAdminAccessPageNewSuccessfully();
    }

    public function testPageNewCapitalSuccessfullyWithUser(): void
    {
        $this->simulateUserAccessPageNewSuccessfully();
    }

    // /**
    //  * @dataProvider provideFormDataInvalid
    //  */
    // public function testCreateCompteSalaireAlreadyExistAndBlank(array $formData, int $expected): void
    // {
    //     $this->simulateUserAccessPageNewSuccessfully();

    //     $this->crawler = $this->client->request('GET', $this->generateNewFormUrl());
    //     $nameForm = $this->getFormEntity();
    //     $form = $this->crawler->filter(sprintf('form[name="%s"]', $nameForm))
    //         ->form([
    //             $nameForm => $formData
    //         ]);
    //     $this->crawler = $this->client->submit($form);

    //     $numberActual = $this->crawler->filter('.invalid-feedback')->count();
    //     $this->assertSame($expected, $numberActual);
    // }

    // public function testCreateCompteSalaireSuccessfully(): void
    // {
    //     $this->simulateUserAccessPageNewSuccessfully();

    //     $this->crawler = $this->client->request('GET', $this->generateNewFormUrl());
    //     $nameForm = $this->getFormEntity();
    //     $form = $this->crawler->filter(sprintf('form[name="%s"]', $nameForm))
    //         ->form([
    //             $nameForm => [
    //                 'dateDebutCompte' => '2024-03-01',
    //                 'dateFinCompte' => '2024-03-15'
    //             ]
    //         ]);
    //     $this->crawler = $this->client->submit($form);
    //     $this->assertResponseStatusCodeSame(302);
    // }

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

    // public static function fieldsHidden(): array
    // {
    //     return [
    //         ['id'],
    //         ['owner']
    //     ];
    // }

    // public static function provideFormDataInvalid(): array
    // {
    //     return [
    //         'date debut et fin dans une même compte' => [
    //             'data' => [
    //                 'dateDebutCompte' => '2024-01-02',
    //                 'dateFinCompte' => '2024-01-14'
    //             ],
    //             'expected' => 2
    //         ],
    //         'date debut dans un compte' => [
    //             'data' => [
    //                 'dateDebutCompte' => '2024-01-02',
    //                 'dateFinCompte' => '2024-06-14'
    //             ],
    //             'expected' => 1
    //         ],
    //         'date fin dans un compte' => [
    //             'data' => [
    //                 'dateDebutCompte' => '2024-06-02',
    //                 'dateFinCompte' => '2024-01-14'
    //             ],
    //             'expected' => 1
    //         ],
    //         'date debut et fin vide' => [
    //             'data' => [
    //                 'dateDebutCompte' => '',
    //                 'dateFinCompte' => ''
    //             ],
    //             'expected' => 2
    //         ],
    //         'date debut vide' => [
    //             'data' => [
    //                 'dateDebutCompte' => '',
    //                 'dateFinCompte' => '2024-07-10'
    //             ],
    //             'expected' => 1
    //         ],
    //         'date fin vide' => [
    //             'data' => [
    //                 'dateDebutCompte' => '2024-05-20',
    //                 'dateFinCompte' => ''
    //             ],
    //             'expected' => 1
    //         ]
    //     ];
    // }
}
