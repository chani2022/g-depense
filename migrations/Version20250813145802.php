<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250813145802 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE depense ADD quantity_id INT NOT NULL, ADD nom_depense VARCHAR(255) NOT NULL, ADD prix DOUBLE PRECISION NOT NULL, ADD is_vital TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE depense ADD CONSTRAINT FK_340597577E8B4AFC FOREIGN KEY (quantity_id) REFERENCES quantity (id)');
        $this->addSql('CREATE INDEX IDX_340597577E8B4AFC ON depense (quantity_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE depense DROP FOREIGN KEY FK_340597577E8B4AFC');
        $this->addSql('DROP INDEX IDX_340597577E8B4AFC ON depense');
        $this->addSql('ALTER TABLE depense DROP quantity_id, DROP nom_depense, DROP prix, DROP is_vital');
    }
}
