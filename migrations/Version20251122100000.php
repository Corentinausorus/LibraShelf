<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20251122100000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create initial editeur table required by later migrations';
    }

    public function up(Schema $schema): void
    {
        // Create editeur table if it does not exist
        $this->addSql('CREATE TABLE IF NOT EXISTS editeur (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, nom VARCHAR(255) NOT NULL)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE IF EXISTS editeur');
    }
}
