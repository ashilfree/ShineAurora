<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210528085506 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE discount_banner (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, title_ar VARCHAR(255) NOT NULL, sub_title VARCHAR(255) NOT NULL, sub_title_ar VARCHAR(255) NOT NULL, sub_title2 VARCHAR(255) NOT NULL, sub_title_ar2 VARCHAR(255) NOT NULL, starting_at DATETIME NOT NULL, btn_title VARCHAR(255) NOT NULL, btn_title_ar VARCHAR(255) NOT NULL, btn_url VARCHAR(255) NOT NULL, file_name VARCHAR(255) NOT NULL, updated_at DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE discount_banner');
    }
}
