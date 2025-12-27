<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251227103321 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE parametre_emprunt (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, emprunt_duree_jours INTEGER NOT NULL, penalite_centimes_par_jour INTEGER NOT NULL, jours_tolerance INTEGER NOT NULL, configuration DATETIME DEFAULT NULL --(DC2Type:datetime_immutable)
        )');
        $this->addSql('CREATE TEMPORARY TABLE __temp__emprunt AS SELECT id, user_id, exemplaire_id, start_at, due_at, returned_at, penalty, status FROM emprunt');
        $this->addSql('DROP TABLE emprunt');
        $this->addSql('CREATE TABLE emprunt (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, user_id INTEGER NOT NULL, exemplaire_id INTEGER DEFAULT NULL, start_at DATETIME NOT NULL --(DC2Type:datetime_immutable)
        , due_at DATETIME NOT NULL --(DC2Type:datetime_immutable)
        , returned_at DATETIME NOT NULL --(DC2Type:datetime_immutable)
        , penalty DOUBLE PRECISION DEFAULT NULL, status VARCHAR(255) NOT NULL, CONSTRAINT FK_364071D7A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_364071D75843AA21 FOREIGN KEY (exemplaire_id) REFERENCES exemplaires (id) ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO emprunt (id, user_id, exemplaire_id, start_at, due_at, returned_at, penalty, status) SELECT id, user_id, exemplaire_id, start_at, due_at, returned_at, penalty, status FROM __temp__emprunt');
        $this->addSql('DROP TABLE __temp__emprunt');
        $this->addSql('CREATE INDEX IDX_364071D7A76ED395 ON emprunt (user_id)');
        $this->addSql('CREATE INDEX IDX_364071D75843AA21 ON emprunt (exemplaire_id)');
        $this->addSql('CREATE TEMPORARY TABLE __temp__ouvrage AS SELECT id, editeur_id, created_by_id, titre, isbn, langues, année, resume FROM ouvrage');
        $this->addSql('DROP TABLE ouvrage');
        $this->addSql('CREATE TABLE ouvrage (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, editeur_id INTEGER DEFAULT NULL, created_by_id INTEGER NOT NULL, titre VARCHAR(255) NOT NULL, isbn VARCHAR(255) NOT NULL, langues CLOB DEFAULT NULL --(DC2Type:json)
        , année DATETIME DEFAULT NULL --(DC2Type:datetime_immutable)
        , resume VARCHAR(255) DEFAULT NULL, CONSTRAINT FK_52A8CBD83375BD21 FOREIGN KEY (editeur_id) REFERENCES editeur (id) ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_52A8CBD8B03A8386 FOREIGN KEY (created_by_id) REFERENCES user (id) ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO ouvrage (id, editeur_id, created_by_id, titre, isbn, langues, année, resume) SELECT id, editeur_id, created_by_id, titre, isbn, langues, année, resume FROM __temp__ouvrage');
        $this->addSql('DROP TABLE __temp__ouvrage');
        $this->addSql('CREATE INDEX IDX_52A8CBD8B03A8386 ON ouvrage (created_by_id)');
        $this->addSql('CREATE INDEX IDX_52A8CBD83375BD21 ON ouvrage (editeur_id)');
        $this->addSql('CREATE TEMPORARY TABLE __temp__reservation AS SELECT id, user_id, ouvrage_id, exemplaire_id, creation_date, statut FROM reservation');
        $this->addSql('DROP TABLE reservation');
        $this->addSql('CREATE TABLE reservation (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, user_id INTEGER DEFAULT NULL, ouvrage_id INTEGER NOT NULL, exemplaire_id INTEGER DEFAULT NULL, creation_date DATETIME NOT NULL --(DC2Type:datetime_immutable)
        , statut VARCHAR(255) NOT NULL, notified_at DATETIME DEFAULT NULL --(DC2Type:datetime_immutable)
        , CONSTRAINT FK_42C84955A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_42C8495515D884B5 FOREIGN KEY (ouvrage_id) REFERENCES ouvrage (id) ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_42C849555843AA21 FOREIGN KEY (exemplaire_id) REFERENCES exemplaires (id) ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO reservation (id, user_id, ouvrage_id, exemplaire_id, creation_date, statut) SELECT id, user_id, ouvrage_id, exemplaire_id, creation_date, statut FROM __temp__reservation');
        $this->addSql('DROP TABLE __temp__reservation');
        $this->addSql('CREATE INDEX IDX_42C849555843AA21 ON reservation (exemplaire_id)');
        $this->addSql('CREATE INDEX IDX_42C84955A76ED395 ON reservation (user_id)');
        $this->addSql('CREATE INDEX IDX_42C8495515D884B5 ON reservation (ouvrage_id)');
        $this->addSql('ALTER TABLE tags ADD COLUMN nom VARCHAR(100) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE parametre_emprunt');
        $this->addSql('CREATE TEMPORARY TABLE __temp__emprunt AS SELECT id, user_id, exemplaire_id, start_at, due_at, returned_at, penalty, status FROM emprunt');
        $this->addSql('DROP TABLE emprunt');
        $this->addSql('CREATE TABLE emprunt (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, user_id INTEGER NOT NULL, exemplaire_id INTEGER DEFAULT NULL, start_at DATETIME NOT NULL --(DC2Type:datetime_immutable)
        , due_at DATETIME NOT NULL --(DC2Type:datetime_immutable)
        , returned_at DATETIME NOT NULL --(DC2Type:datetime_immutable)
        , penalty DOUBLE PRECISION DEFAULT NULL, status VARCHAR(255) NOT NULL, CONSTRAINT FK_364071D7A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_364071D75843AA21 FOREIGN KEY (exemplaire_id) REFERENCES exemplaires (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO emprunt (id, user_id, exemplaire_id, start_at, due_at, returned_at, penalty, status) SELECT id, user_id, exemplaire_id, start_at, due_at, returned_at, penalty, status FROM __temp__emprunt');
        $this->addSql('DROP TABLE __temp__emprunt');
        $this->addSql('CREATE INDEX IDX_364071D7A76ED395 ON emprunt (user_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_364071D75843AA21 ON emprunt (exemplaire_id)');
        $this->addSql('CREATE TEMPORARY TABLE __temp__ouvrage AS SELECT id, editeur_id, created_by_id, titre, isbn, langues, Année, resume FROM ouvrage');
        $this->addSql('DROP TABLE ouvrage');
        $this->addSql('CREATE TABLE ouvrage (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, editeur_id INTEGER DEFAULT NULL, created_by_id INTEGER DEFAULT NULL, titre VARCHAR(255) NOT NULL, isbn VARCHAR(255) NOT NULL, langues CLOB DEFAULT NULL --(DC2Type:json)
        , Année DATETIME DEFAULT NULL --(DC2Type:datetime_immutable)
        , resume VARCHAR(255) DEFAULT NULL, CONSTRAINT FK_52A8CBD83375BD21 FOREIGN KEY (editeur_id) REFERENCES editeur (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_52A8CBD8B03A8386 FOREIGN KEY (created_by_id) REFERENCES user (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO ouvrage (id, editeur_id, created_by_id, titre, isbn, langues, Année, resume) SELECT id, editeur_id, created_by_id, titre, isbn, langues, Année, resume FROM __temp__ouvrage');
        $this->addSql('DROP TABLE __temp__ouvrage');
        $this->addSql('CREATE INDEX IDX_52A8CBD83375BD21 ON ouvrage (editeur_id)');
        $this->addSql('CREATE INDEX IDX_52A8CBD8B03A8386 ON ouvrage (created_by_id)');
        $this->addSql('CREATE TEMPORARY TABLE __temp__reservation AS SELECT id, exemplaire_id, user_id, ouvrage_id, creation_date, statut FROM reservation');
        $this->addSql('DROP TABLE reservation');
        $this->addSql('CREATE TABLE reservation (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, exemplaire_id INTEGER DEFAULT NULL, user_id INTEGER DEFAULT NULL, ouvrage_id INTEGER NOT NULL, creation_date DATETIME NOT NULL --(DC2Type:datetime_immutable)
        , statut VARCHAR(255) NOT NULL, CONSTRAINT FK_42C849555843AA21 FOREIGN KEY (exemplaire_id) REFERENCES exemplaires (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_42C84955A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_42C8495515D884B5 FOREIGN KEY (ouvrage_id) REFERENCES ouvrage (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO reservation (id, exemplaire_id, user_id, ouvrage_id, creation_date, statut) SELECT id, exemplaire_id, user_id, ouvrage_id, creation_date, statut FROM __temp__reservation');
        $this->addSql('DROP TABLE __temp__reservation');
        $this->addSql('CREATE INDEX IDX_42C849555843AA21 ON reservation (exemplaire_id)');
        $this->addSql('CREATE INDEX IDX_42C84955A76ED395 ON reservation (user_id)');
        $this->addSql('CREATE INDEX IDX_42C8495515D884B5 ON reservation (ouvrage_id)');
        $this->addSql('CREATE TEMPORARY TABLE __temp__tags AS SELECT id FROM tags');
        $this->addSql('DROP TABLE tags');
        $this->addSql('CREATE TABLE tags (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL)');
        $this->addSql('INSERT INTO tags (id) SELECT id FROM __temp__tags');
        $this->addSql('DROP TABLE __temp__tags');
    }
}
