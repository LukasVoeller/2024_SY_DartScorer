<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240409182311 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE game_cricket DROP FOREIGN KEY FK_42CB1934BF396750');
        $this->addSql('ALTER TABLE game_shanghai DROP FOREIGN KEY FK_19F77755BF396750');
        $this->addSql('ALTER TABLE game_x01 DROP FOREIGN KEY FK_3061C5F6BF396750');
        $this->addSql('ALTER TABLE leg DROP FOREIGN KEY FK_75D0804FDB9613A0');
        $this->addSql('ALTER TABLE score DROP FOREIGN KEY FK_32993751DB9613A0');
        $this->addSql('ALTER TABLE `set` DROP FOREIGN KEY FK_E61425DCDB9613A0');
        $this->addSql('DROP TABLE game');
        $this->addSql('ALTER TABLE game_cricket ADD date DATETIME NOT NULL, ADD player1_id INT NOT NULL, ADD player2_id INT NOT NULL, ADD player_id_starting INT NOT NULL, ADD player_id_winner INT DEFAULT NULL, ADD state VARCHAR(32) DEFAULT NULL, ADD match_mode VARCHAR(32) NOT NULL, ADD match_mode_sets_needed INT DEFAULT NULL, ADD match_mode_legs_needed INT DEFAULT NULL, CHANGE id id INT AUTO_INCREMENT NOT NULL');
        $this->addSql('ALTER TABLE game_shanghai ADD date DATETIME NOT NULL, ADD player1_id INT NOT NULL, ADD player2_id INT NOT NULL, ADD player_id_starting INT NOT NULL, ADD player_id_winner INT DEFAULT NULL, ADD state VARCHAR(32) DEFAULT NULL, ADD match_mode VARCHAR(32) NOT NULL, ADD match_mode_sets_needed INT DEFAULT NULL, ADD match_mode_legs_needed INT DEFAULT NULL, CHANGE id id INT AUTO_INCREMENT NOT NULL');
        $this->addSql('ALTER TABLE game_x01 ADD date DATETIME NOT NULL, ADD player1_id INT NOT NULL, ADD player2_id INT NOT NULL, ADD player_id_starting INT NOT NULL, ADD player_id_winner INT DEFAULT NULL, ADD state VARCHAR(32) DEFAULT NULL, ADD match_mode VARCHAR(32) NOT NULL, ADD match_mode_sets_needed INT DEFAULT NULL, ADD match_mode_legs_needed INT DEFAULT NULL, CHANGE id id INT AUTO_INCREMENT NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE game (id INT AUTO_INCREMENT NOT NULL, date DATETIME NOT NULL, player1_id INT NOT NULL, player2_id INT NOT NULL, player_id_starting INT NOT NULL, player_id_winner INT DEFAULT NULL, state VARCHAR(32) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, match_mode VARCHAR(32) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, match_mode_sets_needed INT DEFAULT NULL, match_mode_legs_needed INT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE game_cricket DROP date, DROP player1_id, DROP player2_id, DROP player_id_starting, DROP player_id_winner, DROP state, DROP match_mode, DROP match_mode_sets_needed, DROP match_mode_legs_needed, CHANGE id id INT NOT NULL');
        $this->addSql('ALTER TABLE game_cricket ADD CONSTRAINT FK_42CB1934BF396750 FOREIGN KEY (id) REFERENCES game (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE game_shanghai DROP date, DROP player1_id, DROP player2_id, DROP player_id_starting, DROP player_id_winner, DROP state, DROP match_mode, DROP match_mode_sets_needed, DROP match_mode_legs_needed, CHANGE id id INT NOT NULL');
        $this->addSql('ALTER TABLE game_shanghai ADD CONSTRAINT FK_19F77755BF396750 FOREIGN KEY (id) REFERENCES game (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE game_x01 DROP date, DROP player1_id, DROP player2_id, DROP player_id_starting, DROP player_id_winner, DROP state, DROP match_mode, DROP match_mode_sets_needed, DROP match_mode_legs_needed, CHANGE id id INT NOT NULL');
        $this->addSql('ALTER TABLE game_x01 ADD CONSTRAINT FK_3061C5F6BF396750 FOREIGN KEY (id) REFERENCES game (id) ON DELETE CASCADE');
    }
}
