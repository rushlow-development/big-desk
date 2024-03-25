<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240324183822 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add User and authentication';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE "user" (
          display_name VARCHAR(180) NOT NULL,
          username VARCHAR(180) NOT NULL,
          PASSWORD VARCHAR(255) NOT NULL,
          roles JSON NOT NULL,
          git_hub_token JSON DEFAULT NULL,
          id UUID NOT NULL,
          PRIMARY KEY(id)
        )');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL ON "user" (username, display_name)');
        $this->addSql('CREATE TABLE rememberme_token (
          series VARCHAR(88) NOT NULL,
          value VARCHAR(88) NOT NULL,
          lastUsed TIMESTAMP(6) WITHOUT TIME ZONE NOT NULL,
          class VARCHAR(100) NOT NULL,
          username VARCHAR(200) NOT NULL,
          PRIMARY KEY(series)
        )');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE "user"');
        $this->addSql('DROP TABLE rememberme_token');
    }
}