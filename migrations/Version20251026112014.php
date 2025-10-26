<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251026112014 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE historique_inventaire (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, exemplaire_id INTEGER DEFAULT NULL, type VARCHAR(255) NOT NULL, CONSTRAINT FK_4BDD9A095843AA21 FOREIGN KEY (exemplaire_id) REFERENCES exemplaires (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_4BDD9A095843AA21 ON historique_inventaire (exemplaire_id)');
        $this->addSql('CREATE TABLE penalites (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, user_id INTEGER NOT NULL, montant INTEGER NOT NULL, raison CLOB NOT NULL --(DC2Type:simple_array)
        , CONSTRAINT FK_E12CFCEA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_E12CFCEA76ED395 ON penalites (user_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE historique_inventaire');
        $this->addSql('DROP TABLE penalites');
    }
}
