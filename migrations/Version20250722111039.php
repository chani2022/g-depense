<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250722111039 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE depense (id INT AUTO_INCREMENT NOT NULL, compte_salaire_id INT NOT NULL, category_id INT NOT NULL, INDEX IDX_34059757EE6C183F (compte_salaire_id), INDEX IDX_3405975712469DE2 (category_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE depense ADD CONSTRAINT FK_34059757EE6C183F FOREIGN KEY (compte_salaire_id) REFERENCES compte_salaire (id)');
        $this->addSql('ALTER TABLE depense ADD CONSTRAINT FK_3405975712469DE2 FOREIGN KEY (category_id) REFERENCES category (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE depense DROP FOREIGN KEY FK_34059757EE6C183F');
        $this->addSql('ALTER TABLE depense DROP FOREIGN KEY FK_3405975712469DE2');
        $this->addSql('DROP TABLE depense');
    }
}
