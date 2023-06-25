<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230613190854 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE card ALTER pirate_name DROP NOT NULL');
        $this->addSql('ALTER TABLE card ALTER color DROP NOT NULL');
        $this->addSql('ALTER TABLE card ALTER value DROP NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE card ALTER pirate_name SET NOT NULL');
        $this->addSql('ALTER TABLE card ALTER color SET NOT NULL');
        $this->addSql('ALTER TABLE card ALTER value SET NOT NULL');
    }
}
