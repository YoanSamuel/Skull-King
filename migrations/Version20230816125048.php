<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230816125048 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP SEQUENCE card_id_seq CASCADE');
        $this->addSql('ALTER TABLE card DROP CONSTRAINT fk_161498d399e6f5df');
        $this->addSql('DROP TABLE card');
        $this->addSql('ALTER TABLE player ADD cards JSON NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('CREATE SEQUENCE card_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE card (id INT NOT NULL, player_id INT DEFAULT NULL, card_type VARCHAR(255) NOT NULL, pirate_name VARCHAR(255) DEFAULT NULL, color VARCHAR(255) DEFAULT NULL, value VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX idx_161498d399e6f5df ON card (player_id)');
        $this->addSql('ALTER TABLE card ADD CONSTRAINT fk_161498d399e6f5df FOREIGN KEY (player_id) REFERENCES player (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE player DROP cards');
    }
}
