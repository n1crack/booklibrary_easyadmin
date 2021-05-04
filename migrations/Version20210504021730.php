<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210504021730 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE app_users ADD COLUMN is_verified BOOLEAN NOT NULL');
        $this->addSql('DROP INDEX IDX_CBE5A33112469DE2');
        $this->addSql('CREATE TEMPORARY TABLE __temp__book AS SELECT id, category_id, name, author, image FROM book');
        $this->addSql('DROP TABLE book');
        $this->addSql('CREATE TABLE book (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, category_id INTEGER NOT NULL, name VARCHAR(255) NOT NULL COLLATE BINARY, author VARCHAR(255) NOT NULL COLLATE BINARY, image VARCHAR(255) NOT NULL COLLATE BINARY, CONSTRAINT FK_CBE5A33112469DE2 FOREIGN KEY (category_id) REFERENCES category (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO book (id, category_id, name, author, image) SELECT id, category_id, name, author, image FROM __temp__book');
        $this->addSql('DROP TABLE __temp__book');
        $this->addSql('CREATE INDEX IDX_CBE5A33112469DE2 ON book (category_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX UNIQ_C2502824F85E0677');
        $this->addSql('DROP INDEX UNIQ_C2502824E7927C74');
        $this->addSql('CREATE TEMPORARY TABLE __temp__app_users AS SELECT id, username, password, email, is_active FROM app_users');
        $this->addSql('DROP TABLE app_users');
        $this->addSql('CREATE TABLE app_users (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, username VARCHAR(25) NOT NULL, password VARCHAR(64) NOT NULL, email VARCHAR(254) NOT NULL, is_active BOOLEAN NOT NULL)');
        $this->addSql('INSERT INTO app_users (id, username, password, email, is_active) SELECT id, username, password, email, is_active FROM __temp__app_users');
        $this->addSql('DROP TABLE __temp__app_users');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_C2502824F85E0677 ON app_users (username)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_C2502824E7927C74 ON app_users (email)');
        $this->addSql('DROP INDEX IDX_CBE5A33112469DE2');
        $this->addSql('CREATE TEMPORARY TABLE __temp__book AS SELECT id, category_id, name, author, image FROM book');
        $this->addSql('DROP TABLE book');
        $this->addSql('CREATE TABLE book (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, category_id INTEGER NOT NULL, name VARCHAR(255) NOT NULL, author VARCHAR(255) NOT NULL, image VARCHAR(255) NOT NULL)');
        $this->addSql('INSERT INTO book (id, category_id, name, author, image) SELECT id, category_id, name, author, image FROM __temp__book');
        $this->addSql('DROP TABLE __temp__book');
        $this->addSql('CREATE INDEX IDX_CBE5A33112469DE2 ON book (category_id)');
    }
}
