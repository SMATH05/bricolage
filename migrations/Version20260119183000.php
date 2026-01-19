<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260119183000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create Message table for Real-Time Chat System';
    }

    public function up(Schema $schema): void
    {
        $platform = $this->connection->getDatabasePlatform()->getName();
        $isPostgreSQL = $platform === 'postgresql';

        if ($isPostgreSQL) {
            // PostgreSQL syntax
            $this->addSql('CREATE TABLE message (id SERIAL NOT NULL, sender_id INT NOT NULL, recipient_id INT NOT NULL, content TEXT NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, is_read BOOLEAN NOT NULL, PRIMARY KEY(id))');
            $this->addSql('CREATE INDEX IDX_B6BD307FF624B39D ON message (sender_id)');
            $this->addSql('CREATE INDEX IDX_B6BD307FE92F8F78 ON message (recipient_id)');
            $this->addSql('COMMENT ON COLUMN message.created_at IS \'(DC2Type:datetime_immutable)\'');
            $this->addSql('ALTER TABLE message ADD CONSTRAINT FK_B6BD307FF624B39D FOREIGN KEY (sender_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
            $this->addSql('ALTER TABLE message ADD CONSTRAINT FK_B6BD307FE92F8F78 FOREIGN KEY (recipient_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        } else {
            // MySQL syntax
            $this->addSql('CREATE TABLE message (id INT AUTO_INCREMENT NOT NULL, sender_id INT NOT NULL, recipient_id INT NOT NULL, content LONGTEXT NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', is_read TINYINT(1) NOT NULL, INDEX IDX_B6BD307FF624B39D (sender_id), INDEX IDX_B6BD307FE92F8F78 (recipient_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
            $this->addSql('ALTER TABLE message ADD CONSTRAINT FK_B6BD307FF624B39D FOREIGN KEY (sender_id) REFERENCES `user` (id)');
            $this->addSql('ALTER TABLE message ADD CONSTRAINT FK_B6BD307FE92F8F78 FOREIGN KEY (recipient_id) REFERENCES `user` (id)');
        }
    }

    public function down(Schema $schema): void
    {
        $platform = $this->connection->getDatabasePlatform()->getName();
        $isPostgreSQL = $platform === 'postgresql';

        if ($isPostgreSQL) {
            $this->addSql('ALTER TABLE message DROP CONSTRAINT FK_B6BD307FF624B39D');
            $this->addSql('ALTER TABLE message DROP CONSTRAINT FK_B6BD307FE92F8F78');
        } else {
            $this->addSql('ALTER TABLE message DROP FOREIGN KEY FK_B6BD307FF624B39D');
            $this->addSql('ALTER TABLE message DROP FOREIGN KEY FK_B6BD307FE92F8F78');
        }

        $this->addSql('DROP TABLE message');
    }
}
