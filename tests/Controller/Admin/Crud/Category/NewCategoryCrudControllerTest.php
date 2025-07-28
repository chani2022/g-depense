<?php

namespace App\Tests\Controller\Admin\Crud\Category;

use App\Tests\Controller\Admin\Crud\Category\AbstractCategoryCrudTest;
use EasyCorp\Bundle\EasyAdminBundle\Test\Trait\CrudTestFormAsserts;
use Hautelook\AliceBundle\PhpUnit\RefreshDatabaseTrait;

class NewCategoryControllerCrudTest extends AbstractCategoryCrudTest
{
    use CrudTestFormAsserts;

    public function testPageNewCategoryAccessDeniedIfUserNotAuthenticated(): void
    {
        $this->client->request('GET', $this->generateNewFormUrl());

        $this->assertResponseStatusCodeSame(302);
    }

    /**
     * -----------------------------------------------------------------
     * --------------------------------utilisateur simple---------------
     * -----------------------------------------------------------------
     */
    public function testPageNewCategorySuccessfullyIfUserAuthenticated(): void
    {
        $this->simulateAccessPageNewCategorySuccessfullyWithUser();
    }

    /**
     * @dataProvider fieldsShowing
     */
    public function testFieldsShowingInPageNewCategorySuccess($field): void
    {
        $this->simulateAccessPageNewCategorySuccessfullyWithUser();

        $this->assertFormFieldExists($field);
    }

    public function testFieldsNotInPageNewCategorySuccess(): void
    {
        $this->simulateAccessPageNewCategorySuccessfullyWithUser();

        $this->assertFormFieldNotExists('id');
    }

    public function testAssertPropertiesRequired(array $formData, int $expected): void {}

    /**
     * @dataProvider formDataInvalid
     */
    public function testCreateNewCategoryWithFormDataInvalid(array $formData, int $expected): void
    {
        $this->simulateAccessPageNewCategorySuccessfullyWithUser();

        // $formName = $this->getFormEntity();

        // $this->crawler = $this->client->request('GET', $this->generateNewFormUrl());

        // $form = $this->crawler->filter(sprintf('form[name="%s"]', $formName))
        //     ->form([
        //         $formName => [
        //             'nom' => 'alreadyExist'
        //         ]
        //     ]);
        // $this->crawler = $this->client->submit($form);
        $this->simulateSubmitForm($formData);

        $numberErrorActual = $this->crawler->filter('.invalid-feedback')->count();
        $this->assertSame($expected, $numberErrorActual);
    }

    /**
     * -------------------------------------------------------------
     * ----------------------------Admin----------------------------
     * -------------------------------------------------------------
     */
    public function testPageNewCategorySuccessfullyIfAdminAuthenticated(): void
    {
        $this->simulateAccessPageNewCategorySuccessfullyWithAdmin();
    }

    //simulation
    private function simulateAccessPageNewCategorySuccessfullyWithUser(): void
    {
        $this->logUser();
        $this->crawler = $this->client->request('GET', $this->generateNewFormUrl());

        $this->assertResponseIsSuccessful();
    }

    private function simulateAccessPageNewCategorySuccessfullyWithAdmin(): void
    {
        $this->logAdmin();
        $this->crawler = $this->client->request('GET', $this->generateNewFormUrl());

        $this->assertResponseIsSuccessful();
    }

    private function simulateSubmitForm()
    {
        $formName = $this->getFormEntity();

        $this->crawler = $this->client->request('GET', $this->generateNewFormUrl());

        $form = $this->crawler->filter(sprintf('form[name="%s"]', $formName))
            ->form([
                $formName => [
                    'nom' => 'alreadyExist'
                ]
            ]);
        $this->crawler = $this->client->submit($form);
    }

    public static function fieldsShowing(): array
    {
        return [
            ['nom'],
            ['prix']
        ];
    }

    public static function formDataInvalid(): array
    {
        return [
            'nom and prix required' => [
                'formData' => [
                    'nom' => '',
                    'prix' => ''
                ],
                'expected' => 2
            ],
            // 'nom category already exist' => [
            //     'formData' => [
            //         'nom' => 'alreadyExist',
            //         'prix' => 5.25
            //     ],
            //     'expected' => 1
            // ]
        ];
    }
}
