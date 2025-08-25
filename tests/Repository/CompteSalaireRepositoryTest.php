<?php

namespace App\Repository;

use Hautelook\AliceBundle\PhpUnit\RefreshDatabaseTrait;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use App\Entity\CompteSalaire;
use App\Entity\User;
use App\Tests\Trait\UserAuthenticatedTrait;
use DateTime;

class CompteSalaireRepositoryTest extends KernelTestCase
{
    use RefreshDatabaseTrait;
    use UserAuthenticatedTrait;

    private ?CompteSalaireRepository $compteSalaireRepository;
    private ?User $userAuthenticated;

    protected function setUp(): void
    {
        parent::setUp();
        static::bootKernel();
        $this->compteSalaireRepository = $this->getContainer()->get(CompteSalaireRepository::class);
        $this->userAuthenticated = $this->getSimpeUserAuthenticated();
    }

    public function testGetCompteSalaireByDateReturnNull(): void
    {
        /** @var CompteSalaire|null */
        $compteSalaire = $this->compteSalaireRepository->getCompteSalaireByDate($this->userAuthenticated, new DateTime('2025-02-15'));
        $this->assertNull($compteSalaire);
    }
    /**
     * @dataProvider provideDate
     */
    public function testGetCompteSalaireByDateReturnCompteSalaire(string $dateSearch, string $dateDebutExpected, string $dateFinExpected): void
    {
        /** @var CompteSalaire|null */
        $compteSalaire = $this->compteSalaireRepository->getCompteSalaireByDate($this->userAuthenticated, new DateTime($dateSearch));
        $this->assertInstanceOf(CompteSalaire::class, $compteSalaire);
        $this->assertSame($dateDebutExpected, $compteSalaire->getDateDebutCompte()->format('Y-m-d'));
        $this->assertSame($dateFinExpected, $compteSalaire->getDateFinCompte()->format('Y-m-d'));
    }

    public static function provideDate(): array
    {
        return [
            [
                'dateSearch' => '2024-01-06',
                'dateDebutCompteExpected' => '2024-01-01',
                'dateFinCompteExpected' => '2024-01-15',
            ],
            [
                'date_search' => '2024-01-17',
                'dateDebutCompteExpected' => '2024-01-16',
                'dateFinCompteExpected' => '2024-01-30',
            ],
        ];
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->compteSalaireRepository = null;
        $this->userAuthenticated = null;
    }
}
