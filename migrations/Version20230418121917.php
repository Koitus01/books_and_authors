<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230418121917 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE book ADD cover VARCHAR(255) DEFAULT NULL, CHANGE name title LONGTEXT NOT NULL');
        $this->addSql('CREATE INDEX isbn ON book (isbn)');
        $this->addSql('CREATE INDEX publishing ON book (publishing)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX isbn ON book');
        $this->addSql('DROP INDEX publishing ON book');
        $this->addSql('ALTER TABLE book DROP cover, CHANGE title name LONGTEXT NOT NULL');
    }
}
