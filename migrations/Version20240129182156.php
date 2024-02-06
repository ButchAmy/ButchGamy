<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240129182156 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user ADD username VARCHAR(255) NOT NULL, ADD profile_pic VARCHAR(255) DEFAULT NULL, ADD profile_bio LONGTEXT DEFAULT NULL, ADD birthday DATE DEFAULT NULL, ADD gender VARCHAR(255) DEFAULT NULL, ADD created_on DATETIME NOT NULL, ADD updated_on DATETIME NOT NULL, ADD admin TINYINT(1) NOT NULL, ADD login_allowed TINYINT(1) NOT NULL, ADD friend_allowed TINYINT(1) NOT NULL, ADD message_allowed TINYINT(1) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user DROP username, DROP profile_pic, DROP profile_bio, DROP birthday, DROP gender, DROP created_on, DROP updated_on, DROP admin, DROP login_allowed, DROP friend_allowed, DROP message_allowed');
    }
}
