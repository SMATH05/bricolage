<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify it to your needs!
 */
final class Version20260121000002_AddResetPasswordFields extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add reset password fields to user table';
    }

    public function up(Schema $schema): void
    {
        $table = $schema->getTable('user');

        if (!$table->hasColumn('reset_token')) {
            $table->addColumn('reset_token', 'string', ['length' => 255, 'notnull' => false]);
        }

        if (!$table->hasColumn('reset_token_expires_at')) {
            $table->addColumn('reset_token_expires_at', 'datetime', ['notnull' => false]);
        }
    }

    public function down(Schema $schema): void
    {
        $table = $schema->getTable('user');

        if ($table->hasColumn('reset_token')) {
            $table->dropColumn('reset_token');
        }

        if ($table->hasColumn('reset_token_expires_at')) {
            $table->dropColumn('reset_token_expires_at');
        }
    }
}
