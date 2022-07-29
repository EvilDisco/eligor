<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220729194906 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE file_link ADD parser_id INT NOT NULL');
        $this->addSql('ALTER TABLE file_link ADD CONSTRAINT FK_BF50F19FF54E453B FOREIGN KEY (parser_id) REFERENCES parser (id)');
        $this->addSql('CREATE INDEX IDX_BF50F19FF54E453B ON file_link (parser_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE file_link DROP FOREIGN KEY FK_BF50F19FF54E453B');
        $this->addSql('DROP INDEX IDX_BF50F19FF54E453B ON file_link');
        $this->addSql('ALTER TABLE file_link DROP parser_id');
    }
}
