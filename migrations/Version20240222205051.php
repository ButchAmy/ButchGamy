<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240222205051 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE friend_request (id INT AUTO_INCREMENT NOT NULL, user_a_id INT NOT NULL, user_b_id INT NOT NULL, requested_on DATETIME NOT NULL, accepted TINYINT(1) NOT NULL, INDEX IDX_F284D94415F1F91 (user_a_id), INDEX IDX_F284D9453EAB07F (user_b_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE friend_request ADD CONSTRAINT FK_F284D94415F1F91 FOREIGN KEY (user_a_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE friend_request ADD CONSTRAINT FK_F284D9453EAB07F FOREIGN KEY (user_b_id) REFERENCES user (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE friend_request DROP FOREIGN KEY FK_F284D94415F1F91');
        $this->addSql('ALTER TABLE friend_request DROP FOREIGN KEY FK_F284D9453EAB07F');
        $this->addSql('DROP TABLE friend_request');
    }
}
