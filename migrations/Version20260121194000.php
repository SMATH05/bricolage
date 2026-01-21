<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260121194000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add media_data BLOB column to post table for storing files in database';
    }

    public function up(Schema $schema): void
    {
        $platform = $this->connection->getDatabasePlatform()->getName();
        
        if ($platform === 'postgresql') {
            $this->addSql('ALTER TABLE "post" ADD COLUMN media_data BYTEA DEFAULT NULL');
        } else {
            // MySQL
            $this->addSql('ALTER TABLE post ADD COLUMN media_data LONGBLOB DEFAULT NULL');
        }
    }

    public function down(Schema $schema): void
    {
        $platform = $this->connection->getDatabasePlatform()->getName();
        
        if ($platform === 'postgresql') {
            $this->addSql('ALTER TABLE "post" DROP COLUMN media_data');
        } else {
            $this->addSql('ALTER TABLE post DROP COLUMN media_data');
        }
    }
}
