<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251125005549 extends AbstractMigration
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
        $this->addSql('CREATE TEMPORARY TABLE __temp__editeur AS SELECT id, nom FROM editeur');
        $this->addSql('DROP TABLE editeur');
        $this->addSql('CREATE TABLE editeur (id INTEGER NOT NULL, nom VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('INSERT INTO editeur (id, nom) SELECT id, nom FROM __temp__editeur');
        $this->addSql('DROP TABLE __temp__editeur');
        $this->addSql('CREATE TEMPORARY TABLE __temp__exemplaires AS SELECT id, reservation_id, ouvrage_id, cote, etat, disponible FROM exemplaires');
        $this->addSql('DROP TABLE exemplaires');
        $this->addSql('CREATE TABLE exemplaires (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, reservation_id INTEGER DEFAULT NULL, ouvrage_id INTEGER DEFAULT NULL, cote VARCHAR(10) NOT NULL, etat VARCHAR(40) NOT NULL, disponible BOOLEAN NOT NULL, CONSTRAINT FK_551C55FB83297E7 FOREIGN KEY (reservation_id) REFERENCES reservation (id) ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_551C55F15D884B5 FOREIGN KEY (ouvrage_id) REFERENCES ouvrage (id) ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO exemplaires (id, reservation_id, ouvrage_id, cote, etat, disponible) SELECT id, reservation_id, ouvrage_id, cote, etat, disponible FROM __temp__exemplaires');
        $this->addSql('DROP TABLE __temp__exemplaires');
        $this->addSql('CREATE INDEX IDX_551C55F15D884B5 ON exemplaires (ouvrage_id)');
        $this->addSql('CREATE INDEX IDX_551C55FB83297E7 ON exemplaires (reservation_id)');
        $this->addSql('CREATE TEMPORARY TABLE __temp__reservation AS SELECT id, user_id, creation_date FROM reservation');
        $this->addSql('DROP TABLE reservation');
        $this->addSql('CREATE TABLE reservation (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, user_id INTEGER DEFAULT NULL, exemplaire_id INTEGER DEFAULT NULL, ouvrage_id INTEGER NOT NULL, creation_date DATETIME NOT NULL --(DC2Type:datetime_immutable)
        , statut VARCHAR(255) NOT NULL, CONSTRAINT FK_42C84955A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_42C849555843AA21 FOREIGN KEY (exemplaire_id) REFERENCES exemplaires (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_42C8495515D884B5 FOREIGN KEY (ouvrage_id) REFERENCES ouvrage (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO reservation (id, user_id, creation_date) SELECT id, user_id, creation_date FROM __temp__reservation');
        $this->addSql('DROP TABLE __temp__reservation');
        $this->addSql('CREATE INDEX IDX_42C84955A76ED395 ON reservation (user_id)');
        $this->addSql('CREATE INDEX IDX_42C849555843AA21 ON reservation (exemplaire_id)');
        $this->addSql('CREATE INDEX IDX_42C8495515D884B5 ON reservation (ouvrage_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE parametre_emprunt');
        $this->addSql('CREATE TEMPORARY TABLE __temp__editeur AS SELECT id, nom FROM editeur');
        $this->addSql('DROP TABLE editeur');
        $this->addSql('CREATE TABLE editeur (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, nom VARCHAR(255) NOT NULL)');
        $this->addSql('INSERT INTO editeur (id, nom) SELECT id, nom FROM __temp__editeur');
        $this->addSql('DROP TABLE __temp__editeur');
        $this->addSql('CREATE TEMPORARY TABLE __temp__exemplaires AS SELECT id, reservation_id, ouvrage_id, cote, etat, disponible FROM exemplaires');
        $this->addSql('DROP TABLE exemplaires');
        $this->addSql('CREATE TABLE exemplaires (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, reservation_id INTEGER DEFAULT NULL, ouvrage_id INTEGER DEFAULT NULL, cote VARCHAR(4) NOT NULL, etat VARCHAR(40) NOT NULL, disponible BOOLEAN NOT NULL, CONSTRAINT FK_551C55FB83297E7 FOREIGN KEY (reservation_id) REFERENCES reservation (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_551C55F15D884B5 FOREIGN KEY (ouvrage_id) REFERENCES ouvrage (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO exemplaires (id, reservation_id, ouvrage_id, cote, etat, disponible) SELECT id, reservation_id, ouvrage_id, cote, etat, disponible FROM __temp__exemplaires');
        $this->addSql('DROP TABLE __temp__exemplaires');
        $this->addSql('CREATE INDEX IDX_551C55FB83297E7 ON exemplaires (reservation_id)');
        $this->addSql('CREATE INDEX IDX_551C55F15D884B5 ON exemplaires (ouvrage_id)');
        $this->addSql('CREATE TEMPORARY TABLE __temp__reservation AS SELECT id, user_id, creation_date FROM reservation');
        $this->addSql('DROP TABLE reservation');
        $this->addSql('CREATE TABLE reservation (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, user_id INTEGER DEFAULT NULL, creation_date DATETIME NOT NULL --(DC2Type:datetime_immutable)
        , CONSTRAINT FK_42C84955A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO reservation (id, user_id, creation_date) SELECT id, user_id, creation_date FROM __temp__reservation');
        $this->addSql('DROP TABLE __temp__reservation');
        $this->addSql('CREATE INDEX IDX_42C84955A76ED395 ON reservation (user_id)');
    }
}
