<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250511171101 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE availability (id INT AUTO_INCREMENT NOT NULL, day_of_week VARCHAR(255) NOT NULL, start_time TIME NOT NULL, end_time TIME NOT NULL, user_id INT NOT NULL, INDEX IDX_3FB7A2BFA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE event (id INT AUTO_INCREMENT NOT NULL, start_time TIME NOT NULL, end_time TIME NOT NULL, day DATE NOT NULL, participant_name VARCHAR(255) NOT NULL, participant_email VARCHAR(255) NOT NULL, participant_message TINYTEXT DEFAULT NULL, event_type_id INT NOT NULL, INDEX IDX_3BAE0AA7401B253C (event_type_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE event_type (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, duration INT NOT NULL, slug VARCHAR(255) NOT NULL, host_id INT NOT NULL, INDEX IDX_93151B821FB8D185 (host_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE unavailability (id INT AUTO_INCREMENT NOT NULL, day_of_week VARCHAR(255) NOT NULL, start_time TIME DEFAULT NULL, end_time TIME DEFAULT NULL, full_day TINYINT(1) DEFAULT NULL, user_id INT NOT NULL, INDEX IDX_F0016D1A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, given_name VARCHAR(255) NOT NULL, family_name VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE availability ADD CONSTRAINT FK_3FB7A2BFA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE event ADD CONSTRAINT FK_3BAE0AA7401B253C FOREIGN KEY (event_type_id) REFERENCES event_type (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE event_type ADD CONSTRAINT FK_93151B821FB8D185 FOREIGN KEY (host_id) REFERENCES user (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE unavailability ADD CONSTRAINT FK_F0016D1A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE availability DROP FOREIGN KEY FK_3FB7A2BFA76ED395
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE event DROP FOREIGN KEY FK_3BAE0AA7401B253C
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE event_type DROP FOREIGN KEY FK_93151B821FB8D185
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE unavailability DROP FOREIGN KEY FK_F0016D1A76ED395
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE availability
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE event
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE event_type
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE unavailability
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE user
        SQL);
    }
}
