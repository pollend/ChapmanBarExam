<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190626061510 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'Create User View For Quiz Access';
    }

    public function up(Schema $schema) : void
    {
        $this->addSql("CREATE VIEW user_quiz_access  AS (SELECT quiz_access.*,qs.owner_id, count(*) AS user_attempts from quiz_access join quiz_session qs on quiz_access.id = qs.quiz_access_id group by qs.owner_id, quiz_access.id);");

    }

    public function down(Schema $schema) : void
    {
        $this->addSql("DROP VIEW user_quiz_access;");
    }
}
