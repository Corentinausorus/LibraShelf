<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251227113241 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE auteur (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, nom VARCHAR(255) NOT NULL)');
        $this->addSql('CREATE TABLE categorie (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, nom VARCHAR(255) NOT NULL)');
        $this->addSql('CREATE TABLE categorie_ouvrage (categorie_id INTEGER NOT NULL, ouvrage_id INTEGER NOT NULL, PRIMARY KEY(categorie_id, ouvrage_id), CONSTRAINT FK_D2B657ABCF5E72D FOREIGN KEY (categorie_id) REFERENCES categorie (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_D2B657A15D884B5 FOREIGN KEY (ouvrage_id) REFERENCES ouvrage (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_D2B657ABCF5E72D ON categorie_ouvrage (categorie_id)');
        $this->addSql('CREATE INDEX IDX_D2B657A15D884B5 ON categorie_ouvrage (ouvrage_id)');
        $this->addSql('CREATE TABLE editeur (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, nom VARCHAR(255) NOT NULL)');
        $this->addSql('CREATE TABLE emprunt (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, user_id INTEGER NOT NULL, exemplaire_id INTEGER DEFAULT NULL, start_at DATETIME NOT NULL --(DC2Type:datetime_immutable)
        , due_at DATETIME NOT NULL --(DC2Type:datetime_immutable)
        , returned_at DATETIME NOT NULL --(DC2Type:datetime_immutable)
        , penalty DOUBLE PRECISION DEFAULT NULL, status VARCHAR(255) NOT NULL, CONSTRAINT FK_364071D7A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_364071D75843AA21 FOREIGN KEY (exemplaire_id) REFERENCES exemplaires (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_364071D7A76ED395 ON emprunt (user_id)');
        $this->addSql('CREATE INDEX IDX_364071D75843AA21 ON emprunt (exemplaire_id)');
        $this->addSql('CREATE TABLE exemplaires (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, reservation_id INTEGER DEFAULT NULL, ouvrage_id INTEGER DEFAULT NULL, cote VARCHAR(10) NOT NULL, etat VARCHAR(255) NOT NULL, disponible BOOLEAN NOT NULL, CONSTRAINT FK_551C55FB83297E7 FOREIGN KEY (reservation_id) REFERENCES reservation (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_551C55F15D884B5 FOREIGN KEY (ouvrage_id) REFERENCES ouvrage (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_551C55FB83297E7 ON exemplaires (reservation_id)');
        $this->addSql('CREATE INDEX IDX_551C55F15D884B5 ON exemplaires (ouvrage_id)');
        $this->addSql('CREATE TABLE historique_inventaire (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, exemplaires_id INTEGER DEFAULT NULL, type VARCHAR(255) NOT NULL, CONSTRAINT FK_4BDD9A09AB40EED1 FOREIGN KEY (exemplaires_id) REFERENCES exemplaires (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_4BDD9A09AB40EED1 ON historique_inventaire (exemplaires_id)');
        $this->addSql('CREATE TABLE notifications (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, type CLOB NOT NULL --(DC2Type:simple_array)
        , to_email VARCHAR(255) DEFAULT NULL, to_sms VARCHAR(255) DEFAULT NULL, subject VARCHAR(255) NOT NULL, body CLOB NOT NULL)');
        $this->addSql('CREATE TABLE ouvrage (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, editeur_id INTEGER DEFAULT NULL, created_by_id INTEGER NOT NULL, titre VARCHAR(255) NOT NULL, isbn VARCHAR(255) NOT NULL, langues CLOB DEFAULT NULL --(DC2Type:json)
        , Année DATETIME DEFAULT NULL --(DC2Type:datetime_immutable)
        , resume VARCHAR(255) DEFAULT NULL, CONSTRAINT FK_52A8CBD83375BD21 FOREIGN KEY (editeur_id) REFERENCES editeur (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_52A8CBD8B03A8386 FOREIGN KEY (created_by_id) REFERENCES user (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_52A8CBD83375BD21 ON ouvrage (editeur_id)');
        $this->addSql('CREATE INDEX IDX_52A8CBD8B03A8386 ON ouvrage (created_by_id)');
        $this->addSql('CREATE TABLE ouvrage_auteur (ouvrage_id INTEGER NOT NULL, auteur_id INTEGER NOT NULL, PRIMARY KEY(ouvrage_id, auteur_id), CONSTRAINT FK_3E39E6E815D884B5 FOREIGN KEY (ouvrage_id) REFERENCES ouvrage (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_3E39E6E860BB6FE6 FOREIGN KEY (auteur_id) REFERENCES auteur (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_3E39E6E815D884B5 ON ouvrage_auteur (ouvrage_id)');
        $this->addSql('CREATE INDEX IDX_3E39E6E860BB6FE6 ON ouvrage_auteur (auteur_id)');
        $this->addSql('CREATE TABLE ouvrage_categorie (ouvrage_id INTEGER NOT NULL, categorie_id INTEGER NOT NULL, PRIMARY KEY(ouvrage_id, categorie_id), CONSTRAINT FK_BF5721A615D884B5 FOREIGN KEY (ouvrage_id) REFERENCES ouvrage (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_BF5721A6BCF5E72D FOREIGN KEY (categorie_id) REFERENCES categorie (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_BF5721A615D884B5 ON ouvrage_categorie (ouvrage_id)');
        $this->addSql('CREATE INDEX IDX_BF5721A6BCF5E72D ON ouvrage_categorie (categorie_id)');
        $this->addSql('CREATE TABLE ouvrage_tags (ouvrage_id INTEGER NOT NULL, tags_id INTEGER NOT NULL, PRIMARY KEY(ouvrage_id, tags_id), CONSTRAINT FK_75B68AAC15D884B5 FOREIGN KEY (ouvrage_id) REFERENCES ouvrage (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_75B68AAC8D7B4FB4 FOREIGN KEY (tags_id) REFERENCES tags (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_75B68AAC15D884B5 ON ouvrage_tags (ouvrage_id)');
        $this->addSql('CREATE INDEX IDX_75B68AAC8D7B4FB4 ON ouvrage_tags (tags_id)');
        $this->addSql('CREATE TABLE parametre_emprunt (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, emprunt_duree_jours INTEGER NOT NULL, penalite_centimes_par_jour INTEGER NOT NULL, jours_tolerance INTEGER NOT NULL, configuration DATETIME DEFAULT NULL --(DC2Type:datetime_immutable)
        )');
        $this->addSql('CREATE TABLE penalites (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, user_id INTEGER NOT NULL, montant INTEGER NOT NULL, raison CLOB NOT NULL --(DC2Type:simple_array)
        , CONSTRAINT FK_E12CFCEA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_E12CFCEA76ED395 ON penalites (user_id)');
        $this->addSql('CREATE TABLE reservation (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, exemplaire_id INTEGER DEFAULT NULL, user_id INTEGER DEFAULT NULL, ouvrage_id INTEGER NOT NULL, creation_date DATETIME NOT NULL --(DC2Type:datetime_immutable)
        , statut VARCHAR(255) NOT NULL, notified_at DATETIME DEFAULT NULL --(DC2Type:datetime_immutable)
        , CONSTRAINT FK_42C849555843AA21 FOREIGN KEY (exemplaire_id) REFERENCES exemplaires (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_42C84955A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_42C8495515D884B5 FOREIGN KEY (ouvrage_id) REFERENCES ouvrage (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_42C849555843AA21 ON reservation (exemplaire_id)');
        $this->addSql('CREATE INDEX IDX_42C84955A76ED395 ON reservation (user_id)');
        $this->addSql('CREATE INDEX IDX_42C8495515D884B5 ON reservation (ouvrage_id)');
        $this->addSql('CREATE TABLE tags (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, nom VARCHAR(100) NOT NULL)');
        $this->addSql('CREATE TABLE tags_ouvrage (tags_id INTEGER NOT NULL, ouvrage_id INTEGER NOT NULL, PRIMARY KEY(tags_id, ouvrage_id), CONSTRAINT FK_741420C98D7B4FB4 FOREIGN KEY (tags_id) REFERENCES tags (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_741420C915D884B5 FOREIGN KEY (ouvrage_id) REFERENCES ouvrage (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_741420C98D7B4FB4 ON tags_ouvrage (tags_id)');
        $this->addSql('CREATE INDEX IDX_741420C915D884B5 ON tags_ouvrage (ouvrage_id)');
        $this->addSql('CREATE TABLE user (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, email VARCHAR(180) NOT NULL, password VARCHAR(255) NOT NULL, nom VARCHAR(255) NOT NULL, role VARCHAR(50) DEFAULT \'ROLE_MEMBER\' NOT NULL)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL ON user (email)');
        $this->addSql('CREATE TABLE messenger_messages (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, body CLOB NOT NULL, headers CLOB NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL --(DC2Type:datetime_immutable)
        , available_at DATETIME NOT NULL --(DC2Type:datetime_immutable)
        , delivered_at DATETIME DEFAULT NULL --(DC2Type:datetime_immutable)
        )');
        $this->addSql('CREATE INDEX IDX_75EA56E0FB7336F0 ON messenger_messages (queue_name)');
        $this->addSql('CREATE INDEX IDX_75EA56E0E3BD61CE ON messenger_messages (available_at)');
        $this->addSql('CREATE INDEX IDX_75EA56E016BA31DB ON messenger_messages (delivered_at)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE auteur');
        $this->addSql('DROP TABLE categorie');
        $this->addSql('DROP TABLE categorie_ouvrage');
        $this->addSql('DROP TABLE editeur');
        $this->addSql('DROP TABLE emprunt');
        $this->addSql('DROP TABLE exemplaires');
        $this->addSql('DROP TABLE historique_inventaire');
        $this->addSql('DROP TABLE notifications');
        $this->addSql('DROP TABLE ouvrage');
        $this->addSql('DROP TABLE ouvrage_auteur');
        $this->addSql('DROP TABLE ouvrage_categorie');
        $this->addSql('DROP TABLE ouvrage_tags');
        $this->addSql('DROP TABLE parametre_emprunt');
        $this->addSql('DROP TABLE penalites');
        $this->addSql('DROP TABLE reservation');
        $this->addSql('DROP TABLE tags');
        $this->addSql('DROP TABLE tags_ouvrage');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
