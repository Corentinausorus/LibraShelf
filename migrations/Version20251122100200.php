<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20251122100200 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create initial emprunt table required by later migrations';
    }

    public function up(Schema $schema): void
    {
        // Create emprunt table with minimal columns expected by later migrations
        $this->addSql('CREATE TABLE IF NOT EXISTS emprunt (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, user_id INTEGER NOT NULL, CONSTRAINT FK_364071D7A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IF NOT EXISTS IDX_364071D7A76ED395 ON emprunt (user_id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE IF EXISTS emprunt');
    }
}
