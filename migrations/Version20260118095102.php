<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260118095102 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $platform = $this->connection->getDatabasePlatform()->getName();
        $isPostgreSQL = $platform === 'postgresql';
        
        if ($isPostgreSQL) {
            // PostgreSQL syntax
            $this->addSql('CREATE TABLE admin (id SERIAL NOT NULL, id_admin VARCHAR(255) NOT NULL, nom VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, mot_de_passe VARCHAR(255) NOT NULL, regles TEXT NOT NULL, PRIMARY KEY(id))');
            $this->addSql('CREATE TABLE annonce (id SERIAL NOT NULL, recrut_id_id INT DEFAULT NULL, titre VARCHAR(255) NOT NULL, description VARCHAR(255) NOT NULL, date_publication TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, budget DOUBLE PRECISION NOT NULL, photo VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
            $this->addSql('CREATE INDEX IDX_F65593E5CE8B0FFA ON annonce (recrut_id_id)');
            $this->addSql('CREATE TABLE candidature (id SERIAL NOT NULL, chercheur_id_id INT DEFAULT NULL, annonce_id_id INT DEFAULT NULL, id_candidature VARCHAR(255) NOT NULL, date_pro TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, statut VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
            $this->addSql('CREATE INDEX IDX_E33BD3B826B2368C ON candidature (chercheur_id_id)');
            $this->addSql('CREATE INDEX IDX_E33BD3B868C955C8 ON candidature (annonce_id_id)');
            $this->addSql('CREATE TABLE chercheur (id SERIAL NOT NULL, user_id INT DEFAULT NULL, id_chercheur VARCHAR(255) NOT NULL, nom VARCHAR(255) NOT NULL, prenom VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, mot_de_passe VARCHAR(255) NOT NULL, description TEXT NOT NULL, disponibilite VARCHAR(255) NOT NULL, photo VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
            $this->addSql('CREATE UNIQUE INDEX UNIQ_9DD29B50A76ED395 ON chercheur (user_id)');
            $this->addSql('CREATE TABLE recruteur (id SERIAL NOT NULL, user_id INT DEFAULT NULL, nom VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, adresse VARCHAR(255) DEFAULT NULL, telephone VARCHAR(255) NOT NULL, photo VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
            $this->addSql('CREATE UNIQUE INDEX UNIQ_2BD3678CA76ED395 ON recruteur (user_id)');
            $this->addSql('CREATE TABLE "user" (id SERIAL NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, is_verified BOOLEAN NOT NULL, PRIMARY KEY(id))');
            $this->addSql('CREATE UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL ON "user" (email)');
            $this->addSql('CREATE TABLE messenger_messages (id BIGSERIAL NOT NULL, body TEXT NOT NULL, headers TEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, available_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, delivered_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
            $this->addSql('CREATE INDEX IDX_75EA56E0FB7336F0 ON messenger_messages (queue_name)');
            $this->addSql('CREATE INDEX IDX_75EA56E0E3BD61CE ON messenger_messages (available_at)');
            $this->addSql('CREATE INDEX IDX_75EA56E016BA31DB ON messenger_messages (delivered_at)');
            $this->addSql('ALTER TABLE annonce ADD CONSTRAINT FK_F65593E5CE8B0FFA FOREIGN KEY (recrut_id_id) REFERENCES recruteur (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
            $this->addSql('ALTER TABLE candidature ADD CONSTRAINT FK_E33BD3B826B2368C FOREIGN KEY (chercheur_id_id) REFERENCES chercheur (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
            $this->addSql('ALTER TABLE candidature ADD CONSTRAINT FK_E33BD3B868C955C8 FOREIGN KEY (annonce_id_id) REFERENCES annonce (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
            $this->addSql('ALTER TABLE chercheur ADD CONSTRAINT FK_9DD29B50A76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
            $this->addSql('ALTER TABLE recruteur ADD CONSTRAINT FK_2BD3678CA76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        } else {
            // MySQL syntax
            $this->addSql('CREATE TABLE admin (id INT AUTO_INCREMENT NOT NULL, id_admin VARCHAR(255) NOT NULL, nom VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, mot_de_passe VARCHAR(255) NOT NULL, regles LONGTEXT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
            $this->addSql('CREATE TABLE annonce (id INT AUTO_INCREMENT NOT NULL, recrut_id_id INT DEFAULT NULL, titre VARCHAR(255) NOT NULL, description VARCHAR(255) NOT NULL, date_publication DATETIME NOT NULL, budget DOUBLE PRECISION NOT NULL, photo VARCHAR(255) DEFAULT NULL, INDEX IDX_F65593E5CE8B0FFA (recrut_id_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
            $this->addSql('CREATE TABLE candidature (id INT AUTO_INCREMENT NOT NULL, chercheur_id_id INT DEFAULT NULL, annonce_id_id INT DEFAULT NULL, id_candidature VARCHAR(255) NOT NULL, date_pro DATETIME NOT NULL, statut VARCHAR(255) NOT NULL, INDEX IDX_E33BD3B826B2368C (chercheur_id_id), INDEX IDX_E33BD3B868C955C8 (annonce_id_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
            $this->addSql('CREATE TABLE chercheur (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, id_chercheur VARCHAR(255) NOT NULL, nom VARCHAR(255) NOT NULL, prenom VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, mot_de_passe VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, disponibilite VARCHAR(255) NOT NULL, photo VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_9DD29B50A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
            $this->addSql('CREATE TABLE recruteur (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, nom VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, adresse VARCHAR(255) DEFAULT NULL, telephone VARCHAR(255) NOT NULL, photo VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_2BD3678CA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
            $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL COMMENT \'(DC2Type:json)\', password VARCHAR(255) NOT NULL, is_verified TINYINT(1) NOT NULL, UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
            $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', available_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', delivered_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
            $this->addSql('ALTER TABLE annonce ADD CONSTRAINT FK_F65593E5CE8B0FFA FOREIGN KEY (recrut_id_id) REFERENCES recruteur (id)');
            $this->addSql('ALTER TABLE candidature ADD CONSTRAINT FK_E33BD3B826B2368C FOREIGN KEY (chercheur_id_id) REFERENCES chercheur (id)');
            $this->addSql('ALTER TABLE candidature ADD CONSTRAINT FK_E33BD3B868C955C8 FOREIGN KEY (annonce_id_id) REFERENCES annonce (id)');
            $this->addSql('ALTER TABLE chercheur ADD CONSTRAINT FK_9DD29B50A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
            $this->addSql('ALTER TABLE recruteur ADD CONSTRAINT FK_2BD3678CA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        }
    }

    public function down(Schema $schema): void
    {
        $platform = $this->connection->getDatabasePlatform()->getName();
        $isPostgreSQL = $platform === 'postgresql';
        
        if ($isPostgreSQL) {
            $this->addSql('ALTER TABLE annonce DROP CONSTRAINT FK_F65593E5CE8B0FFA');
            $this->addSql('ALTER TABLE candidature DROP CONSTRAINT FK_E33BD3B826B2368C');
            $this->addSql('ALTER TABLE candidature DROP CONSTRAINT FK_E33BD3B868C955C8');
            $this->addSql('ALTER TABLE chercheur DROP CONSTRAINT FK_9DD29B50A76ED395');
            $this->addSql('ALTER TABLE recruteur DROP CONSTRAINT FK_2BD3678CA76ED395');
        } else {
            $this->addSql('ALTER TABLE annonce DROP FOREIGN KEY FK_F65593E5CE8B0FFA');
            $this->addSql('ALTER TABLE candidature DROP FOREIGN KEY FK_E33BD3B826B2368C');
            $this->addSql('ALTER TABLE candidature DROP FOREIGN KEY FK_E33BD3B868C955C8');
            $this->addSql('ALTER TABLE chercheur DROP FOREIGN KEY FK_9DD29B50A76ED395');
            $this->addSql('ALTER TABLE recruteur DROP FOREIGN KEY FK_2BD3678CA76ED395');
        }
        
        $this->addSql('DROP TABLE admin');
        $this->addSql('DROP TABLE annonce');
        $this->addSql('DROP TABLE candidature');
        $this->addSql('DROP TABLE chercheur');
        $this->addSql('DROP TABLE recruteur');
        if ($isPostgreSQL) {
            $this->addSql('DROP TABLE "user"');
        } else {
            $this->addSql('DROP TABLE user');
        }
        $this->addSql('DROP TABLE messenger_messages');
    }
}
