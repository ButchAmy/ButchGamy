<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240228124548 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE game_result (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, game_id INT NOT NULL, score INT NOT NULL, time INT NOT NULL, achieved_on DATETIME NOT NULL, INDEX IDX_6E5F6CDBA76ED395 (user_id), INDEX IDX_6E5F6CDBE48FD905 (game_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE game_result ADD CONSTRAINT FK_6E5F6CDBA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE game_result ADD CONSTRAINT FK_6E5F6CDBE48FD905 FOREIGN KEY (game_id) REFERENCES game (id)');
        $this->addSql('ALTER TABLE game_results DROP FOREIGN KEY FK_A619B3BA76ED395');
        $this->addSql('ALTER TABLE game_results DROP FOREIGN KEY FK_A619B3BE48FD905');
        $this->addSql('DROP TABLE game_results');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE game_results (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, game_id INT NOT NULL, score INT NOT NULL, time INT NOT NULL, achieved_on DATETIME NOT NULL, INDEX IDX_A619B3BE48FD905 (game_id), INDEX IDX_A619B3BA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE game_results ADD CONSTRAINT FK_A619B3BA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE game_results ADD CONSTRAINT FK_A619B3BE48FD905 FOREIGN KEY (game_id) REFERENCES game (id)');
        $this->addSql('ALTER TABLE game_result DROP FOREIGN KEY FK_6E5F6CDBA76ED395');
        $this->addSql('ALTER TABLE game_result DROP FOREIGN KEY FK_6E5F6CDBE48FD905');
        $this->addSql('DROP TABLE game_result');
    }
}
