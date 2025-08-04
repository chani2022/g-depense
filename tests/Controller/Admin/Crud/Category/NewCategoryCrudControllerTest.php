<?php

namespace App\Tests\Controller\Admin\Crud\Category;

use App\Tests\Controller\Admin\Crud\Category\AbstractCategoryCrudTest;
use App\Tests\Trait\QuantityTrait;
use EasyCorp\Bundle\EasyAdminBundle\Test\Trait\CrudTestFormAsserts;

class NewCategoryControllerCrudTest extends AbstractCategoryCrudTest
{
    use CrudTestFormAsserts;
    use QuantityTrait;

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
     * @dataProvider formDataValidWithoutQuantity
     */
    public function testCreateNewCategoryWithFormDataValidWidthoutQuantitySuccess(array $formData): void
    {
        $this->simulateAccessPageNewCategorySuccessfullyWithUser();

        $this->simulateSubmitForm($formData);

        $this->assertResponseStatusCodeSame(302);
    }
    /**
     * @dataProvider formDataValidWithQuantity
     */
    public function testCreateNewCategoryWithFormDataValidWithQuantitySuccess(array $formData): void
    {
        $this->simulateAccessPageNewCategorySuccessfullyWithUser();

        $this->simulateSubmitForm($formData);

        $this->assertResponseStatusCodeSame(302);
    }
    /**
     * @dataProvider formDataValidButNomCategoryOwnerUserOther
     */
    public function testCreateNewCategoryWithFormDataValidWithOtherUserSuccess(array $formData): void
    {
        $this->simulateAccessPageNewCategorySuccessfullyWithOtherUser();

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
            'nom required' => [
                'formData' => [
                    'nom' => null,
                    'prix' => 15.25
                ],
                'expected' => 1
            ],
            'prix required' => [
                'formData' => [
                    'nom' => 'nom',
                    'prix' => null
                ],
                'expected' => 1
            ],
            'prix must positif' => [
                'formData' => [
                    'nom' => 'nom',
                    'prix' => -10
                ],
                'expected' => 1
            ],
            'prix must decimal' => [
                'formData' => [
                    'nom' => 'nom',
                    'prix' => 'test'
                ],
                'expected' => 1
            ],
            'nom category already exist' => [
                'formData' => [
                    'nom' => 'alreadyExist',
                    'prix' => 5.25
                ],
                'expected' => 1
            ]
        ];
    }

    public static function formDataValidWithoutQuantity(): array
    {
        return [
            [
                'formData' => [
                    'nom' => 'new category',
                    'prix' => 15.25,
                    'isVital' => true
                ]
            ]
        ];
    }

    public function formDataValidWithQuantity(): array
    {
        return [
            [
                'formData' => [
                    'nom' => 'new category',
                    'prix' => 15.25,
                    'isVital' => true,
                    'quantity' => $this->getQuantity()
                ]
            ]
        ];
    }

    public static function formDataValidButNomCategoryOwnerUserOther(): array
    {
        return [
            [
                'formData' => [
                    'nom' => 'alreadyExist',
                    'prix' => 15.25,
                    'isVital' => true
                ]
            ]
        ];
    }
}
