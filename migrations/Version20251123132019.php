<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251123132019 extends AbstractMigration
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
        $this->addSql('CREATE TEMPORARY TABLE __temp__emprunt AS SELECT id, user_id FROM emprunt');
        $this->addSql('DROP TABLE emprunt');
        $this->addSql('CREATE TABLE emprunt (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, user_id INTEGER NOT NULL, exemplaire_id INTEGER DEFAULT NULL, start_at DATETIME NOT NULL --(DC2Type:datetime_immutable)
        , due_at DATETIME NOT NULL --(DC2Type:datetime_immutable)
        , returned_at DATETIME NOT NULL --(DC2Type:datetime_immutable)
        , penalty DOUBLE PRECISION DEFAULT NULL, status VARCHAR(255) NOT NULL, CONSTRAINT FK_364071D7A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_364071D75843AA21 FOREIGN KEY (exemplaire_id) REFERENCES exemplaires (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO emprunt (id, user_id) SELECT id, user_id FROM __temp__emprunt');
        $this->addSql('DROP TABLE __temp__emprunt');
        $this->addSql('CREATE INDEX IDX_364071D7A76ED395 ON emprunt (user_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_364071D75843AA21 ON emprunt (exemplaire_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TEMPORARY TABLE __temp__editeur AS SELECT id, nom FROM editeur');
        $this->addSql('DROP TABLE editeur');
        $this->addSql('CREATE TABLE editeur (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, nom VARCHAR(255) NOT NULL)');
        $this->addSql('INSERT INTO editeur (id, nom) SELECT id, nom FROM __temp__editeur');
        $this->addSql('DROP TABLE __temp__editeur');
        $this->addSql('CREATE TEMPORARY TABLE __temp__emprunt AS SELECT id, user_id FROM emprunt');
        $this->addSql('DROP TABLE emprunt');
        $this->addSql('CREATE TABLE emprunt (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, user_id INTEGER NOT NULL, CONSTRAINT FK_364071D7A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO emprunt (id, user_id) SELECT id, user_id FROM __temp__emprunt');
        $this->addSql('DROP TABLE __temp__emprunt');
        $this->addSql('CREATE INDEX IDX_364071D7A76ED395 ON emprunt (user_id)');
    }
}
