<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250505190312 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE team (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, slug VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_C4E0A61F989D9B62 (slug), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE user2team (team_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_DFC3592A296CD8AE (team_id), INDEX IDX_DFC3592AA76ED395 (user_id), PRIMARY KEY(team_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE user2manage_team (team_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_102B9666296CD8AE (team_id), INDEX IDX_102B9666A76ED395 (user_id), PRIMARY KEY(team_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, given_name VARCHAR(255) NOT NULL, family_name VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user2team ADD CONSTRAINT FK_DFC3592A296CD8AE FOREIGN KEY (team_id) REFERENCES team (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user2team ADD CONSTRAINT FK_DFC3592AA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user2manage_team ADD CONSTRAINT FK_102B9666296CD8AE FOREIGN KEY (team_id) REFERENCES team (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user2manage_team ADD CONSTRAINT FK_102B9666A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE user2team DROP FOREIGN KEY FK_DFC3592A296CD8AE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user2team DROP FOREIGN KEY FK_DFC3592AA76ED395
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user2manage_team DROP FOREIGN KEY FK_102B9666296CD8AE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user2manage_team DROP FOREIGN KEY FK_102B9666A76ED395
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE team
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE user2team
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE user2manage_team
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE user
        SQL);
    }
}
