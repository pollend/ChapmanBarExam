<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190615024017 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'Create User View For Quiz Access';
    }

    public function up(Schema $schema) : void
    {
       $this->addSql("CREATE VIEW user_quiz_access  AS (SELECT quiz_access.*,qs.owner_id, count(*) AS user_attempts from quiz_access join quiz_session qs on quiz_access.id = qs.quiz_access_id group by qs.owner_id, quiz_access.id);");
       $this->addSql("CREATE VIEW quiz_session_breakdown AS (SELECT  MAX(quiz_session.id) as id,quiz.id as quiz_id, owner_id,classroom_id, AVG(score) as average_score, MAX(score) as max_score, quiz.max_score as target_score, COUNT(*) as attempts FROM quiz_session INNER JOIN quiz ON quiz.id = quiz_session.quiz_id group by (owner_id,classroom_id,quiz.id));");

    }

    public function down(Schema $schema) : void
    {
        $this->addSql("DROP VIEW user_quiz_access;");
        $this->addSql("DROP VIEW quiz_session_report;");
    }
}
