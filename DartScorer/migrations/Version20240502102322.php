<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240502102322 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE game (id INT AUTO_INCREMENT NOT NULL, date DATETIME NOT NULL, player1_id INT NOT NULL, player1_name VARCHAR(255) NOT NULL, player2_id INT NOT NULL, player2_name VARCHAR(255) NOT NULL, player_starting_id INT NOT NULL, player_id_winner INT DEFAULT NULL, state VARCHAR(32) DEFAULT NULL, match_mode VARCHAR(32) NOT NULL, match_mode_sets_needed INT DEFAULT NULL, match_mode_legs_needed INT DEFAULT NULL, game_type VARCHAR(255) NOT NULL, start_score INT DEFAULT NULL, finish_type VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE game_leg (id INT AUTO_INCREMENT NOT NULL, related_game_id INT DEFAULT NULL, related_set_id INT DEFAULT NULL, player_id_winner INT DEFAULT NULL, INDEX IDX_6E8A938ADB9613A0 (related_game_id), INDEX IDX_6E8A938ABC3775BE (related_set_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE game_score (id INT AUTO_INCREMENT NOT NULL, related_game_id INT DEFAULT NULL, related_leg_id INT DEFAULT NULL, player_id INT NOT NULL, value INT NOT NULL, darts_thrown INT NOT NULL, dart1 INT DEFAULT NULL, dart2 INT DEFAULT NULL, dart3 INT DEFAULT NULL, INDEX IDX_AA4EDEDB9613A0 (related_game_id), INDEX IDX_AA4EDEC1B3D81 (related_leg_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE game_set (id INT AUTO_INCREMENT NOT NULL, related_game_id INT NOT NULL, player_id_winner INT DEFAULT NULL, match_mode_legs_needed INT NOT NULL, INDEX IDX_FD4E3619DB9613A0 (related_game_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE player (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, date DATETIME NOT NULL, name VARCHAR(32) NOT NULL, UNIQUE INDEX UNIQ_98197A65A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, date DATETIME NOT NULL, username VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_IDENTIFIER_USERNAME (username), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE game_leg ADD CONSTRAINT FK_6E8A938ADB9613A0 FOREIGN KEY (related_game_id) REFERENCES game (id)');
        $this->addSql('ALTER TABLE game_leg ADD CONSTRAINT FK_6E8A938ABC3775BE FOREIGN KEY (related_set_id) REFERENCES game_set (id)');
        $this->addSql('ALTER TABLE game_score ADD CONSTRAINT FK_AA4EDEDB9613A0 FOREIGN KEY (related_game_id) REFERENCES game (id)');
        $this->addSql('ALTER TABLE game_score ADD CONSTRAINT FK_AA4EDEC1B3D81 FOREIGN KEY (related_leg_id) REFERENCES game_leg (id)');
        $this->addSql('ALTER TABLE game_set ADD CONSTRAINT FK_FD4E3619DB9613A0 FOREIGN KEY (related_game_id) REFERENCES game (id)');
        $this->addSql('ALTER TABLE player ADD CONSTRAINT FK_98197A65A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE SET NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE game_leg DROP FOREIGN KEY FK_6E8A938ADB9613A0');
        $this->addSql('ALTER TABLE game_leg DROP FOREIGN KEY FK_6E8A938ABC3775BE');
        $this->addSql('ALTER TABLE game_score DROP FOREIGN KEY FK_AA4EDEDB9613A0');
        $this->addSql('ALTER TABLE game_score DROP FOREIGN KEY FK_AA4EDEC1B3D81');
        $this->addSql('ALTER TABLE game_set DROP FOREIGN KEY FK_FD4E3619DB9613A0');
        $this->addSql('ALTER TABLE player DROP FOREIGN KEY FK_98197A65A76ED395');
        $this->addSql('DROP TABLE game');
        $this->addSql('DROP TABLE game_leg');
        $this->addSql('DROP TABLE game_score');
        $this->addSql('DROP TABLE game_set');
        $this->addSql('DROP TABLE player');
        $this->addSql('DROP TABLE user');
    }
}