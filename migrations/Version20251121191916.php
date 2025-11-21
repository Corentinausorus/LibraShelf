<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251121191916 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TEMPORARY TABLE __temp__editeur AS SELECT id, nom FROM editeur');
        $this->addSql('DROP TABLE editeur');
        $this->addSql('CREATE TABLE editeur (id INTEGER NOT NULL, nom VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('INSERT INTO editeur (id, nom) SELECT id, nom FROM __temp__editeur');
        $this->addSql('DROP TABLE __temp__editeur');
        $this->addSql('CREATE TEMPORARY TABLE __temp__ouvrage AS SELECT id, editeur_id, titre, isbn, langues, année, resume FROM ouvrage');
        $this->addSql('DROP TABLE ouvrage');
        $this->addSql('CREATE TABLE ouvrage (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, editeur_id INTEGER DEFAULT NULL, created_by_id INTEGER DEFAULT NULL, titre VARCHAR(255) NOT NULL, isbn VARCHAR(255) NOT NULL, langues CLOB DEFAULT NULL --(DC2Type:json)
        , année DATETIME DEFAULT NULL --(DC2Type:datetime_immutable)
        , resume VARCHAR(255) DEFAULT NULL, CONSTRAINT FK_52A8CBD83375BD21 FOREIGN KEY (editeur_id) REFERENCES editeur (id) ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_52A8CBD8B03A8386 FOREIGN KEY (created_by_id) REFERENCES user (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO ouvrage (id, editeur_id, titre, isbn, langues, année, resume) SELECT id, editeur_id, titre, isbn, langues, année, resume FROM __temp__ouvrage');
        $this->addSql('DROP TABLE __temp__ouvrage');
        $this->addSql('CREATE INDEX IDX_52A8CBD83375BD21 ON ouvrage (editeur_id)');
        $this->addSql('CREATE INDEX IDX_52A8CBD8B03A8386 ON ouvrage (created_by_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TEMPORARY TABLE __temp__editeur AS SELECT id, nom FROM editeur');
        $this->addSql('DROP TABLE editeur');
        $this->addSql('CREATE TABLE editeur (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, nom VARCHAR(255) NOT NULL)');
        $this->addSql('INSERT INTO editeur (id, nom) SELECT id, nom FROM __temp__editeur');
        $this->addSql('DROP TABLE __temp__editeur');
        $this->addSql('CREATE TEMPORARY TABLE __temp__ouvrage AS SELECT id, editeur_id, titre, isbn, langues, année, resume FROM ouvrage');
        $this->addSql('DROP TABLE ouvrage');
        $this->addSql('CREATE TABLE ouvrage (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, editeur_id INTEGER DEFAULT NULL, titre VARCHAR(255) NOT NULL, isbn VARCHAR(255) NOT NULL, langues CLOB DEFAULT NULL --(DC2Type:json)
        , année DATETIME DEFAULT NULL --(DC2Type:datetime_immutable)
        , resume VARCHAR(255) DEFAULT NULL, CONSTRAINT FK_52A8CBD83375BD21 FOREIGN KEY (editeur_id) REFERENCES editeur (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO ouvrage (id, editeur_id, titre, isbn, langues, année, resume) SELECT id, editeur_id, titre, isbn, langues, année, resume FROM __temp__ouvrage');
        $this->addSql('DROP TABLE __temp__ouvrage');
        $this->addSql('CREATE INDEX IDX_52A8CBD83375BD21 ON ouvrage (editeur_id)');
    }
}
