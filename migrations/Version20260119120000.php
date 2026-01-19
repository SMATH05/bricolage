<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260119120000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create Product table for Marketplace (Compatible with MySQL and PostgreSQL)';
    }

    public function up(Schema $schema): void
    {
        $platform = $this->connection->getDatabasePlatform()->getName();
        $isPostgreSQL = $platform === 'postgresql';

        if ($isPostgreSQL) {
            // PostgreSQL syntax
            $this->addSql('CREATE TABLE product (id SERIAL NOT NULL, user_id INT NOT NULL, title VARCHAR(255) NOT NULL, description TEXT NOT NULL, price NUMERIC(10, 2) NOT NULL, image VARCHAR(255) DEFAULT NULL, category VARCHAR(50) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
            $this->addSql('CREATE INDEX IDX_D34A04ADA76ED395 ON product (user_id)');
            $this->addSql('ALTER TABLE product ADD CONSTRAINT FK_D34A04ADA76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        } else {
            // MySQL syntax
            $this->addSql('CREATE TABLE product (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, title VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, price NUMERIC(10, 2) NOT NULL, image VARCHAR(255) DEFAULT NULL, category VARCHAR(50) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_D34A04ADA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
            $this->addSql('ALTER TABLE product ADD CONSTRAINT FK_D34A04ADA76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)');
        }
    }

    public function down(Schema $schema): void
    {
        $platform = $this->connection->getDatabasePlatform()->getName();
        $isPostgreSQL = $platform === 'postgresql';

        if ($isPostgreSQL) {
            $this->addSql('ALTER TABLE product DROP CONSTRAINT FK_D34A04ADA76ED395');
        } else {
            $this->addSql('ALTER TABLE product DROP FOREIGN KEY FK_D34A04ADA76ED395');
        }

        $this->addSql('DROP TABLE product');
    }
}
