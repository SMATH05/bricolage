<?php

namespace App\Controller;

use Doctrine\DBAL\Connection;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class EmergencyFixController extends AbstractController
{
    #[Route('/system/fix-database', name: 'app_emergency_fix')]
    public function fix(Connection $connection): Response
    {
        $messages = [];

        $sqls = [
            // 1. Add theme column to user table (Postgres syntax)
            'ALTER TABLE "user" ADD COLUMN IF NOT EXISTS theme VARCHAR(10) DEFAULT \'light\' NOT NULL',

            // 2. Followers table
            'CREATE TABLE IF NOT EXISTS user_followers (user_source INT NOT NULL, user_target INT NOT NULL, PRIMARY KEY(user_source, user_target))',
            'CREATE INDEX IF NOT EXISTS IDX_84E870413AD8644E ON user_followers (user_source)',
            'CREATE INDEX IF NOT EXISTS IDX_84E87041233D34C1 ON user_followers (user_target)',

            // 3. Skills system
            'CREATE TABLE IF NOT EXISTS skill (id SERIAL NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id))',
            'CREATE TABLE IF NOT EXISTS chercheur_skill (chercheur_id INT NOT NULL, skill_id INT NOT NULL, PRIMARY KEY(chercheur_id, skill_id))',
            'CREATE INDEX IF NOT EXISTS IDX_7F75D508930F3294 ON chercheur_skill (chercheur_id)',
            'CREATE INDEX IF NOT EXISTS IDX_7F75D5085585C142 ON chercheur_skill (skill_id)',
        ];

        foreach ($sqls as $sql) {
            try {
                $connection->executeStatement($sql);
                $messages[] = "✅ Success: " . substr($sql, 0, 50) . "...";
            } catch (\Exception $e) {
                // If column already exists or other non-critical errors, we continue
                $messages[] = "⚠️ Skipping: " . $e->getMessage();
            }
        }

        return new Response("<h1>Database Self-Heal Result</h1><ul><li>" . implode("</li><li>", $messages) . "</li></ul><p><strong>Site should be fixed now! Go to <a href='/login'>Login</a></strong></p>");
    }
}
