<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260527065255 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE comments (id VARCHAR(36) NOT NULL, task_id VARCHAR(36) NOT NULL, author_id VARCHAR(36) NOT NULL, content TEXT NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY (id))');
        $this->addSql('CREATE TABLE project_members (user_id VARCHAR(36) NOT NULL, role VARCHAR(20) NOT NULL, joined_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, project_id VARCHAR(36) NOT NULL, PRIMARY KEY (project_id, user_id))');
        $this->addSql('CREATE INDEX IDX_D3BEDE9A166D1F9C ON project_members (project_id)');
        $this->addSql('CREATE TABLE projects (id VARCHAR(36) NOT NULL, name VARCHAR(150) NOT NULL, owner_id VARCHAR(36) NOT NULL, archived BOOLEAN DEFAULT false NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY (id))');
        $this->addSql('CREATE TABLE tasks (id VARCHAR(36) NOT NULL, project_id VARCHAR(36) NOT NULL, title VARCHAR(255) NOT NULL, description TEXT DEFAULT NULL, status VARCHAR(20) NOT NULL, assignee_id VARCHAR(36) DEFAULT NULL, reporter_id VARCHAR(36) DEFAULT NULL, due_date DATE DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY (id))');
        $this->addSql('CREATE TABLE users (id VARCHAR(36) NOT NULL, password_hash VARCHAR(255) NOT NULL, name VARCHAR(100) NOT NULL, role VARCHAR(20) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, email VARCHAR(180) NOT NULL, PRIMARY KEY (id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1483A5E9E7927C74 ON users (email)');
        $this->addSql('ALTER TABLE project_members ADD CONSTRAINT FK_D3BEDE9A166D1F9C FOREIGN KEY (project_id) REFERENCES projects (id) ON DELETE CASCADE NOT DEFERRABLE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE project_members DROP CONSTRAINT FK_D3BEDE9A166D1F9C');
        $this->addSql('DROP TABLE comments');
        $this->addSql('DROP TABLE project_members');
        $this->addSql('DROP TABLE projects');
        $this->addSql('DROP TABLE tasks');
        $this->addSql('DROP TABLE users');
    }
}
