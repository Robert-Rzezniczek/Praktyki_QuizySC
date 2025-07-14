<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250711150047 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE quiz_result DROP FOREIGN KEY quiz_result_ibfk_1
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE quiz_result DROP FOREIGN KEY quiz_result_ibfk_2
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE quiz_result DROP total_time, CHANGE user_id user_id INT DEFAULT NULL, CHANGE quiz_id quiz_id INT DEFAULT NULL, CHANGE score score DOUBLE PRECISION NOT NULL, CHANGE correct_answers correct_answers INT NOT NULL, CHANGE started_at started_at DATETIME NOT NULL, CHANGE completed_at completed_at DATETIME NOT NULL, CHANGE expires_at expires_at DATETIME NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX user_id ON quiz_result
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_FE2E314AA76ED395 ON quiz_result (user_id)
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX quiz_id ON quiz_result
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_FE2E314A853CD175 ON quiz_result (quiz_id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE quiz_result ADD CONSTRAINT quiz_result_ibfk_1 FOREIGN KEY (user_id) REFERENCES user_profile (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE quiz_result ADD CONSTRAINT quiz_result_ibfk_2 FOREIGN KEY (quiz_id) REFERENCES quizzes (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE quizzes CHANGE time_limit time_limit INT DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user_answer DROP FOREIGN KEY user_answer_ibfk_3
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user_answer DROP FOREIGN KEY user_answer_ibfk_1
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user_answer DROP FOREIGN KEY user_answer_ibfk_4
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user_answer DROP FOREIGN KEY user_answer_ibfk_2
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user_answer CHANGE quiz_result_id quiz_result_id INT DEFAULT NULL, CHANGE is_correct is_correct TINYINT(1) NOT NULL, CHANGE answered_at answered_at DATETIME NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX quiz_result_id ON user_answer
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_BF8F51181C7C7A5 ON user_answer (quiz_result_id)
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX user_id ON user_answer
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_BF8F5118A76ED395 ON user_answer (user_id)
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX question_id ON user_answer
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_BF8F51181E27F6BF ON user_answer (question_id)
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX answer_id ON user_answer
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_BF8F5118AA334807 ON user_answer (answer_id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user_answer ADD CONSTRAINT user_answer_ibfk_3 FOREIGN KEY (question_id) REFERENCES questions (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user_answer ADD CONSTRAINT user_answer_ibfk_1 FOREIGN KEY (quiz_result_id) REFERENCES quiz_result (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user_answer ADD CONSTRAINT user_answer_ibfk_4 FOREIGN KEY (answer_id) REFERENCES answers (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user_answer ADD CONSTRAINT user_answer_ibfk_2 FOREIGN KEY (user_id) REFERENCES user_profile (id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE quizzes CHANGE time_limit time_limit INT NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE quiz_result DROP FOREIGN KEY FK_FE2E314AA76ED395
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE quiz_result DROP FOREIGN KEY FK_FE2E314A853CD175
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE quiz_result ADD total_time INT DEFAULT NULL, CHANGE user_id user_id INT NOT NULL, CHANGE quiz_id quiz_id INT NOT NULL, CHANGE score score DOUBLE PRECISION DEFAULT NULL, CHANGE correct_answers correct_answers INT DEFAULT NULL, CHANGE started_at started_at DATETIME DEFAULT NULL, CHANGE completed_at completed_at DATETIME DEFAULT NULL, CHANGE expires_at expires_at DATETIME DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX idx_fe2e314a853cd175 ON quiz_result
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX quiz_id ON quiz_result (quiz_id)
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX idx_fe2e314aa76ed395 ON quiz_result
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX user_id ON quiz_result (user_id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE quiz_result ADD CONSTRAINT FK_FE2E314AA76ED395 FOREIGN KEY (user_id) REFERENCES user_profile (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE quiz_result ADD CONSTRAINT FK_FE2E314A853CD175 FOREIGN KEY (quiz_id) REFERENCES quizzes (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user_answer DROP FOREIGN KEY FK_BF8F51181C7C7A5
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user_answer DROP FOREIGN KEY FK_BF8F5118A76ED395
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user_answer DROP FOREIGN KEY FK_BF8F51181E27F6BF
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user_answer DROP FOREIGN KEY FK_BF8F5118AA334807
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user_answer CHANGE quiz_result_id quiz_result_id INT NOT NULL, CHANGE is_correct is_correct TINYINT(1) DEFAULT NULL, CHANGE answered_at answered_at DATETIME DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX idx_bf8f5118a76ed395 ON user_answer
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX user_id ON user_answer (user_id)
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX idx_bf8f51181e27f6bf ON user_answer
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX question_id ON user_answer (question_id)
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX idx_bf8f5118aa334807 ON user_answer
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX answer_id ON user_answer (answer_id)
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX idx_bf8f51181c7c7a5 ON user_answer
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX quiz_result_id ON user_answer (quiz_result_id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user_answer ADD CONSTRAINT FK_BF8F51181C7C7A5 FOREIGN KEY (quiz_result_id) REFERENCES quiz_result (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user_answer ADD CONSTRAINT FK_BF8F5118A76ED395 FOREIGN KEY (user_id) REFERENCES user_profile (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user_answer ADD CONSTRAINT FK_BF8F51181E27F6BF FOREIGN KEY (question_id) REFERENCES questions (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user_answer ADD CONSTRAINT FK_BF8F5118AA334807 FOREIGN KEY (answer_id) REFERENCES answers (id)
        SQL);
    }
}
