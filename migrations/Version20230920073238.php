<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230920073238 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE fold_result_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE game_room_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE game_room_user_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE player_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE player_announce_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE skull_king_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE fold_result (id INT NOT NULL, skull_king_id INT DEFAULT NULL, nb_round INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_6AC997759CCC4B62 ON fold_result (skull_king_id)');
        $this->addSql('CREATE TABLE game_room (id INT NOT NULL, skull_king_id INT DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_998A3DB79CCC4B62 ON game_room (skull_king_id)');
        $this->addSql('COMMENT ON COLUMN game_room.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE game_room_game_room_user (game_room_id INT NOT NULL, game_room_user_id INT NOT NULL, PRIMARY KEY(game_room_id, game_room_user_id))');
        $this->addSql('CREATE INDEX IDX_8E063183C1D50FBC ON game_room_game_room_user (game_room_id)');
        $this->addSql('CREATE INDEX IDX_8E063183A59C5B1B ON game_room_game_room_user (game_room_user_id)');
        $this->addSql('CREATE TABLE game_room_user (id INT NOT NULL, user_id UUID NOT NULL, user_name VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN game_room_user.user_id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE player (id INT NOT NULL, skull_king_id INT NOT NULL, user_id UUID NOT NULL, name VARCHAR(255) NOT NULL, cards JSON NOT NULL, announce INT DEFAULT NULL, score NUMERIC(10, 0) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_98197A659CCC4B62 ON player (skull_king_id)');
        $this->addSql('COMMENT ON COLUMN player.user_id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE player_announce (id INT NOT NULL, fold_result_id INT DEFAULT NULL, player_id INT NOT NULL, potential_bonus INT NOT NULL, announced INT NOT NULL, done INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_E0019C95E8C30928 ON player_announce (fold_result_id)');
        $this->addSql('CREATE TABLE skull_king (id INT NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, version INT DEFAULT 1 NOT NULL, nb_round INT NOT NULL, current_player_id INT DEFAULT NULL, first_player_id INT DEFAULT NULL, color_asked VARCHAR(255) DEFAULT NULL, jsonCards JSON DEFAULT NULL, state VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN skull_king.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE messenger_messages (id BIGSERIAL NOT NULL, body TEXT NOT NULL, headers TEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, available_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, delivered_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_75EA56E0FB7336F0 ON messenger_messages (queue_name)');
        $this->addSql('CREATE INDEX IDX_75EA56E0E3BD61CE ON messenger_messages (available_at)');
        $this->addSql('CREATE INDEX IDX_75EA56E016BA31DB ON messenger_messages (delivered_at)');
        $this->addSql('CREATE OR REPLACE FUNCTION notify_messenger_messages() RETURNS TRIGGER AS $$
            BEGIN
                PERFORM pg_notify(\'messenger_messages\', NEW.queue_name::text);
                RETURN NEW;
            END;
        $$ LANGUAGE plpgsql;');
        $this->addSql('DROP TRIGGER IF EXISTS notify_trigger ON messenger_messages;');
        $this->addSql('CREATE TRIGGER notify_trigger AFTER INSERT OR UPDATE ON messenger_messages FOR EACH ROW EXECUTE PROCEDURE notify_messenger_messages();');
        $this->addSql('ALTER TABLE fold_result ADD CONSTRAINT FK_6AC997759CCC4B62 FOREIGN KEY (skull_king_id) REFERENCES skull_king (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE game_room ADD CONSTRAINT FK_998A3DB79CCC4B62 FOREIGN KEY (skull_king_id) REFERENCES skull_king (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE game_room_game_room_user ADD CONSTRAINT FK_8E063183C1D50FBC FOREIGN KEY (game_room_id) REFERENCES game_room (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE game_room_game_room_user ADD CONSTRAINT FK_8E063183A59C5B1B FOREIGN KEY (game_room_user_id) REFERENCES game_room_user (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE player ADD CONSTRAINT FK_98197A659CCC4B62 FOREIGN KEY (skull_king_id) REFERENCES skull_king (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE player_announce ADD CONSTRAINT FK_E0019C95E8C30928 FOREIGN KEY (fold_result_id) REFERENCES fold_result (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE fold_result_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE game_room_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE game_room_user_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE player_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE player_announce_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE skull_king_id_seq CASCADE');
        $this->addSql('ALTER TABLE fold_result DROP CONSTRAINT FK_6AC997759CCC4B62');
        $this->addSql('ALTER TABLE game_room DROP CONSTRAINT FK_998A3DB79CCC4B62');
        $this->addSql('ALTER TABLE game_room_game_room_user DROP CONSTRAINT FK_8E063183C1D50FBC');
        $this->addSql('ALTER TABLE game_room_game_room_user DROP CONSTRAINT FK_8E063183A59C5B1B');
        $this->addSql('ALTER TABLE player DROP CONSTRAINT FK_98197A659CCC4B62');
        $this->addSql('ALTER TABLE player_announce DROP CONSTRAINT FK_E0019C95E8C30928');
        $this->addSql('DROP TABLE fold_result');
        $this->addSql('DROP TABLE game_room');
        $this->addSql('DROP TABLE game_room_game_room_user');
        $this->addSql('DROP TABLE game_room_user');
        $this->addSql('DROP TABLE player');
        $this->addSql('DROP TABLE player_announce');
        $this->addSql('DROP TABLE skull_king');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
