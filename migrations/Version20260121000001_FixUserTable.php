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
        // Add nom and prenom columns if they don't exist
        $table = $schema->getTable('user');
        
        if (!$table->hasColumn('nom')) {
            $table->addColumn('nom', 'string', ['length' => 100, 'notnull' => false]);
        }
        
        if (!$table->hasColumn('prenom')) {
            $table->addColumn('prenom', 'string', ['length' => 100, 'notnull' => false]);
        }
        
        // Update existing records to extract name from email
        $this->addSql('UPDATE user SET nom = CASE 
            WHEN LOCATE(\'@\', email) > 0 THEN 
                SUBSTRING_INDEX(email, 1, LOCATE(\'@\', email)) - 1
            ELSE 
                SUBSTRING_INDEX(email, 1, LOCATE(\'.\', email)) - 1
            END,
            prenom = CASE 
            WHEN LOCATE(\'@\', email) > 0 THEN 
                SUBSTRING_INDEX(SUBSTRING_INDEX(email, 1, LOCATE(\'@\', email)) + 1, LOCATE(\'.\', SUBSTRING_INDEX(email, 1, LOCATE(\'@\', email)))) - 1
            ELSE 
                SUBSTRING_INDEX(SUBSTRING_INDEX(email, 1, LOCATE(\'.\', SUBSTRING_INDEX(email, 1, LOCATE(\'@\', email)))) - 1
            END
            WHERE nom IS NULL OR prenom IS NULL');
        
        $this->addSql('UPDATE user SET email = LOWER(email)');
        $this->addSql('UPDATE user SET nom = COALESCE(NULLIF(TRIM(nom), \'\'), CONCAT(COALESCE(NULLIF(TRIM(prenom), \'\'), \' \'), COALESCE(NULLIF(TRIM(nom), \'\'))))');
        $this->addSql('UPDATE user SET prenom = COALESCE(NULLIF(TRIM(prenom), \'\'), SUBSTRING_INDEX(email, 1, LOCATE(\'@\', email)) - 1, SUBSTRING_INDEX(email, 1, LOCATE(\'.\', SUBSTRING_INDEX(email, 1, LOCATE(\'@\', email)))) - 1)');
        
        // Fix roles JSON format
        $this->addSql("UPDATE user SET roles = CASE 
            WHEN roles = '[]' THEN '[]'
            WHEN roles = 'a:0:{}' THEN '[]'
            WHEN roles LIKE '%:%' THEN roles
            ELSE JSON_ARRAY(roles)
            END WHERE roles IS NULL OR roles = '' OR roles = '[]'");
    }
