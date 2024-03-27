<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240327104400 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE time_entry RENAME TO timer');
        $this->addSql('ALTER INDEX idx_6e537c0c7e3c61f9 RENAME TO IDX_6AD0DE1A7E3C61F9');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE timer RENAME TO time_entry');
        $this->addSql('ALTER INDEX IDX_6AD0DE1A7E3C61F9 RENAME TO idx_6e537c0c7e3c61f9');
    }
}
