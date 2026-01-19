<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260119130000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create Commande table for Payment Integration';
    }

    public function up(Schema $schema): void
    {
        $platform = $this->connection->getDatabasePlatform()->getName();
        $isPostgreSQL = $platform === 'postgresql';

        if ($isPostgreSQL) {
            // PostgreSQL syntax
            $this->addSql('CREATE TABLE commande (id SERIAL NOT NULL, user_id INT NOT NULL, product_id INT DEFAULT NULL, tracking_id VARCHAR(255) NOT NULL, status VARCHAR(50) NOT NULL, amount NUMERIC(10, 2) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, UNIQUE(tracking_id), PRIMARY KEY(id))');
            $this->addSql('CREATE INDEX IDX_6EEAA67DA76ED395 ON commande (user_id)');
            $this->addSql('CREATE INDEX IDX_6EEAA67D4584665A ON commande (product_id)');
            $this->addSql('ALTER TABLE commande ADD CONSTRAINT FK_6EEAA67DA76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
            $this->addSql('ALTER TABLE commande ADD CONSTRAINT FK_6EEAA67D4584665A FOREIGN KEY (product_id) REFERENCES product (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        } else {
            // MySQL syntax
            $this->addSql('CREATE TABLE commande (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, product_id INT DEFAULT NULL, tracking_id VARCHAR(255) NOT NULL, status VARCHAR(50) NOT NULL, amount NUMERIC(10, 2) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', UNIQUE INDEX UNIQ_6EEAA67D5E237E06 (tracking_id), INDEX IDX_6EEAA67DA76ED395 (user_id), INDEX IDX_6EEAA67D4584665A (product_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
            $this->addSql('ALTER TABLE commande ADD CONSTRAINT FK_6EEAA67DA76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)');
            $this->addSql('ALTER TABLE commande ADD CONSTRAINT FK_6EEAA67D4584665A FOREIGN KEY (product_id) REFERENCES product (id)');
        }
    }

    public function down(Schema $schema): void
    {
        $platform = $this->connection->getDatabasePlatform()->getName();
        $isPostgreSQL = $platform === 'postgresql';

        if ($isPostgreSQL) {
            $this->addSql('ALTER TABLE commande DROP CONSTRAINT FK_6EEAA67DA76ED395');
            $this->addSql('ALTER TABLE commande DROP CONSTRAINT FK_6EEAA67D4584665A');
        } else {
            $this->addSql('ALTER TABLE commande DROP FOREIGN KEY FK_6EEAA67DA76ED395');
            $this->addSql('ALTER TABLE commande DROP FOREIGN KEY FK_6EEAA67D4584665A');
        }

        $this->addSql('DROP TABLE commande');
    }
}
