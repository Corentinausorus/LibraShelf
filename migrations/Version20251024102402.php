<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251024102402 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE auteur (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, nom VARCHAR(255) NOT NULL)');
        $this->addSql('CREATE TABLE auteur_ouvrage (auteur_id INTEGER NOT NULL, ouvrage_id INTEGER NOT NULL, PRIMARY KEY(auteur_id, ouvrage_id), CONSTRAINT FK_EC8A08BD60BB6FE6 FOREIGN KEY (auteur_id) REFERENCES auteur (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_EC8A08BD15D884B5 FOREIGN KEY (ouvrage_id) REFERENCES ouvrage (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_EC8A08BD60BB6FE6 ON auteur_ouvrage (auteur_id)');
        $this->addSql('CREATE INDEX IDX_EC8A08BD15D884B5 ON auteur_ouvrage (ouvrage_id)');
        $this->addSql('CREATE TABLE emprunt (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, user_id INTEGER NOT NULL, CONSTRAINT FK_364071D7A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_364071D7A76ED395 ON emprunt (user_id)');
        $this->addSql('CREATE TABLE user (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles CLOB NOT NULL --(DC2Type:json)
        , password VARCHAR(255) NOT NULL)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL ON user (email)');
        $this->addSql('CREATE TEMPORARY TABLE __temp__ouvrage AS SELECT id, titre, editeur, isbn, categories, tags, langues, année, resume FROM ouvrage');
        $this->addSql('DROP TABLE ouvrage');
        $this->addSql('CREATE TABLE ouvrage (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, titre VARCHAR(255) NOT NULL, editeur VARCHAR(255) NOT NULL, isbn INTEGER NOT NULL, categories CLOB DEFAULT NULL --(DC2Type:array)
        , tags CLOB DEFAULT NULL --(DC2Type:array)
        , langues CLOB DEFAULT NULL --(DC2Type:array)
        , année DATE DEFAULT NULL, resume VARCHAR(255) DEFAULT NULL)');
        $this->addSql('INSERT INTO ouvrage (id, titre, editeur, isbn, categories, tags, langues, année, resume) SELECT id, titre, editeur, isbn, categories, tags, langues, année, resume FROM __temp__ouvrage');
        $this->addSql('DROP TABLE __temp__ouvrage');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE auteur');
        $this->addSql('DROP TABLE auteur_ouvrage');
        $this->addSql('DROP TABLE emprunt');
        $this->addSql('DROP TABLE user');
        $this->addSql('ALTER TABLE ouvrage ADD COLUMN auteurs CLOB DEFAULT NULL');
    }
}
