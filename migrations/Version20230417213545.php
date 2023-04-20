<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230417213545 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('create unique index author_first_name_second_name_third_name_uindex on author (first_name, second_name, third_name)');
        /*
         * will not work, because title is a text
         * $this->addSql('create unique index book_name_isbn_uindex on book (name, isbn)');
         * $this->addSql('create unique index book_name_publishing_uindex on book (name, publishing)');
        */

    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
