<?php

namespace App\Tests\Repository;

use App\Entity\User;
use App\Repository\DepenseRepository;
use App\Tests\Trait\UserAuthenticatedTrait;
use DateTime;
use Hautelook\AliceBundle\PhpUnit\RefreshDatabaseTrait;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class DepenseRepositoryTest extends KernelTestCase
{
    use RefreshDatabaseTrait;
    use UserAuthenticatedTrait;

    private ?DepenseRepository $depenseRepository;

    protected function setUp(): void
    {
        static::bootKernel();

        $this->depenseRepository = $this->getContainer()->get(depenseRepository::class);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->depenseRepository = null;
    }

    /**
     * @dataProvider providerTotalDepenseAndCapitalByWithoutDate
     */
    public function testGetTotalDepenseAndCapitalNotDateGiving(string $roles, array $expected): void
    {
        $userAuthenticated = $this->mockUserAuthenticated($roles);
        $depensesActual = $this->depenseRepository->findDepensesWithCapital($userAuthenticated);

        $this->assertSame($expected, $depensesActual);
    }

    /**
     * @dataProvider providerTotalDepenseAndCapitalByUserAuthenticated
     */
    public function testGetTotalDepenseAndCapitalForEveryCompteSalaire(string $roles, array $expected): void
    {
        $userAuthenticated = $this->mockUserAuthenticated($roles);
        $depensesActual = $this->depenseRepository->findDepensesWithCapital($userAuthenticated, ['2024-01-01', (new DateTime('+ 20 days'))->format('Y-m-d')]);

        $this->assertSame($expected, $depensesActual);
    }
    /**
     * @dataProvider providerTotalDepenseAndCapitalInDateGiving
     */
    public function testGetTotalDepenseAndCapitalForDateGiving(string $roles, array $expected): void
    {
        $userAuthenticated = $this->mockUserAuthenticated($roles);
        $totalDepenseCapitalGeneralActual = $this->depenseRepository->getTotalDepenseAndCapitalInDateGivingByUser($userAuthenticated, ['2024-01-01', (new DateTime('+ 20 days'))->format('Y-m-d')]);

        $this->assertSame($totalDepenseCapitalGeneralActual, $expected);
    }

    private function mockUserAuthenticated(string $roles): User
    {
        return match ($roles) {
            'user' => $this->getSimpeUserAuthenticated(),
            'other-user' => $this->getSimpeOtherUserAuthenticated(),
            'admin' => $this->getAdminAuthenticated()
        };
    }

    public static function providerTotalDepenseAndCapitalByUserAuthenticated(): array
    {
        return
            [
                [
                    'user' => 'user',
                    'expected' => [
                        [
                            "id" => 1,
                            "label" => "01/01/2024 - 15/01/2024",
                            "total_depense" => 15.25,
                            "total_capital" => 1500.75,
                        ],
                        [
                            "id" => 1,
                            "label" => "13/08/2025 - 27/08/2025",
                            "total_depense" => 25.25,
                            "total_capital" => 15.25,
                        ]
                    ],
                ],
                [
                    'user' => 'other-user',
                    'expected' => [
                        [
                            "id" => 2,
                            "label" => "16/02/2024 - 01/03/2024",
                            "total_depense" => 100.25,
                            "total_capital" => 200.75,
                        ]
                    ],
                ],
                [
                    'user' => 'admin',
                    'expected' => []
                ]
            ];
    }

    public static function providerTotalDepenseAndCapitalInDateGiving(): array
    {
        return [
            [
                'user' => 'user',
                'expected' => [
                    [
                        'total_depense_general' => 40.50,
                        'total_capital_general' => 1516.0
                    ]
                ]
            ],
            [
                'user' => 'other-user',
                'expected' => [
                    [
                        'total_depense_general' => 100.25,
                        'total_capital_general' => 200.75
                    ]
                ]
            ],
            [
                'user' => 'admin',
                'expected' => [
                    [
                        'total_depense_general' => null,
                        'total_capital_general' => null
                    ]
                ]
            ],
        ];
    }

    public function providerTotalDepenseAndCapitalByWithoutDate(): array
    {
        return
            [
                [
                    'user' => 'user',
                    'expected' => [
                        [
                            "id" => 1,
                            "label" => (new DateTime('-7 days'))->format('d/m/Y') . ' - ' . (new DateTime('+7 days'))->format('d/m/Y'),
                            "total_depense" => 25.25,
                            "total_capital" => 15.25,
                        ],
                    ],
                ],
                [
                    'user' => 'other-user',
                    'expected' => [],
                ],
                [
                    'user' => 'admin',
                    'expected' => []
                ]
            ];
    }
}
