<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250717185203 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE answers (id INT AUTO_INCREMENT NOT NULL, question_id INT NOT NULL, content LONGTEXT NOT NULL, is_correct TINYINT(1) NOT NULL, position INT NOT NULL, INDEX IDX_50D0C6061E27F6BF (question_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE faq (id INT AUTO_INCREMENT NOT NULL, question VARCHAR(255) NOT NULL, answer LONGTEXT NOT NULL, position INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE powiaty (id INT AUTO_INCREMENT NOT NULL, wojewodztwo_id INT NOT NULL, name VARCHAR(255) NOT NULL, INDEX IDX_AEE5E2F13E8EA8F5 (wojewodztwo_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE questions (id INT AUTO_INCREMENT NOT NULL, quiz_id INT NOT NULL, content LONGTEXT NOT NULL, points INT NOT NULL, position INT NOT NULL, INDEX IDX_8ADC54D5853CD175 (quiz_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE quiz_result (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, quiz_id INT DEFAULT NULL, score DOUBLE PRECISION NOT NULL, correct_answers INT NOT NULL, started_at DATETIME NOT NULL, completed_at DATETIME NOT NULL, expires_at DATETIME NOT NULL, INDEX IDX_FE2E314AA76ED395 (user_id), INDEX IDX_FE2E314A853CD175 (quiz_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE quizzes (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, time_limit INT DEFAULT NULL, is_published TINYINT(1) NOT NULL, created_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', updated_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', brand_name VARCHAR(255) DEFAULT NULL, branddescription LONGTEXT DEFAULT NULL, logo_url VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE user_answer (id INT AUTO_INCREMENT NOT NULL, quiz_result_id INT DEFAULT NULL, user_id INT NOT NULL, question_id INT NOT NULL, answer_id INT NOT NULL, is_correct TINYINT(1) NOT NULL, answered_at DATETIME NOT NULL, INDEX IDX_BF8F51181C7C7A5 (quiz_result_id), INDEX IDX_BF8F5118A76ED395 (user_id), INDEX IDX_BF8F51181E27F6BF (question_id), INDEX IDX_BF8F5118AA334807 (answer_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE user_auth (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL COMMENT '(DC2Type:json)', password VARCHAR(255) NOT NULL, is_two_factor_enabled TINYINT(1) DEFAULT NULL, auth_code VARCHAR(6) DEFAULT NULL, UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE user_profile (id INT AUTO_INCREMENT NOT NULL, wojewodztwo_id INT NOT NULL, powiat_id INT NOT NULL, user_auth_id INT NOT NULL, imie VARCHAR(100) NOT NULL, nazwisko VARCHAR(100) NOT NULL, szkola VARCHAR(255) NOT NULL, podzial_wiekowy VARCHAR(255) NOT NULL, INDEX IDX_D95AB4053E8EA8F5 (wojewodztwo_id), INDEX IDX_D95AB405C05AA04F (powiat_id), UNIQUE INDEX UNIQ_D95AB405D88F9F96 (user_auth_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE wojewodztwa (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', available_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', delivered_at DATETIME DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)', INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE answers ADD CONSTRAINT FK_50D0C6061E27F6BF FOREIGN KEY (question_id) REFERENCES questions (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE powiaty ADD CONSTRAINT FK_AEE5E2F13E8EA8F5 FOREIGN KEY (wojewodztwo_id) REFERENCES wojewodztwa (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE questions ADD CONSTRAINT FK_8ADC54D5853CD175 FOREIGN KEY (quiz_id) REFERENCES quizzes (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE quiz_result ADD CONSTRAINT FK_FE2E314AA76ED395 FOREIGN KEY (user_id) REFERENCES user_profile (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE quiz_result ADD CONSTRAINT FK_FE2E314A853CD175 FOREIGN KEY (quiz_id) REFERENCES quizzes (id)
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
        $this->addSql(<<<'SQL'
            ALTER TABLE user_profile ADD CONSTRAINT FK_D95AB4053E8EA8F5 FOREIGN KEY (wojewodztwo_id) REFERENCES wojewodztwa (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user_profile ADD CONSTRAINT FK_D95AB405C05AA04F FOREIGN KEY (powiat_id) REFERENCES powiaty (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user_profile ADD CONSTRAINT FK_D95AB405D88F9F96 FOREIGN KEY (user_auth_id) REFERENCES user_auth (id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE answers DROP FOREIGN KEY FK_50D0C6061E27F6BF
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE powiaty DROP FOREIGN KEY FK_AEE5E2F13E8EA8F5
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE questions DROP FOREIGN KEY FK_8ADC54D5853CD175
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE quiz_result DROP FOREIGN KEY FK_FE2E314AA76ED395
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE quiz_result DROP FOREIGN KEY FK_FE2E314A853CD175
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
            ALTER TABLE user_profile DROP FOREIGN KEY FK_D95AB4053E8EA8F5
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user_profile DROP FOREIGN KEY FK_D95AB405C05AA04F
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user_profile DROP FOREIGN KEY FK_D95AB405D88F9F96
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE answers
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE faq
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE powiaty
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE questions
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE quiz_result
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE quizzes
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE user_answer
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE user_auth
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE user_profile
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE wojewodztwa
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE messenger_messages
        SQL);
    }
}
