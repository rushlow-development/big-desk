<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240319093206 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add timer support';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE time_entry (
          started_at TIMESTAMP(6) WITHOUT TIME ZONE NOT NULL,
          accumulated_time INT NOT NULL,
          running BOOLEAN NOT NULL,
          last_restarted_at TIMESTAMP(6) WITHOUT TIME ZONE DEFAULT NULL,
          id UUID NOT NULL,
          PRIMARY KEY(id)
        )');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE time_entry');
    }
}
