<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260120172000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create Social Feed tables (post, post_like, post_comment) for PostgreSQL';
    }

    public function up(Schema $schema): void
    {
        $platform = $this->connection->getDatabasePlatform()->getName();
        $isPostgreSQL = $platform === 'postgresql';

        if ($isPostgreSQL) {
            $this->addSql('CREATE TABLE "post" (id SERIAL PRIMARY KEY, author_id INT NOT NULL, content TEXT, media VARCHAR(255), media_type VARCHAR(20) DEFAULT \'text\', created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL)');
            $this->addSql('CREATE TABLE "post_like" (id SERIAL PRIMARY KEY, post_id INT NOT NULL, user_id INT NOT NULL)');
            $this->addSql('CREATE TABLE "post_comment" (id SERIAL PRIMARY KEY, post_id INT NOT NULL, author_id INT NOT NULL, content TEXT NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL)');

            $this->addSql('ALTER TABLE "post" ADD CONSTRAINT FK_POST_AUTHOR FOREIGN KEY (author_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
            $this->addSql('ALTER TABLE "post_like" ADD CONSTRAINT FK_LIKE_POST FOREIGN KEY (post_id) REFERENCES "post" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
            $this->addSql('ALTER TABLE "post_like" ADD CONSTRAINT FK_LIKE_USER FOREIGN KEY (user_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
            $this->addSql('ALTER TABLE "post_comment" ADD CONSTRAINT FK_COMMENT_POST FOREIGN KEY (post_id) REFERENCES "post" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
            $this->addSql('ALTER TABLE "post_comment" ADD CONSTRAINT FK_COMMENT_AUTHOR FOREIGN KEY (author_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');

            $this->addSql('CREATE INDEX IDX_POST_AUTHOR_PG ON "post" (author_id)');
            $this->addSql('CREATE INDEX IDX_LIKE_POST_PG ON "post_like" (post_id)');
            $this->addSql('CREATE INDEX IDX_LIKE_USER_PG ON "post_like" (user_id)');
            $this->addSql('CREATE INDEX IDX_COMMENT_POST_PG ON "post_comment" (post_id)');
            $this->addSql('CREATE INDEX IDX_COMMENT_AUTHOR_PG ON "post_comment" (author_id)');
        } else {
            // MySQL fallback if needed, but the primary target is PG
            $this->addSql('CREATE TABLE post (id INT AUTO_INCREMENT NOT NULL, author_id INT NOT NULL, content LONGTEXT DEFAULT NULL, media VARCHAR(255) DEFAULT NULL, media_type VARCHAR(20) DEFAULT \'text\', created_at DATETIME NOT NULL, INDEX IDX_5A8A6C8DF624B39D (author_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
            $this->addSql('CREATE TABLE post_like (id INT AUTO_INCREMENT NOT NULL, post_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_653627B84B89032C (post_id), INDEX IDX_653627B8A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
            $this->addSql('CREATE TABLE post_comment (id INT AUTO_INCREMENT NOT NULL, post_id INT NOT NULL, author_id INT NOT NULL, content LONGTEXT NOT NULL, created_at DATETIME NOT NULL, INDEX IDX_8FA6F6D14B89032C (post_id), INDEX IDX_8FA6F6D1F624B39D (author_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
            $this->addSql('ALTER TABLE post ADD CONSTRAINT FK_5A8A6C8DF624B39D FOREIGN KEY (author_id) REFERENCES `user` (id)');
            $this->addSql('ALTER TABLE post_like ADD CONSTRAINT FK_653627B84B89032C FOREIGN KEY (post_id) REFERENCES post (id)');
            $this->addSql('ALTER TABLE post_like ADD CONSTRAINT FK_653627B8A76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)');
            $this->addSql('ALTER TABLE post_comment ADD CONSTRAINT FK_8FA6F6D14B89032C FOREIGN KEY (post_id) REFERENCES post (id)');
            $this->addSql('ALTER TABLE post_comment ADD CONSTRAINT FK_8FA6F6D1F624B39D FOREIGN KEY (author_id) REFERENCES `user` (id)');
        }
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE IF EXISTS "post_comment"');
        $this->addSql('DROP TABLE IF EXISTS "post_like"');
        $this->addSql('DROP TABLE IF EXISTS "post"');
    }
}
