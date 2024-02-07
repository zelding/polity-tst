<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240207180643 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE messenger_messages (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, body CLOB NOT NULL, headers CLOB NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL)');
        $this->addSql('CREATE INDEX IDX_75EA56E0FB7336F0 ON messenger_messages (queue_name)');
        $this->addSql('CREATE INDEX IDX_75EA56E0E3BD61CE ON messenger_messages (available_at)');
        $this->addSql('CREATE INDEX IDX_75EA56E016BA31DB ON messenger_messages (delivered_at)');
        $this->addSql('CREATE TEMPORARY TABLE __temp__member AS SELECT id, full_name, country, ep_political_group, national_political_group FROM member');
        $this->addSql('DROP TABLE member');
        $this->addSql('CREATE TABLE member (id INTEGER NOT NULL, full_name VARCHAR(255) NOT NULL, country VARCHAR(255) NOT NULL, ep_political_group VARCHAR(255) NOT NULL, national_political_group VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('INSERT INTO member (id, full_name, country, ep_political_group, national_political_group) SELECT id, full_name, country, ep_political_group, national_political_group FROM __temp__member');
        $this->addSql('DROP TABLE __temp__member');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE messenger_messages');
        $this->addSql('CREATE TEMPORARY TABLE __temp__member AS SELECT id, full_name, country, ep_political_group, national_political_group FROM member');
        $this->addSql('DROP TABLE member');
        $this->addSql('CREATE TABLE member (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, full_name VARCHAR(255) NOT NULL, country VARCHAR(255) NOT NULL, ep_political_group VARCHAR(255) NOT NULL, national_political_group VARCHAR(255) NOT NULL)');
        $this->addSql('INSERT INTO member (id, full_name, country, ep_political_group, national_political_group) SELECT id, full_name, country, ep_political_group, national_political_group FROM __temp__member');
        $this->addSql('DROP TABLE __temp__member');
    }
}
