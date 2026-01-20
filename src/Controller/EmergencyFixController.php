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

            // 4. Social Feed (BricoGram)
            'CREATE TABLE IF NOT EXISTS post (id SERIAL NOT NULL, author_id INT NOT NULL, content TEXT, media VARCHAR(255), media_type VARCHAR(20) DEFAULT \'text\', created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))',
            'CREATE TABLE IF NOT EXISTS post_like (id SERIAL NOT NULL, post_id INT NOT NULL, user_id INT NOT NULL, PRIMARY KEY(id))',
            'CREATE TABLE IF NOT EXISTS post_comment (id SERIAL NOT NULL, post_id INT NOT NULL, author_id INT NOT NULL, content TEXT NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))',

            // Indices for Social Feed
            'CREATE INDEX IF NOT EXISTS IDX_POST_AUTHOR ON post (author_id)',
            'CREATE INDEX IF NOT EXISTS IDX_LIKE_POST ON post_like (post_id)',
            'CREATE INDEX IF NOT EXISTS IDX_LIKE_USER ON post_like (user_id)',
            'CREATE INDEX IF NOT EXISTS IDX_COMMENT_POST ON post_comment (post_id)',
            'CREATE INDEX IF NOT EXISTS IDX_COMMENT_AUTHOR ON post_comment (author_id)',
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
