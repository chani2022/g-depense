<?php

namespace App\Tests\Controller\Admin\Crud\Category;

use App\Tests\Controller\Admin\Crud\Category\AbstractCategoryCrudTest;
use EasyCorp\Bundle\EasyAdminBundle\Test\Trait\CrudTestFormAsserts;

class NewCategoryControllerCrudTest extends AbstractCategoryCrudTest
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
    /**
     * @dataProvider fieldsNotShowingIfUserAuthenticated
     */
    public function testFieldsShowingInPageNewCategoryIfUserAuthenthenticated(string $field): void
    {
        $this->simulateAccessPageNewCategorySuccessfullyWithUser();

        $this->assertFormFieldNotExists($field);
    }

    public function testFieldsNotInPageNewCategorySuccess(): void
    {
        $this->simulateAccessPageNewCategorySuccessfullyWithUser();

        $this->assertFormFieldNotExists('id');
    }

    /**
     * @dataProvider formDataInvalid
     */
    public function testCreateNewCategoryWithFormDataInvalid(array $formData, int $expected): void
    {
        $this->simulateAccessPageNewCategorySuccessfullyWithUser();

        $this->simulateSubmitForm($formData);

        $numberErrorActual = $this->crawler->filter('.invalid-feedback')->count();
        $this->assertSame($expected, $numberErrorActual);
    }

    /**
     * @dataProvider formDataValidButNomCategoryOwnerUserOther
     */
    public function testCreateNewCategoryWithDataAlreadyExistButWithOtherUserSuccess(array $formData): void
    {
        $this->simulateAccessPageNewCategorySuccessfullyWithOtherUser();

        $this->simulateSubmitForm($formData);

        $this->assertResponseStatusCodeSame(302);
    }

    /**
     * @dataProvider formDataValid
     */
    public function testCreateNewCategoryWithUserAuthenticatedSuccess(array $formData): void
    {
        $this->simulateAccessPageNewCategorySuccessfullyWithUser();

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

    /**
     * @dataProvider provideFieldShowingAdminAuthenticated
     */
    public function testFieldShowingInPageNewCategoryIfAdminAuthenticated(string $field): void
    {
        $this->simulateAccessPageNewCategorySuccessfullyWithAdmin();

        $this->assertFormFieldExists($field);
    }

    /**
     * @dataProvider formDataInValidAdminAuthenticated
     */
    public function testCreateNewCategoryWithDataInvalidAdminAuthenticatedSuccess(array $formData, int $expected): void
    {
        $this->simulateAccessPageNewCategorySuccessfullyWithAdmin();

        $this->simulateSubmitForm($formData);

        $numberErrorActual = $this->crawler->filter('.invalid-feedback')->count();
        $this->assertSame($expected, $numberErrorActual);
    }

    /**
     * @dataProvider formDataValidAdminAuthenticated
     */
    public function testCreateNewCategoryWithAdminAuthenticatedSuccess(array $formData): void
    {
        $formData['owner'] = $this->getSimpeUserAuthenticated()->getId();
        $this->simulateAccessPageNewCategorySuccessfullyWithAdmin();

        $this->simulateSubmitForm($formData);

        $this->assertResponseStatusCodeSame(302);
    }

    //simulation
    private function simulateAccessPageNewCategorySuccessfullyWithUser(): void
    {
        $this->logUser();
        $this->crawler = $this->client->request('GET', $this->generateNewFormUrl());

        $this->assertResponseIsSuccessful();
    }

    private function simulateAccessPageNewCategorySuccessfullyWithOtherUser(): void
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
            ['nom'],
        ];
    }

    public static function fieldsNotShowingIfUserAuthenticated(): array
    {
        return [
            ['owner.imageName']
        ];
    }

    public static function formDataInvalid(): array
    {
        return [
            'nom and prix required' => [
                'formData' => [
                    'nom' => '',
                ],
                'expected' => 1
            ]
        ];
    }

    public static function formDataValidButNomCategoryOwnerUserOther(): array
    {
        return [
            [
                'formData' => [
                    'nom' => 'alreadyExist',
                ]
            ]
        ];
    }

    public static function formDataValid(): array
    {
        return [
            [
                'formData' => [
                    'nom' => 'not exist',
                ]
            ]
        ];
    }

    public static function provideFieldShowingAdminAuthenticated(): array
    {
        return [
            ['nom'],
            ['owner']
        ];
    }

    public static function formDataInValidAdminAuthenticated(): array
    {
        return [
            [
                'formData' => [
                    'nom' => '',
                    'owner' => ''
                ],
                'expected' => 2
            ]
        ];
    }

    public static function formDataValidAdminAuthenticated(): array
    {
        return [
            [
                'formData' => [
                    'nom' => 'not exist',
                    'owner' => 'user'
                ]
            ]
        ];
    }
}
