<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240226145001 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE friend_request DROP FOREIGN KEY FK_F284D94415F1F91');
        $this->addSql('ALTER TABLE friend_request DROP FOREIGN KEY FK_F284D9453EAB07F');
        $this->addSql('DROP INDEX IDX_F284D94415F1F91 ON friend_request');
        $this->addSql('DROP INDEX IDX_F284D9453EAB07F ON friend_request');
        $this->addSql('ALTER TABLE friend_request ADD user_from_id INT NOT NULL, ADD user_to_id INT NOT NULL, DROP user_a_id, DROP user_b_id');
        $this->addSql('ALTER TABLE friend_request ADD CONSTRAINT FK_F284D9420C3C701 FOREIGN KEY (user_from_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE friend_request ADD CONSTRAINT FK_F284D94D2F7B13D FOREIGN KEY (user_to_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_F284D9420C3C701 ON friend_request (user_from_id)');
        $this->addSql('CREATE INDEX IDX_F284D94D2F7B13D ON friend_request (user_to_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE friend_request DROP FOREIGN KEY FK_F284D9420C3C701');
        $this->addSql('ALTER TABLE friend_request DROP FOREIGN KEY FK_F284D94D2F7B13D');
        $this->addSql('DROP INDEX IDX_F284D9420C3C701 ON friend_request');
        $this->addSql('DROP INDEX IDX_F284D94D2F7B13D ON friend_request');
        $this->addSql('ALTER TABLE friend_request ADD user_a_id INT NOT NULL, ADD user_b_id INT NOT NULL, DROP user_from_id, DROP user_to_id');
        $this->addSql('ALTER TABLE friend_request ADD CONSTRAINT FK_F284D94415F1F91 FOREIGN KEY (user_a_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE friend_request ADD CONSTRAINT FK_F284D9453EAB07F FOREIGN KEY (user_b_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_F284D94415F1F91 ON friend_request (user_a_id)');
        $this->addSql('CREATE INDEX IDX_F284D9453EAB07F ON friend_request (user_b_id)');
    }
}
