<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240401090306 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE game_cricket (id INT AUTO_INCREMENT NOT NULL, player1_id INT DEFAULT NULL, player2_id INT DEFAULT NULL, player_starting_id INT DEFAULT NULL, winner_id INT DEFAULT NULL, loser_id INT DEFAULT NULL, date DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, player1_scores JSON DEFAULT NULL, player2_scores JSON DEFAULT NULL, player1_sets INT DEFAULT 0 NOT NULL, player2_sets INT DEFAULT 0 NOT NULL, player1_legs INT DEFAULT 0 NOT NULL, player2_legs INT DEFAULT 0 NOT NULL, state VARCHAR(255) DEFAULT NULL, match_mode VARCHAR(255) DEFAULT NULL, match_mode_sets INT DEFAULT 0 NOT NULL, match_mode_legs INT DEFAULT 0 NOT NULL, player1_darts INT DEFAULT 0 NOT NULL, player2_darts INT DEFAULT 0 NOT NULL, end_score_player1 INT DEFAULT 0 NOT NULL, end_score_player2 INT DEFAULT 0 NOT NULL, game_type VARCHAR(255) NOT NULL, INDEX IDX_42CB1934C0990423 (player1_id), INDEX IDX_42CB1934D22CABCD (player2_id), INDEX IDX_42CB19342090FA7D (player_starting_id), INDEX IDX_42CB19345DFCD4B8 (winner_id), INDEX IDX_42CB19341BCAA5F6 (loser_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE game_shanghai (id INT AUTO_INCREMENT NOT NULL, player1_id INT DEFAULT NULL, player2_id INT DEFAULT NULL, player_starting_id INT DEFAULT NULL, winner_id INT DEFAULT NULL, loser_id INT DEFAULT NULL, date DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, player1_scores JSON DEFAULT NULL, player2_scores JSON DEFAULT NULL, player1_sets INT DEFAULT 0 NOT NULL, player2_sets INT DEFAULT 0 NOT NULL, player1_legs INT DEFAULT 0 NOT NULL, player2_legs INT DEFAULT 0 NOT NULL, state VARCHAR(255) DEFAULT NULL, match_mode VARCHAR(255) DEFAULT NULL, match_mode_sets INT DEFAULT 0 NOT NULL, match_mode_legs INT DEFAULT 0 NOT NULL, player1_darts INT DEFAULT 0 NOT NULL, player2_darts INT DEFAULT 0 NOT NULL, end_score_player1 INT DEFAULT 0 NOT NULL, end_score_player2 INT DEFAULT 0 NOT NULL, game_type VARCHAR(255) NOT NULL, INDEX IDX_19F77755C0990423 (player1_id), INDEX IDX_19F77755D22CABCD (player2_id), INDEX IDX_19F777552090FA7D (player_starting_id), INDEX IDX_19F777555DFCD4B8 (winner_id), INDEX IDX_19F777551BCAA5F6 (loser_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE game_x01 (id INT AUTO_INCREMENT NOT NULL, player1_id INT DEFAULT NULL, player2_id INT DEFAULT NULL, player_starting_id INT DEFAULT NULL, winner_id INT DEFAULT NULL, loser_id INT DEFAULT NULL, date DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, player1_scores JSON DEFAULT NULL, player2_scores JSON DEFAULT NULL, player1_sets INT DEFAULT 0 NOT NULL, player2_sets INT DEFAULT 0 NOT NULL, player1_legs INT DEFAULT 0 NOT NULL, player2_legs INT DEFAULT 0 NOT NULL, state VARCHAR(255) DEFAULT NULL, match_mode VARCHAR(255) DEFAULT NULL, match_mode_sets INT DEFAULT 0 NOT NULL, match_mode_legs INT DEFAULT 0 NOT NULL, player1_darts INT DEFAULT 0 NOT NULL, player2_darts INT DEFAULT 0 NOT NULL, start_score INT DEFAULT 0 NOT NULL, finish_type VARCHAR(255) DEFAULT NULL, player1_averages JSON DEFAULT NULL, player2_averages JSON DEFAULT NULL, INDEX IDX_3061C5F6C0990423 (player1_id), INDEX IDX_3061C5F6D22CABCD (player2_id), INDEX IDX_3061C5F62090FA7D (player_starting_id), INDEX IDX_3061C5F65DFCD4B8 (winner_id), INDEX IDX_3061C5F61BCAA5F6 (loser_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE player (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(16) NOT NULL, average DOUBLE PRECISION DEFAULT \'0\', highscore INT DEFAULT 0, counter180 INT DEFAULT 0, counter_played_games INT DEFAULT 0, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, player_id INT DEFAULT NULL, username VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_8D93D64999E6F5DF (player_id), UNIQUE INDEX UNIQ_IDENTIFIER_USERNAME (username), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE game_cricket ADD CONSTRAINT FK_42CB1934C0990423 FOREIGN KEY (player1_id) REFERENCES player (id)');
        $this->addSql('ALTER TABLE game_cricket ADD CONSTRAINT FK_42CB1934D22CABCD FOREIGN KEY (player2_id) REFERENCES player (id)');
        $this->addSql('ALTER TABLE game_cricket ADD CONSTRAINT FK_42CB19342090FA7D FOREIGN KEY (player_starting_id) REFERENCES player (id)');
        $this->addSql('ALTER TABLE game_cricket ADD CONSTRAINT FK_42CB19345DFCD4B8 FOREIGN KEY (winner_id) REFERENCES player (id)');
        $this->addSql('ALTER TABLE game_cricket ADD CONSTRAINT FK_42CB19341BCAA5F6 FOREIGN KEY (loser_id) REFERENCES player (id)');
        $this->addSql('ALTER TABLE game_shanghai ADD CONSTRAINT FK_19F77755C0990423 FOREIGN KEY (player1_id) REFERENCES player (id)');
        $this->addSql('ALTER TABLE game_shanghai ADD CONSTRAINT FK_19F77755D22CABCD FOREIGN KEY (player2_id) REFERENCES player (id)');
        $this->addSql('ALTER TABLE game_shanghai ADD CONSTRAINT FK_19F777552090FA7D FOREIGN KEY (player_starting_id) REFERENCES player (id)');
        $this->addSql('ALTER TABLE game_shanghai ADD CONSTRAINT FK_19F777555DFCD4B8 FOREIGN KEY (winner_id) REFERENCES player (id)');
        $this->addSql('ALTER TABLE game_shanghai ADD CONSTRAINT FK_19F777551BCAA5F6 FOREIGN KEY (loser_id) REFERENCES player (id)');
        $this->addSql('ALTER TABLE game_x01 ADD CONSTRAINT FK_3061C5F6C0990423 FOREIGN KEY (player1_id) REFERENCES player (id)');
        $this->addSql('ALTER TABLE game_x01 ADD CONSTRAINT FK_3061C5F6D22CABCD FOREIGN KEY (player2_id) REFERENCES player (id)');
        $this->addSql('ALTER TABLE game_x01 ADD CONSTRAINT FK_3061C5F62090FA7D FOREIGN KEY (player_starting_id) REFERENCES player (id)');
        $this->addSql('ALTER TABLE game_x01 ADD CONSTRAINT FK_3061C5F65DFCD4B8 FOREIGN KEY (winner_id) REFERENCES player (id)');
        $this->addSql('ALTER TABLE game_x01 ADD CONSTRAINT FK_3061C5F61BCAA5F6 FOREIGN KEY (loser_id) REFERENCES player (id)');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D64999E6F5DF FOREIGN KEY (player_id) REFERENCES player (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE game_cricket DROP FOREIGN KEY FK_42CB1934C0990423');
        $this->addSql('ALTER TABLE game_cricket DROP FOREIGN KEY FK_42CB1934D22CABCD');
        $this->addSql('ALTER TABLE game_cricket DROP FOREIGN KEY FK_42CB19342090FA7D');
        $this->addSql('ALTER TABLE game_cricket DROP FOREIGN KEY FK_42CB19345DFCD4B8');
        $this->addSql('ALTER TABLE game_cricket DROP FOREIGN KEY FK_42CB19341BCAA5F6');
        $this->addSql('ALTER TABLE game_shanghai DROP FOREIGN KEY FK_19F77755C0990423');
        $this->addSql('ALTER TABLE game_shanghai DROP FOREIGN KEY FK_19F77755D22CABCD');
        $this->addSql('ALTER TABLE game_shanghai DROP FOREIGN KEY FK_19F777552090FA7D');
        $this->addSql('ALTER TABLE game_shanghai DROP FOREIGN KEY FK_19F777555DFCD4B8');
        $this->addSql('ALTER TABLE game_shanghai DROP FOREIGN KEY FK_19F777551BCAA5F6');
        $this->addSql('ALTER TABLE game_x01 DROP FOREIGN KEY FK_3061C5F6C0990423');
        $this->addSql('ALTER TABLE game_x01 DROP FOREIGN KEY FK_3061C5F6D22CABCD');
        $this->addSql('ALTER TABLE game_x01 DROP FOREIGN KEY FK_3061C5F62090FA7D');
        $this->addSql('ALTER TABLE game_x01 DROP FOREIGN KEY FK_3061C5F65DFCD4B8');
        $this->addSql('ALTER TABLE game_x01 DROP FOREIGN KEY FK_3061C5F61BCAA5F6');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D64999E6F5DF');
        $this->addSql('DROP TABLE game_cricket');
        $this->addSql('DROP TABLE game_shanghai');
        $this->addSql('DROP TABLE game_x01');
        $this->addSql('DROP TABLE player');
        $this->addSql('DROP TABLE user');
    }
}
