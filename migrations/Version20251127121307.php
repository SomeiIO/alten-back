<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251127121307 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TEMPORARY TABLE __temp__product AS SELECT id, code, name, description, image, category, price, quantity, internal_reference, shell_id, inventory_status, rating, created_at, updated_at FROM product');
        $this->addSql('DROP TABLE product');
        $this->addSql('CREATE TABLE product (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, code VARCHAR(100) NOT NULL, name VARCHAR(255) NOT NULL, description CLOB DEFAULT NULL, image VARCHAR(255) DEFAULT NULL, category VARCHAR(100) DEFAULT NULL, price DOUBLE PRECISION NOT NULL, quantity INTEGER NOT NULL, internal_reference VARCHAR(150) DEFAULT NULL, shell_id INTEGER NOT NULL, inventory_status VARCHAR(255) NOT NULL, rating INTEGER NOT NULL, created_at INTEGER NOT NULL, updated_at INTEGER NOT NULL)');
        $this->addSql('INSERT INTO product (id, code, name, description, image, category, price, quantity, internal_reference, shell_id, inventory_status, rating, created_at, updated_at) SELECT id, code, name, description, image, category, price, quantity, internal_reference, shell_id, inventory_status, rating, created_at, updated_at FROM __temp__product');
        $this->addSql('DROP TABLE __temp__product');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TEMPORARY TABLE __temp__product AS SELECT id, code, name, description, image, category, price, quantity, internal_reference, shell_id, inventory_status, rating, created_at, updated_at FROM product');
        $this->addSql('DROP TABLE product');
        $this->addSql('CREATE TABLE product (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, code VARCHAR(100) NOT NULL, name VARCHAR(255) NOT NULL, description CLOB NOT NULL, image VARCHAR(255) NOT NULL, category VARCHAR(100) NOT NULL, price DOUBLE PRECISION NOT NULL, quantity INTEGER NOT NULL, internal_reference VARCHAR(150) NOT NULL, shell_id INTEGER NOT NULL, inventory_status VARCHAR(255) NOT NULL, rating INTEGER NOT NULL, created_at INTEGER NOT NULL, updated_at INTEGER NOT NULL)');
        $this->addSql('INSERT INTO product (id, code, name, description, image, category, price, quantity, internal_reference, shell_id, inventory_status, rating, created_at, updated_at) SELECT id, code, name, description, image, category, price, quantity, internal_reference, shell_id, inventory_status, rating, created_at, updated_at FROM __temp__product');
        $this->addSql('DROP TABLE __temp__product');
    }
}
