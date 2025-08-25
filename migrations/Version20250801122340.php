<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250801122340 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE category ADD quantity_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE category ADD CONSTRAINT FK_64C19C17E8B4AFC FOREIGN KEY (quantity_id) REFERENCES quantity (id)');
        $this->addSql('CREATE INDEX IDX_64C19C17E8B4AFC ON category (quantity_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE category DROP FOREIGN KEY FK_64C19C17E8B4AFC');
        $this->addSql('DROP INDEX IDX_64C19C17E8B4AFC ON category');
        $this->addSql('ALTER TABLE category DROP quantity_id');
    }
}
