<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240204154416 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE member_contact (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, city VARCHAR(255) NOT NULL, address VARCHAR(255) NOT NULL, phone VARCHAR(255) NOT NULL, member_id INTEGER DEFAULT NULL, CONSTRAINT FK_F39651AC7597D3FE FOREIGN KEY (member_id) REFERENCES member (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_F39651AC7597D3FE ON member_contact (member_id)');
        $this->addSql('CREATE TEMPORARY TABLE __temp__member AS SELECT id, full_name, country, ep_political_group, national_political_group FROM member');
        $this->addSql('DROP TABLE member');
        $this->addSql('CREATE TABLE member (id INTEGER NOT NULL, full_name VARCHAR(255) NOT NULL, country VARCHAR(255) NOT NULL, ep_political_group VARCHAR(255) NOT NULL, national_political_group VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('INSERT INTO member (id, full_name, country, ep_political_group, national_political_group) SELECT id, full_name, country, ep_political_group, national_political_group FROM __temp__member');
        $this->addSql('DROP TABLE __temp__member');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE member_contact');
        $this->addSql('CREATE TEMPORARY TABLE __temp__member AS SELECT id, full_name, country, ep_political_group, national_political_group FROM member');
        $this->addSql('DROP TABLE member');
        $this->addSql('CREATE TABLE member (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, full_name VARCHAR(255) NOT NULL, country VARCHAR(255) NOT NULL, ep_political_group VARCHAR(255) NOT NULL, national_political_group VARCHAR(255) NOT NULL)');
        $this->addSql('INSERT INTO member (id, full_name, country, ep_political_group, national_political_group) SELECT id, full_name, country, ep_political_group, national_political_group FROM __temp__member');
        $this->addSql('DROP TABLE __temp__member');
    }
}
