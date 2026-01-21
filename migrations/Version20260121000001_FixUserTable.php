<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify it to your needs!
 */
final class Version20260121000001_FixUserTable extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Fix user table with proper column names and data';
    }

    public function up(Schema $schema): void
    {
        $table = $schema->getTable('user');

        if (!$table->hasColumn('nom')) {
            $table->addColumn('nom', 'string', ['length' => 100, 'notnull' => false]);
        }

        if (!$table->hasColumn('prenom')) {
            $table->addColumn('prenom', 'string', ['length' => 100, 'notnull' => false]);
        }
    }

    public function down(Schema $schema): void
    {
        $table = $schema->getTable('user');

        if ($table->hasColumn('nom')) {
            $table->dropColumn('nom');
        }

        if ($table->hasColumn('prenom')) {
            $table->dropColumn('prenom');
        }
    }

}
