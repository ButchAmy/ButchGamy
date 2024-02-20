<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240201172556 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE achievement (id INT AUTO_INCREMENT NOT NULL, game_id INT NOT NULL, name VARCHAR(255) NOT NULL, description VARCHAR(255) NOT NULL, image VARCHAR(255) DEFAULT NULL, INDEX IDX_96737FF1E48FD905 (game_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE game (id INT AUTO_INCREMENT NOT NULL, developer_id INT NOT NULL, title VARCHAR(255) NOT NULL, description VARCHAR(511) NOT NULL, image VARCHAR(255) DEFAULT NULL, url VARCHAR(255) NOT NULL, api_key VARCHAR(255) NOT NULL, created_on DATETIME NOT NULL, updated_on DATETIME NOT NULL, public TINYINT(1) NOT NULL, INDEX IDX_232B318C64DD9267 (developer_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE game_results (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, game_id INT NOT NULL, score INT NOT NULL, time INT NOT NULL, achieved_on DATETIME NOT NULL, INDEX IDX_A619B3BA76ED395 (user_id), INDEX IDX_A619B3BE48FD905 (game_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL COMMENT \'(DC2Type:json)\', password VARCHAR(255) NOT NULL, username VARCHAR(255) NOT NULL, profile_pic VARCHAR(255) DEFAULT NULL, profile_bio VARCHAR(511) DEFAULT NULL, birthday DATE DEFAULT NULL, gender VARCHAR(255) DEFAULT NULL, created_on DATETIME NOT NULL, updated_on DATETIME NOT NULL, admin TINYINT(1) NOT NULL, login_allowed TINYINT(1) NOT NULL, friend_allowed TINYINT(1) NOT NULL, message_allowed TINYINT(1) NOT NULL, UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_achievement (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, achievement_id INT NOT NULL, achieved_on DATETIME NOT NULL, INDEX IDX_3F68B664A76ED395 (user_id), INDEX IDX_3F68B664B3EC99FE (achievement_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', available_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', delivered_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE achievement ADD CONSTRAINT FK_96737FF1E48FD905 FOREIGN KEY (game_id) REFERENCES game (id)');
        $this->addSql('ALTER TABLE game ADD CONSTRAINT FK_232B318C64DD9267 FOREIGN KEY (developer_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE game_results ADD CONSTRAINT FK_A619B3BA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE game_results ADD CONSTRAINT FK_A619B3BE48FD905 FOREIGN KEY (game_id) REFERENCES game (id)');
        $this->addSql('ALTER TABLE user_achievement ADD CONSTRAINT FK_3F68B664A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE user_achievement ADD CONSTRAINT FK_3F68B664B3EC99FE FOREIGN KEY (achievement_id) REFERENCES achievement (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE achievement DROP FOREIGN KEY FK_96737FF1E48FD905');
        $this->addSql('ALTER TABLE game DROP FOREIGN KEY FK_232B318C64DD9267');
        $this->addSql('ALTER TABLE game_results DROP FOREIGN KEY FK_A619B3BA76ED395');
        $this->addSql('ALTER TABLE game_results DROP FOREIGN KEY FK_A619B3BE48FD905');
        $this->addSql('ALTER TABLE user_achievement DROP FOREIGN KEY FK_3F68B664A76ED395');
        $this->addSql('ALTER TABLE user_achievement DROP FOREIGN KEY FK_3F68B664B3EC99FE');
        $this->addSql('DROP TABLE achievement');
        $this->addSql('DROP TABLE game');
        $this->addSql('DROP TABLE game_results');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE user_achievement');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
