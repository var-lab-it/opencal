<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250507120011 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE event (id INT AUTO_INCREMENT NOT NULL, start_date_time DATETIME NOT NULL, end_date_time DATETIME NOT NULL, participant_name VARCHAR(255) NOT NULL, participant_email VARCHAR(255) NOT NULL, participant_message TINYTEXT DEFAULT NULL, type_id INT NOT NULL, host_id INT NOT NULL, INDEX IDX_3BAE0AA7C54C8C93 (type_id), INDEX IDX_3BAE0AA71FB8D185 (host_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE event_type (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, duration INT NOT NULL, slug VARCHAR(255) NOT NULL, host_id INT NOT NULL, INDEX IDX_93151B821FB8D185 (host_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, given_name VARCHAR(255) NOT NULL, family_name VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE event ADD CONSTRAINT FK_3BAE0AA7C54C8C93 FOREIGN KEY (type_id) REFERENCES event_type (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE event ADD CONSTRAINT FK_3BAE0AA71FB8D185 FOREIGN KEY (host_id) REFERENCES user (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE event_type ADD CONSTRAINT FK_93151B821FB8D185 FOREIGN KEY (host_id) REFERENCES user (id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE event DROP FOREIGN KEY FK_3BAE0AA7C54C8C93
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE event DROP FOREIGN KEY FK_3BAE0AA71FB8D185
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE event_type DROP FOREIGN KEY FK_93151B821FB8D185
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE event
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE event_type
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE user
        SQL);
    }
}
