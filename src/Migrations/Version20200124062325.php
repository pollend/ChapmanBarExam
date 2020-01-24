<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200124062325 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'remove duplicate from quiz_response and enforce constraint';
    }

    public function up(Schema $schema) : void
    {
        $this->addSql("DELETE FROM quiz_response as qq WHERE qq.id NOT IN (SELECT q.id FROM (SELECT DISTINCT ON (session_id, question_id) * FROM quiz_response) as q)");
        $this->addSql('ALTER TABLE quiz_response ADD CONSTRAINT quiz_response_session_id_question_id_key UNIQUE (session_id,question_id);');
    }

    public function down(Schema $schema) : void
    {
        $this->addSql('drop index quiz_response_session_id_question_id_key;');
    }
}
