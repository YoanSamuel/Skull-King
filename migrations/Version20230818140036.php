<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230818140036 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE player_announce ADD player_id INT NOT NULL');
        $this->addSql('ALTER TABLE player_announce DROP user_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE player_announce ADD user_id UUID NOT NULL');
        $this->addSql('ALTER TABLE player_announce DROP player_id');
        $this->addSql('COMMENT ON COLUMN player_announce.user_id IS \'(DC2Type:uuid)\'');
    }
}
