<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220728205305 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE file_link (id INT AUTO_INCREMENT NOT NULL, link VARCHAR(500) NOT NULL, title VARCHAR(500) NOT NULL, status VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE livejournal_author (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, blog VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE livejournal_post (id INT AUTO_INCREMENT NOT NULL, author_id INT DEFAULT NULL, title VARCHAR(255) NOT NULL, text LONGTEXT NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_DE824442F675F31B (author_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE parser (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE parser_stats (id INT AUTO_INCREMENT NOT NULL, parser_id INT NOT NULL, duration INT DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_7A16E19EF54E453B (parser_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE livejournal_post ADD CONSTRAINT FK_DE824442F675F31B FOREIGN KEY (author_id) REFERENCES livejournal_author (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE parser_stats ADD CONSTRAINT FK_7A16E19EF54E453B FOREIGN KEY (parser_id) REFERENCES parser (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE livejournal_post DROP FOREIGN KEY FK_DE824442F675F31B');
        $this->addSql('ALTER TABLE parser_stats DROP FOREIGN KEY FK_7A16E19EF54E453B');
        $this->addSql('DROP TABLE file_link');
        $this->addSql('DROP TABLE livejournal_author');
        $this->addSql('DROP TABLE livejournal_post');
        $this->addSql('DROP TABLE parser');
        $this->addSql('DROP TABLE parser_stats');
    }
}
