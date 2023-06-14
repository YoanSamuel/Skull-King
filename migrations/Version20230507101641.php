<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230507101641 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE game_room_game_room_user (game_room_id INT NOT NULL, game_room_user_id INT NOT NULL, PRIMARY KEY(game_room_id, game_room_user_id))');
        $this->addSql('CREATE INDEX IDX_8E063183C1D50FBC ON game_room_game_room_user (game_room_id)');
        $this->addSql('CREATE INDEX IDX_8E063183A59C5B1B ON game_room_game_room_user (game_room_user_id)');
        $this->addSql('ALTER TABLE game_room_game_room_user ADD CONSTRAINT FK_8E063183C1D50FBC FOREIGN KEY (game_room_id) REFERENCES game_room (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE game_room_game_room_user ADD CONSTRAINT FK_8E063183A59C5B1B FOREIGN KEY (game_room_user_id) REFERENCES game_room_user (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE game_room_game_room_user DROP CONSTRAINT FK_8E063183C1D50FBC');
        $this->addSql('ALTER TABLE game_room_game_room_user DROP CONSTRAINT FK_8E063183A59C5B1B');
        $this->addSql('DROP TABLE game_room_game_room_user');
    }
}
