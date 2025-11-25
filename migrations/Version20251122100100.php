<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20251122100100 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create initial user table required by later migrations';
    }

    public function up(Schema $schema): void
    {
        // Create user table with minimal columns expected by later migrations
        $this->addSql('CREATE TABLE IF NOT EXISTS user (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, email VARCHAR(180) NOT NULL, password VARCHAR(255) NOT NULL, nom VARCHAR(255) NOT NULL, role VARCHAR(50) DEFAULT \''.'ROLE_MEMBER\''. ' NOT NULL)');
        $this->addSql('CREATE UNIQUE INDEX IF NOT EXISTS UNIQ_IDENTIFIER_EMAIL ON user (email)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP INDEX IF EXISTS UNIQ_IDENTIFIER_EMAIL');
        $this->addSql('DROP TABLE IF EXISTS user');
    }
}
