<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230818125225 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE fold_result_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE player_announce_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE fold_result (id INT NOT NULL, skull_king_id INT DEFAULT NULL, nb_round INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_6AC997759CCC4B62 ON fold_result (skull_king_id)');
        $this->addSql('CREATE TABLE player_announce (id INT NOT NULL, fold_result_id INT DEFAULT NULL, user_id UUID NOT NULL, announced INT NOT NULL, done INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_E0019C95E8C30928 ON player_announce (fold_result_id)');
        $this->addSql('COMMENT ON COLUMN player_announce.user_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE fold_result ADD CONSTRAINT FK_6AC997759CCC4B62 FOREIGN KEY (skull_king_id) REFERENCES skull_king (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE player_announce ADD CONSTRAINT FK_E0019C95E8C30928 FOREIGN KEY (fold_result_id) REFERENCES fold_result (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE player ADD name VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE skull_king ADD version INT DEFAULT 1 NOT NULL');
        $this->addSql('ALTER TABLE skull_king ADD nb_round INT NOT NULL');
        $this->addSql('ALTER TABLE skull_king ADD current_player_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE skull_king ADD color_asked VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE skull_king RENAME COLUMN json_cards TO jsonCards');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE fold_result_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE player_announce_id_seq CASCADE');
        $this->addSql('ALTER TABLE fold_result DROP CONSTRAINT FK_6AC997759CCC4B62');
        $this->addSql('ALTER TABLE player_announce DROP CONSTRAINT FK_E0019C95E8C30928');
        $this->addSql('DROP TABLE fold_result');
        $this->addSql('DROP TABLE player_announce');
        $this->addSql('ALTER TABLE player DROP name');
        $this->addSql('ALTER TABLE skull_king DROP version');
        $this->addSql('ALTER TABLE skull_king DROP nb_round');
        $this->addSql('ALTER TABLE skull_king DROP current_player_id');
        $this->addSql('ALTER TABLE skull_king DROP color_asked');
        $this->addSql('ALTER TABLE skull_king RENAME COLUMN jsonCards TO json_cards');
    }
}
