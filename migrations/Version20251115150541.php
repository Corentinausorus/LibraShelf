<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251115150541 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE ouvrage_auteur (ouvrage_id INTEGER NOT NULL, auteur_id INTEGER NOT NULL, PRIMARY KEY(ouvrage_id, auteur_id), CONSTRAINT FK_3E39E6E815D884B5 FOREIGN KEY (ouvrage_id) REFERENCES ouvrage (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_3E39E6E860BB6FE6 FOREIGN KEY (auteur_id) REFERENCES auteur (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_3E39E6E815D884B5 ON ouvrage_auteur (ouvrage_id)');
        $this->addSql('CREATE INDEX IDX_3E39E6E860BB6FE6 ON ouvrage_auteur (auteur_id)');
        $this->addSql('CREATE TABLE ouvrage_categorie (ouvrage_id INTEGER NOT NULL, categorie_id INTEGER NOT NULL, PRIMARY KEY(ouvrage_id, categorie_id), CONSTRAINT FK_BF5721A615D884B5 FOREIGN KEY (ouvrage_id) REFERENCES ouvrage (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_BF5721A6BCF5E72D FOREIGN KEY (categorie_id) REFERENCES categorie (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_BF5721A615D884B5 ON ouvrage_categorie (ouvrage_id)');
        $this->addSql('CREATE INDEX IDX_BF5721A6BCF5E72D ON ouvrage_categorie (categorie_id)');
        $this->addSql('CREATE TABLE ouvrage_tags (ouvrage_id INTEGER NOT NULL, tags_id INTEGER NOT NULL, PRIMARY KEY(ouvrage_id, tags_id), CONSTRAINT FK_75B68AAC15D884B5 FOREIGN KEY (ouvrage_id) REFERENCES ouvrage (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_75B68AAC8D7B4FB4 FOREIGN KEY (tags_id) REFERENCES tags (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_75B68AAC15D884B5 ON ouvrage_tags (ouvrage_id)');
        $this->addSql('CREATE INDEX IDX_75B68AAC8D7B4FB4 ON ouvrage_tags (tags_id)');
        $this->addSql('DROP TABLE auteur_ouvrage');
        $this->addSql('CREATE TEMPORARY TABLE __temp__historique_inventaire AS SELECT id, exemplaire_id, type FROM historique_inventaire');
        $this->addSql('DROP TABLE historique_inventaire');
        $this->addSql('CREATE TABLE historique_inventaire (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, exemplaires_id INTEGER DEFAULT NULL, type VARCHAR(255) NOT NULL, CONSTRAINT FK_4BDD9A09AB40EED1 FOREIGN KEY (exemplaires_id) REFERENCES exemplaires (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO historique_inventaire (id, exemplaires_id, type) SELECT id, exemplaire_id, type FROM __temp__historique_inventaire');
        $this->addSql('DROP TABLE __temp__historique_inventaire');
        $this->addSql('CREATE INDEX IDX_4BDD9A09AB40EED1 ON historique_inventaire (exemplaires_id)');
        $this->addSql('CREATE TEMPORARY TABLE __temp__ouvrage AS SELECT id, editeur_id, titre, isbn, langues, année, resume FROM ouvrage');
        $this->addSql('DROP TABLE ouvrage');
        $this->addSql('CREATE TABLE ouvrage (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, editeur_id INTEGER DEFAULT NULL, titre VARCHAR(255) NOT NULL, isbn VARCHAR(255) NOT NULL, langues CLOB DEFAULT NULL --(DC2Type:json)
        , année DATETIME DEFAULT NULL --(DC2Type:datetime_immutable)
        , resume VARCHAR(255) DEFAULT NULL, CONSTRAINT FK_52A8CBD83375BD21 FOREIGN KEY (editeur_id) REFERENCES editeur (id) ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO ouvrage (id, editeur_id, titre, isbn, langues, année, resume) SELECT id, editeur_id, titre, isbn, langues, année, resume FROM __temp__ouvrage');
        $this->addSql('DROP TABLE __temp__ouvrage');
        $this->addSql('CREATE INDEX IDX_52A8CBD83375BD21 ON ouvrage (editeur_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE auteur_ouvrage (auteur_id INTEGER NOT NULL, ouvrage_id INTEGER NOT NULL, PRIMARY KEY(auteur_id, ouvrage_id), CONSTRAINT FK_EC8A08BD60BB6FE6 FOREIGN KEY (auteur_id) REFERENCES auteur (id) ON UPDATE NO ACTION ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_EC8A08BD15D884B5 FOREIGN KEY (ouvrage_id) REFERENCES ouvrage (id) ON UPDATE NO ACTION ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_EC8A08BD15D884B5 ON auteur_ouvrage (ouvrage_id)');
        $this->addSql('CREATE INDEX IDX_EC8A08BD60BB6FE6 ON auteur_ouvrage (auteur_id)');
        $this->addSql('DROP TABLE ouvrage_auteur');
        $this->addSql('DROP TABLE ouvrage_categorie');
        $this->addSql('DROP TABLE ouvrage_tags');
        $this->addSql('CREATE TEMPORARY TABLE __temp__historique_inventaire AS SELECT id, exemplaires_id, type FROM historique_inventaire');
        $this->addSql('DROP TABLE historique_inventaire');
        $this->addSql('CREATE TABLE historique_inventaire (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, exemplaire_id INTEGER DEFAULT NULL, type VARCHAR(255) NOT NULL, CONSTRAINT FK_4BDD9A095843AA21 FOREIGN KEY (exemplaire_id) REFERENCES exemplaires (id) ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO historique_inventaire (id, exemplaire_id, type) SELECT id, exemplaires_id, type FROM __temp__historique_inventaire');
        $this->addSql('DROP TABLE __temp__historique_inventaire');
        $this->addSql('CREATE INDEX IDX_4BDD9A095843AA21 ON historique_inventaire (exemplaire_id)');
        $this->addSql('CREATE TEMPORARY TABLE __temp__ouvrage AS SELECT id, editeur_id, titre, isbn, langues, année, resume FROM ouvrage');
        $this->addSql('DROP TABLE ouvrage');
        $this->addSql('CREATE TABLE ouvrage (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, editeur_id INTEGER DEFAULT NULL, titre VARCHAR(255) NOT NULL, isbn INTEGER NOT NULL, langues CLOB DEFAULT NULL --(DC2Type:array)
        , année DATE DEFAULT NULL, resume VARCHAR(255) DEFAULT NULL, CONSTRAINT FK_52A8CBD83375BD21 FOREIGN KEY (editeur_id) REFERENCES editeur (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO ouvrage (id, editeur_id, titre, isbn, langues, année, resume) SELECT id, editeur_id, titre, isbn, langues, année, resume FROM __temp__ouvrage');
        $this->addSql('DROP TABLE __temp__ouvrage');
        $this->addSql('CREATE INDEX IDX_52A8CBD83375BD21 ON ouvrage (editeur_id)');
    }
}
