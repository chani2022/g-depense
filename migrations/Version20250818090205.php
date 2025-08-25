<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250818090205 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE category DROP FOREIGN KEY FK_64C19C17E8B4AFC');
        $this->addSql('ALTER TABLE depense DROP FOREIGN KEY FK_340597577E8B4AFC');
        $this->addSql('CREATE TABLE unite (id INT AUTO_INCREMENT NOT NULL, owner_id INT NOT NULL, unite VARCHAR(255) NOT NULL, INDEX IDX_1D64C1187E3C61F9 (owner_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE unite ADD CONSTRAINT FK_1D64C1187E3C61F9 FOREIGN KEY (owner_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE quantity DROP FOREIGN KEY FK_9FF316367E3C61F9');
        $this->addSql('DROP TABLE quantity');
        $this->addSql('DROP INDEX IDX_64C19C17E8B4AFC ON category');
        $this->addSql('ALTER TABLE category DROP quantity_id');
        $this->addSql('DROP INDEX IDX_340597577E8B4AFC ON depense');
        $this->addSql('ALTER TABLE depense ADD quantite DOUBLE PRECISION NOT NULL, CHANGE quantity_id unite_id INT NOT NULL');
        $this->addSql('ALTER TABLE depense ADD CONSTRAINT FK_34059757EC4A74AB FOREIGN KEY (unite_id) REFERENCES unite (id)');
        $this->addSql('CREATE INDEX IDX_34059757EC4A74AB ON depense (unite_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE depense DROP FOREIGN KEY FK_34059757EC4A74AB');
        $this->addSql('CREATE TABLE quantity (id INT AUTO_INCREMENT NOT NULL, owner_id INT NOT NULL, unite VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, quantite DOUBLE PRECISION NOT NULL, INDEX IDX_9FF316367E3C61F9 (owner_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE quantity ADD CONSTRAINT FK_9FF316367E3C61F9 FOREIGN KEY (owner_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE unite DROP FOREIGN KEY FK_1D64C1187E3C61F9');
        $this->addSql('DROP TABLE unite');
        $this->addSql('ALTER TABLE category ADD quantity_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE category ADD CONSTRAINT FK_64C19C17E8B4AFC FOREIGN KEY (quantity_id) REFERENCES quantity (id)');
        $this->addSql('CREATE INDEX IDX_64C19C17E8B4AFC ON category (quantity_id)');
        $this->addSql('DROP INDEX IDX_34059757EC4A74AB ON depense');
        $this->addSql('ALTER TABLE depense DROP quantite, CHANGE unite_id quantity_id INT NOT NULL');
        $this->addSql('ALTER TABLE depense ADD CONSTRAINT FK_340597577E8B4AFC FOREIGN KEY (quantity_id) REFERENCES quantity (id)');
        $this->addSql('CREATE INDEX IDX_340597577E8B4AFC ON depense (quantity_id)');
    }
}
