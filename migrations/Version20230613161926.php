<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230613161926 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE game_room ADD skull_king_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE game_room ADD CONSTRAINT FK_998A3DB79CCC4B62 FOREIGN KEY (skull_king_id) REFERENCES skull_king (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_998A3DB79CCC4B62 ON game_room (skull_king_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE game_room DROP CONSTRAINT FK_998A3DB79CCC4B62');
        $this->addSql('DROP INDEX UNIQ_998A3DB79CCC4B62');
        $this->addSql('ALTER TABLE game_room DROP skull_king_id');
    }
}
