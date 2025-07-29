<?php

namespace App\Tests\Controller\Admin\Crud\Capital;

use App\Tests\Controller\Admin\Crud\Capital\AbstractCapitalCrudTest;
use EasyCorp\Bundle\EasyAdminBundle\Test\Trait\CrudTestIndexAsserts;

class IndexCapitalControllerTest extends AbstractCapitalCrudTest
{
    use CrudTestIndexAsserts;

    protected function setUp(): void
    {
        parent::setUp();
    }

    public function testAccessDeniedPageIndexCapitalIfUserNotAuthenticated(): void
    {
        $this->client->request('GET', $this->generateIndexUrl());
        $this->assertResponseStatusCodeSame(302);
    }

    public function testIndexPageCapitalAccessUserSuccessfully(): void
    {
        $this->simulateUserAccessPageIndexCapitalSuccessfully();
    }

    public function testIndexPageCapitalAccessAdminSuccessfully(): void
    {
        $this->simulateUserAccessPageIndexCapitalSuccessfully();
    }

    public function testShowOnlyCapitalsOwnerIfUserAuthenticated(): void
    {
        $this->simulateUserAccessPageIndexCapitalSuccessfully();
        $this->assertIndexPageEntityCount(2);
    }

    public function testShowAllCapitalIfAdminAuthenticated(): void
    {
        $this->simulateAdminAccessPageIndexCapitalSuccessfully();
        $this->assertIndexPageEntityCount(3);
    }

    private function simulateUserAccessPageIndexCapitalSuccessfully(): void
    {
        $this->client->loginUser($this->getSimpeUserAuthenticated());

        $this->client->request('GET', $this->generateIndexUrl());
        $this->assertResponseIsSuccessful();
    }

    private function simulateAdminAccessPageIndexCapitalSuccessfully(): void
    {
        $this->client->loginUser($this->getAdminAuthenticated());

        $this->client->request('GET', $this->generateIndexUrl());
        $this->assertResponseIsSuccessful();
    }

    /**
     * @dataProvider fieldsHidden
     */
    public function testIndexPageCapitalFieldsHidden(string $field): void
    {
        $this->simulateUserAccessPageIndexCapitalSuccessfully();

        $this->assertIndexColumnNotExists($field);
    }

    /**
     * @dataProvider fieldsShowing
     */
    public function testIndexPageCapitalFieldsShowing(string $field): void
    {
        $this->simulateUserAccessPageIndexCapitalSuccessfully();

        $this->assertIndexColumnExists($field);
    }

    public static function fieldsHidden(): array
    {
        return [
            ['id'],
        ];
    }

    public static function fieldsShowing(): array
    {
        return [
            ['montant'],
            ['ajout'],
            ['compteSalaire.owner.imageName']
        ];
    }
    /**
     * @return array<array{string, string}>
     */
    public static function userAccessDenied(): array
    {
        return [
            ['anonymous'],
            ['roleUser']
        ];
    }

    protected function tearDown(): void
    {
        parent::tearDown();
    }
}
