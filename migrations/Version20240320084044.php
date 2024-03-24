<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240320084044 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Timers can have names';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE time_entry ADD name VARCHAR(255) NOT NULL DEFAULT \'\'');
        $this->addSql('ALTER TABLE time_entry ALTER COLUMN name DROP DEFAULT');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE time_entry DROP name');
    }
}
