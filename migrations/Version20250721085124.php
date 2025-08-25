<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250721085124 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE capital (id INT AUTO_INCREMENT NOT NULL, compte_salaire_id INT NOT NULL, montant DOUBLE PRECISION DEFAULT NULL, ajout DOUBLE PRECISION DEFAULT NULL, INDEX IDX_307CBAA6EE6C183F (compte_salaire_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE capital ADD CONSTRAINT FK_307CBAA6EE6C183F FOREIGN KEY (compte_salaire_id) REFERENCES compte_salaire (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE capital DROP FOREIGN KEY FK_307CBAA6EE6C183F');
        $this->addSql('DROP TABLE capital');
    }
}
