<?php

namespace App\Tests\Controller\Admin\Crud\Quantity;

use App\Tests\Controller\Admin\Crud\Quantity\AbstractQuantityCrudTest;
use EasyCorp\Bundle\EasyAdminBundle\Test\Trait\CrudTestFormAsserts;

class NewQuantityControllerCrudTest extends AbstractQuantityCrudTest
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

    public function testPageNewQuantityAccessDeniedIfUserNotAuthenticated(): void
    {
        $this->client->request('GET', $this->generateNewFormUrl());

        $this->assertResponseStatusCodeSame(302);
    }

    /**
     * -----------------------------------------------------------------
     * --------------------------------utilisateur simple---------------
     * -----------------------------------------------------------------
     */
    public function testPageNewQuantitySuccessfullyIfUserAuthenticated(): void
    {
        $this->simulateAccessPageNewQuantitySuccessfullyWithUser();
    }

    /**
     * @dataProvider fieldsShowing
     */
    public function testFieldsShowingInPageNewQuantitySuccess($field): void
    {
        $this->simulateAccessPageNewQuantitySuccessfullyWithUser();

        $this->assertFormFieldExists($field);
    }
    /**
     * @dataProvider fieldsHiddenWithUserAuthenticated
     */
    public function testFieldsNotInPageNewQuantitySuccess($fieldHidden): void
    {
        $this->simulateAccessPageNewQuantitySuccessfullyWithUser();

        $this->assertFormFieldNotExists($fieldHidden);
    }

    /**
     * @dataProvider formDataInvalid
     */
    public function testCreateNewQuantityWithFormDataInvalid(array $formData, int $expected): void
    {
        $this->simulateAccessPageNewQuantitySuccessfullyWithUser();

        $this->simulateSubmitForm($formData);

        $numberErrorActual = $this->crawler->filter('.invalid-feedback')->count();
        $this->assertSame($expected, $numberErrorActual);
    }

    /**
     * @dataProvider formDataValid
     */
    public function testCreateNewQuantityWithFormDataValid(array $formData): void
    {
        $this->simulateAccessPageNewQuantitySuccessfullyWithUser();

        $this->simulateSubmitForm($formData);

        $this->assertResponseStatusCodeSame(302);
    }
    /**
     * @dataProvider formDataAlreadyExist
     */
    public function testCreateNewQuantityWithFormDataAlreadyExist(array $formData, int $expected): void
    {
        $this->simulateAccessPageNewQuantitySuccessfullyWithUser();

        $this->simulateSubmitForm($formData);

        $numberErrorActual = $this->crawler->filter('.invalid-feedback')->count();
        $this->assertSame($expected, $numberErrorActual);
    }
    /**
     * @dataProvider formDataValidButNomCategoryOwnerUserOther
     */
    public function testCreateNewQuantityWithFormDataValidWithOtherUser(array $formData): void
    {
        $this->simulateAccessPageNewQuantitySuccessfullyWithOtherUser();

        $this->simulateSubmitForm($formData);

        $this->assertResponseStatusCodeSame(302);
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
    private function simulateAccessPageNewQuantitySuccessfullyWithUser(): void
    {
        $this->logUser();
        $this->crawler = $this->client->request('GET', $this->generateNewFormUrl());

        $this->assertResponseIsSuccessful();
    }

    private function simulateAccessPageNewQuantitySuccessfullyWithOtherUser(): void
    {
        $this->logOtherUser();
        $this->crawler = $this->client->request('GET', $this->generateNewFormUrl());

        $this->assertResponseIsSuccessful();
    }

    private function simulateAccessPageNewCategorySuccessfullyWithAdmin(): void
    {
        $this->logAdmin();
        $this->crawler = $this->client->request('GET', $this->generateNewFormUrl());

        $this->assertResponseIsSuccessful();
    }

    private function simulateSubmitForm(array $formData)
    {
        $formName = $this->getFormEntity();

        $this->crawler = $this->client->request('GET', $this->generateNewFormUrl());

        $form = $this->crawler->filter(sprintf('form[name="%s"]', $formName))
            ->form([
                $formName => $formData
            ]);
        $this->crawler = $this->client->submit($form);
    }

    public static function fieldsShowing(): array
    {
        return [
            ['unite'],
        ];
    }

    public static function fieldsHiddenWithUserAuthenticated(): array
    {
        return [
            ['id'],
            ['owner']
        ];
    }

    public static function formDataInvalid(): array
    {
        return [
            'unite required' => [
                'formData' => [
                    'unite' => '',
                ],
                'expected' => 1
            ],
        ];
    }

    public function formDataValid(): array
    {
        return [
            [
                'formData' => [
                    'unite' => 'test',
                    'quantity' => 30.75
                ]
            ]
        ];
    }

    public function formDataValidButNomCategoryOwnerUserOther(): array
    {
        return [
            [
                'formData' => [
                    'unite' => 'alreadyExist',
                    'quantity' => 15
                ]
            ]
        ];
    }

    public function formDataAlreadyExist(): array
    {
        return [
            [
                'formData' => [
                    'unite' => 'alreadyExist',
                    'quantity' => 10.25
                ],
                'expected' => 1
            ]
        ];
    }
}
