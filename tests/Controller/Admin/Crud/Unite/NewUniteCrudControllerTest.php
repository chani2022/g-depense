<?php

namespace App\Tests\Controller\Admin\Crud\Unite;

use App\Tests\Controller\Admin\Crud\Unite\AbstractUniteCrudTest;
use EasyCorp\Bundle\EasyAdminBundle\Test\Trait\CrudTestFormAsserts;

class NewUniteControllerCrudTest extends AbstractUniteCrudTest
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

    public function testPageNewUniteAccessDeniedIfUserNotAuthenticated(): void
    {
        $this->client->request('GET', $this->generateNewFormUrl());

        $this->assertResponseStatusCodeSame(302);
    }

    /**
     * -----------------------------------------------------------------
     * --------------------------------utilisateur simple---------------
     * -----------------------------------------------------------------
     */
    public function testPageNewUniteSuccessfullyIfUserAuthenticated(): void
    {
        $this->simulateAccessPageNewUniteSuccessfullyWithUser();
    }

    /**
     * @dataProvider fieldsShowing
     */
    public function testFieldsShowingInPageNewUniteSuccess($field): void
    {
        $this->simulateAccessPageNewUniteSuccessfullyWithUser();

        $this->assertFormFieldExists($field);
    }
    /**
     * @dataProvider fieldsHiddenWithUserAuthenticated
     */
    public function testFieldsNotInPageNewUniteSuccess($fieldHidden): void
    {
        $this->simulateAccessPageNewUniteSuccessfullyWithUser();

        $this->assertFormFieldNotExists($fieldHidden);
    }

    /**
     * @dataProvider formDataInvalid
     */
    public function testCreateNewUniteWithFormDataInvalid(array $formData, int $expected): void
    {
        $this->simulateAccessPageNewUniteSuccessfullyWithUser();

        $this->simulateSubmitForm($formData);

        $numberErrorActual = $this->crawler->filter('.invalid-feedback')->count();
        $this->assertSame($expected, $numberErrorActual);
    }

    /**
     * @dataProvider formDataValid
     */
    public function testCreateNewUniteWithFormDataValidNotValueAlreadyExist(array $formData): void
    {
        $this->simulateAccessPageNewUniteSuccessfullyWithUser();

        $this->simulateSubmitForm($formData);

        $this->assertResponseStatusCodeSame(302);
    }
    /**
     * @dataProvider formDataAlreadyExist
     */
    public function testCreateNewUniteWithFormDataAlreadyExist(array $formData, int $expected): void
    {
        $this->simulateAccessPageNewUniteSuccessfullyWithUser();

        $this->simulateSubmitForm($formData);

        $numberErrorActual = $this->crawler->filter('.invalid-feedback')->count();
        $this->assertSame($expected, $numberErrorActual);
    }
    /**
     * @dataProvider formDataValidButUniteOwnerUserOther
     */
    public function testCreateNewUniteWithFormDataValidWithOtherUser(array $formData): void
    {
        $this->simulateAccessPageNewUniteSuccessfullyWithOtherUser();

        $this->simulateSubmitForm($formData);

        $this->assertResponseStatusCodeSame(302);
    }

    /**
     * -------------------------------------------------------------
     * ----------------------------Admin----------------------------
     * -------------------------------------------------------------
     */

    //simulation
    private function simulateAccessPageNewUniteSuccessfullyWithUser(): void
    {
        $this->logUser();
        $this->crawler = $this->client->request('GET', $this->generateNewFormUrl());

        $this->assertResponseIsSuccessful();
    }

    private function simulateAccessPageNewUniteSuccessfullyWithOtherUser(): void
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
                ]
            ]
        ];
    }

    public function formDataValidButUniteOwnerUserOther(): array
    {
        return [
            [
                'formData' => [
                    'unite' => 'alreadyExist',
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
                ],
                'expected' => 1
            ]
        ];
    }
}
