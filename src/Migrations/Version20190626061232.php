<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190626061232 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SEQUENCE refresh_tokens_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE multiple_choice (id BIGSERIAL NOT NULL, multiple_choice_question_id BIGINT DEFAULT NULL, "order" SMALLINT NOT NULL, content TEXT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_EE52A32BEB3EBF2 ON multiple_choice (multiple_choice_question_id)');
        $this->addSql('CREATE TABLE quiz (id BIGSERIAL NOT NULL, name VARCHAR(50) NOT NULL, description TEXT NOT NULL, max_score INT DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, deleted_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE quiz_question (id BIGSERIAL NOT NULL, quiz_id BIGINT DEFAULT NULL, "order" INT NOT NULL, "group" INT NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, type VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_6033B00B853CD175 ON quiz_question (quiz_id)');
        $this->addSql('CREATE TABLE quiz_question_question_tag (quiz_question_id BIGINT NOT NULL, question_tag_id BIGINT NOT NULL, PRIMARY KEY(quiz_question_id, question_tag_id))');
        $this->addSql('CREATE INDEX IDX_9A8EFB783101E51F ON quiz_question_question_tag (quiz_question_id)');
        $this->addSql('CREATE INDEX IDX_9A8EFB78BD8F4C19 ON quiz_question_question_tag (question_tag_id)');
        $this->addSql('CREATE TABLE text_block_question (id BIGINT NOT NULL, content TEXT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE quiz_access (id BIGSERIAL NOT NULL, quiz_id BIGINT DEFAULT NULL, classroom_id BIGINT DEFAULT NULL, is_hidden BOOLEAN DEFAULT NULL, num_attempts INT DEFAULT NULL, open_date TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, close_date TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_EB2DDB0D853CD175 ON quiz_access (quiz_id)');
        $this->addSql('CREATE INDEX IDX_EB2DDB0D6278D5A8 ON quiz_access (classroom_id)');
        $this->addSql('CREATE TABLE short_answer_question (id BIGINT NOT NULL, content TEXT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE quiz_response (id BIGSERIAL NOT NULL, session_id BIGINT DEFAULT NULL, question_id BIGINT DEFAULT NULL, type VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_E8BFF2BE613FECDF ON quiz_response (session_id)');
        $this->addSql('CREATE INDEX IDX_E8BFF2BE1E27F6BF ON quiz_response (question_id)');
        $this->addSql('CREATE TABLE short_answer_response (id BIGINT NOT NULL, content TEXT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE multiple_choice_response (id BIGINT NOT NULL, choice_entry_id BIGINT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_ACD930E62B6BC311 ON multiple_choice_response (choice_entry_id)');
        $this->addSql('CREATE TABLE "user" (id BIGSERIAL NOT NULL, roles JSON NOT NULL, username VARCHAR(100) NOT NULL, last_login TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, email VARCHAR(100) NOT NULL, email_verified_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, password VARCHAR(255) NOT NULL, azure_id VARCHAR(50) NOT NULL, remember_token VARCHAR(100) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE quiz_session (id BIGSERIAL NOT NULL, quiz_access_id BIGINT DEFAULT NULL, quiz_id BIGINT DEFAULT NULL, classroom_id BIGINT DEFAULT NULL, owner_id BIGINT DEFAULT NULL, score INT DEFAULT NULL, meta JSON DEFAULT NULL, submitted_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, current_page INT DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_C21E7874498A1629 ON quiz_session (quiz_access_id)');
        $this->addSql('CREATE INDEX IDX_C21E7874853CD175 ON quiz_session (quiz_id)');
        $this->addSql('CREATE INDEX IDX_C21E78746278D5A8 ON quiz_session (classroom_id)');
        $this->addSql('CREATE INDEX IDX_C21E78747E3C61F9 ON quiz_session (owner_id)');
        $this->addSql('CREATE TABLE question_tag (id BIGSERIAL NOT NULL, name VARCHAR(50) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE multiple_choice_question (id BIGINT NOT NULL, correct_entry_id BIGINT DEFAULT NULL, content TEXT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_24557253CFB79C36 ON multiple_choice_question (correct_entry_id)');
        $this->addSql('CREATE TABLE "user_whitelist" (id BIGSERIAL NOT NULL, classroom_id BIGINT DEFAULT NULL, email VARCHAR(50) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_CBE615806278D5A8 ON "user_whitelist" (classroom_id)');
        $this->addSql('CREATE TABLE classroom (id BIGSERIAL NOT NULL, name VARCHAR(50) NOT NULL, description VARCHAR(50) DEFAULT NULL, course_number VARCHAR(50) DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, deleted_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE classroom_user (classroom_id BIGINT NOT NULL, user_id BIGINT NOT NULL, PRIMARY KEY(classroom_id, user_id))');
        $this->addSql('CREATE INDEX IDX_7499B21D6278D5A8 ON classroom_user (classroom_id)');
        $this->addSql('CREATE INDEX IDX_7499B21DA76ED395 ON classroom_user (user_id)');
        $this->addSql('CREATE TABLE refresh_tokens (id INT NOT NULL, refresh_token VARCHAR(128) NOT NULL, username VARCHAR(255) NOT NULL, valid TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_9BACE7E1C74F2195 ON refresh_tokens (refresh_token)');
        $this->addSql('ALTER TABLE multiple_choice ADD CONSTRAINT FK_EE52A32BEB3EBF2 FOREIGN KEY (multiple_choice_question_id) REFERENCES multiple_choice_question (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE quiz_question ADD CONSTRAINT FK_6033B00B853CD175 FOREIGN KEY (quiz_id) REFERENCES quiz (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE quiz_question_question_tag ADD CONSTRAINT FK_9A8EFB783101E51F FOREIGN KEY (quiz_question_id) REFERENCES quiz_question (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE quiz_question_question_tag ADD CONSTRAINT FK_9A8EFB78BD8F4C19 FOREIGN KEY (question_tag_id) REFERENCES question_tag (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE text_block_question ADD CONSTRAINT FK_264E59B6BF396750 FOREIGN KEY (id) REFERENCES quiz_question (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE quiz_access ADD CONSTRAINT FK_EB2DDB0D853CD175 FOREIGN KEY (quiz_id) REFERENCES quiz (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE quiz_access ADD CONSTRAINT FK_EB2DDB0D6278D5A8 FOREIGN KEY (classroom_id) REFERENCES classroom (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE short_answer_question ADD CONSTRAINT FK_53C6E515BF396750 FOREIGN KEY (id) REFERENCES quiz_question (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE quiz_response ADD CONSTRAINT FK_E8BFF2BE613FECDF FOREIGN KEY (session_id) REFERENCES quiz_session (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE quiz_response ADD CONSTRAINT FK_E8BFF2BE1E27F6BF FOREIGN KEY (question_id) REFERENCES quiz_question (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE short_answer_response ADD CONSTRAINT FK_DB4AA7A0BF396750 FOREIGN KEY (id) REFERENCES quiz_response (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE multiple_choice_response ADD CONSTRAINT FK_ACD930E62B6BC311 FOREIGN KEY (choice_entry_id) REFERENCES multiple_choice (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE multiple_choice_response ADD CONSTRAINT FK_ACD930E6BF396750 FOREIGN KEY (id) REFERENCES quiz_response (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE quiz_session ADD CONSTRAINT FK_C21E7874498A1629 FOREIGN KEY (quiz_access_id) REFERENCES quiz_access (id) ON DELETE SET NULL NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE quiz_session ADD CONSTRAINT FK_C21E7874853CD175 FOREIGN KEY (quiz_id) REFERENCES quiz (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE quiz_session ADD CONSTRAINT FK_C21E78746278D5A8 FOREIGN KEY (classroom_id) REFERENCES classroom (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE quiz_session ADD CONSTRAINT FK_C21E78747E3C61F9 FOREIGN KEY (owner_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE multiple_choice_question ADD CONSTRAINT FK_24557253CFB79C36 FOREIGN KEY (correct_entry_id) REFERENCES multiple_choice (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE multiple_choice_question ADD CONSTRAINT FK_24557253BF396750 FOREIGN KEY (id) REFERENCES quiz_question (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE "user_whitelist" ADD CONSTRAINT FK_CBE615806278D5A8 FOREIGN KEY (classroom_id) REFERENCES classroom (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE classroom_user ADD CONSTRAINT FK_7499B21D6278D5A8 FOREIGN KEY (classroom_id) REFERENCES classroom (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE classroom_user ADD CONSTRAINT FK_7499B21DA76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE multiple_choice_response DROP CONSTRAINT FK_ACD930E62B6BC311');
        $this->addSql('ALTER TABLE multiple_choice_question DROP CONSTRAINT FK_24557253CFB79C36');
        $this->addSql('ALTER TABLE quiz_question DROP CONSTRAINT FK_6033B00B853CD175');
        $this->addSql('ALTER TABLE quiz_access DROP CONSTRAINT FK_EB2DDB0D853CD175');
        $this->addSql('ALTER TABLE quiz_session DROP CONSTRAINT FK_C21E7874853CD175');
        $this->addSql('ALTER TABLE quiz_question_question_tag DROP CONSTRAINT FK_9A8EFB783101E51F');
        $this->addSql('ALTER TABLE text_block_question DROP CONSTRAINT FK_264E59B6BF396750');
        $this->addSql('ALTER TABLE short_answer_question DROP CONSTRAINT FK_53C6E515BF396750');
        $this->addSql('ALTER TABLE quiz_response DROP CONSTRAINT FK_E8BFF2BE1E27F6BF');
        $this->addSql('ALTER TABLE multiple_choice_question DROP CONSTRAINT FK_24557253BF396750');
        $this->addSql('ALTER TABLE quiz_session DROP CONSTRAINT FK_C21E7874498A1629');
        $this->addSql('ALTER TABLE short_answer_response DROP CONSTRAINT FK_DB4AA7A0BF396750');
        $this->addSql('ALTER TABLE multiple_choice_response DROP CONSTRAINT FK_ACD930E6BF396750');
        $this->addSql('ALTER TABLE quiz_session DROP CONSTRAINT FK_C21E78747E3C61F9');
        $this->addSql('ALTER TABLE classroom_user DROP CONSTRAINT FK_7499B21DA76ED395');
        $this->addSql('ALTER TABLE quiz_response DROP CONSTRAINT FK_E8BFF2BE613FECDF');
        $this->addSql('ALTER TABLE quiz_question_question_tag DROP CONSTRAINT FK_9A8EFB78BD8F4C19');
        $this->addSql('ALTER TABLE multiple_choice DROP CONSTRAINT FK_EE52A32BEB3EBF2');
        $this->addSql('ALTER TABLE quiz_access DROP CONSTRAINT FK_EB2DDB0D6278D5A8');
        $this->addSql('ALTER TABLE quiz_session DROP CONSTRAINT FK_C21E78746278D5A8');
        $this->addSql('ALTER TABLE "user_whitelist" DROP CONSTRAINT FK_CBE615806278D5A8');
        $this->addSql('ALTER TABLE classroom_user DROP CONSTRAINT FK_7499B21D6278D5A8');
        $this->addSql('DROP SEQUENCE refresh_tokens_id_seq CASCADE');
        $this->addSql('DROP TABLE multiple_choice');
        $this->addSql('DROP TABLE quiz');
        $this->addSql('DROP TABLE quiz_question');
        $this->addSql('DROP TABLE quiz_question_question_tag');
        $this->addSql('DROP TABLE text_block_question');
        $this->addSql('DROP TABLE quiz_access');
        $this->addSql('DROP TABLE short_answer_question');
        $this->addSql('DROP TABLE quiz_response');
        $this->addSql('DROP TABLE short_answer_response');
        $this->addSql('DROP TABLE multiple_choice_response');
        $this->addSql('DROP TABLE "user"');
        $this->addSql('DROP TABLE quiz_session');
        $this->addSql('DROP TABLE question_tag');
        $this->addSql('DROP TABLE multiple_choice_question');
        $this->addSql('DROP TABLE "user_whitelist"');
        $this->addSql('DROP TABLE classroom');
        $this->addSql('DROP TABLE classroom_user');
        $this->addSql('DROP TABLE refresh_tokens');
    }
}
