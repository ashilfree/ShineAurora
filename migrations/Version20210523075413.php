<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210523075413 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE product DROP FOREIGN KEY FK_D34A04AD52F30384');
        $this->addSql('ALTER TABLE product DROP FOREIGN KEY FK_D34A04ADF7BFE87C');
        $this->addSql('CREATE TABLE size_category (size_id INT NOT NULL, category_id INT NOT NULL, INDEX IDX_58778D1D498DA827 (size_id), INDEX IDX_58778D1D12469DE2 (category_id), PRIMARY KEY(size_id, category_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE size_category ADD CONSTRAINT FK_58778D1D498DA827 FOREIGN KEY (size_id) REFERENCES size (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE size_category ADD CONSTRAINT FK_58778D1D12469DE2 FOREIGN KEY (category_id) REFERENCES category (id) ON DELETE CASCADE');
        $this->addSql('DROP TABLE hot_degree');
        $this->addSql('DROP TABLE sub_category');
        $this->addSql('ALTER TABLE about ADD description LONGTEXT DEFAULT NULL, ADD description_ar LONGTEXT DEFAULT NULL, ADD btn_title VARCHAR(255) NOT NULL, ADD btn_title_ar VARCHAR(255) NOT NULL, DROP description1, DROP description_ar1, DROP description2, DROP description_ar2, DROP description3, DROP description_ar3, DROP description4, DROP description_ar4');
        $this->addSql('ALTER TABLE catalog ADD size_id INT DEFAULT NULL, ADD quantity INT NOT NULL, DROP property_name, DROP property_value, DROP property_name_ar, DROP property_value_ar');
        $this->addSql('ALTER TABLE catalog ADD CONSTRAINT FK_1B2C3247498DA827 FOREIGN KEY (size_id) REFERENCES size (id)');
        $this->addSql('CREATE INDEX IDX_1B2C3247498DA827 ON catalog (size_id)');
        $this->addSql('ALTER TABLE category DROP slug, DROP file_name, DROP updated_at, DROP file_name2, DROP updated_at2');
        $this->addSql('ALTER TABLE customer ADD username VARCHAR(255) NOT NULL, ADD address VARCHAR(255) DEFAULT NULL, ADD region VARCHAR(255) DEFAULT NULL, ADD governorate VARCHAR(255) DEFAULT NULL, CHANGE phone phone VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE `order` ADD shipping_full_name VARCHAR(255) DEFAULT NULL, ADD shipping_address VARCHAR(255) DEFAULT NULL, ADD ordered_at DATETIME DEFAULT NULL, ADD is_paid TINYINT(1) NOT NULL, DROP shipping_first_name, DROP shipping_last_name, DROP shipping_street_number, DROP shipping_house_number, DROP shipping_apartment, DROP shipping_country, DROP shipping_postal_code, DROP shipping_lat, DROP shipping_lng, DROP notes, CHANGE payment_method payment_method VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE order_details ADD size VARCHAR(255) NOT NULL');
        $this->addSql('DROP INDEX IDX_D34A04AD52F30384 ON product');
        $this->addSql('DROP INDEX IDX_D34A04ADF7BFE87C ON product');
        $this->addSql('ALTER TABLE product ADD weight DOUBLE PRECISION DEFAULT NULL, ADD materials VARCHAR(255) DEFAULT NULL, ADD materials_ar VARCHAR(255) DEFAULT NULL, DROP sub_category_id, DROP hot_degree_id, DROP quantity');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE hot_degree (id INT AUTO_INCREMENT NOT NULL, label VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE sub_category (id INT AUTO_INCREMENT NOT NULL, category_id INT NOT NULL, name VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, name_ar VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, INDEX IDX_BCE3F79812469DE2 (category_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE sub_category ADD CONSTRAINT FK_BCE3F79812469DE2 FOREIGN KEY (category_id) REFERENCES category (id)');
        $this->addSql('DROP TABLE size_category');
        $this->addSql('ALTER TABLE about ADD description1 LONGTEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, ADD description_ar1 LONGTEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, ADD description2 LONGTEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, ADD description_ar2 LONGTEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, ADD description3 LONGTEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, ADD description_ar3 LONGTEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, ADD description4 LONGTEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, ADD description_ar4 LONGTEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, DROP description, DROP description_ar, DROP btn_title, DROP btn_title_ar');
        $this->addSql('ALTER TABLE catalog DROP FOREIGN KEY FK_1B2C3247498DA827');
        $this->addSql('DROP INDEX IDX_1B2C3247498DA827 ON catalog');
        $this->addSql('ALTER TABLE catalog ADD property_name VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, ADD property_value VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, ADD property_name_ar VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, ADD property_value_ar VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, DROP size_id, DROP quantity');
        $this->addSql('ALTER TABLE category ADD slug VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, ADD file_name VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, ADD updated_at DATETIME NOT NULL, ADD file_name2 VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, ADD updated_at2 DATETIME NOT NULL');
        $this->addSql('ALTER TABLE customer DROP username, DROP address, DROP region, DROP governorate, CHANGE phone phone VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE `order` ADD shipping_first_name VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, ADD shipping_last_name VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, ADD shipping_street_number VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, ADD shipping_house_number VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, ADD shipping_apartment VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, ADD shipping_country VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, ADD shipping_postal_code VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, ADD shipping_lat DOUBLE PRECISION DEFAULT NULL, ADD shipping_lng DOUBLE PRECISION DEFAULT NULL, ADD notes LONGTEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, DROP shipping_full_name, DROP shipping_address, DROP ordered_at, DROP is_paid, CHANGE payment_method payment_method VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE order_details DROP size');
        $this->addSql('ALTER TABLE product ADD sub_category_id INT DEFAULT NULL, ADD hot_degree_id INT DEFAULT NULL, ADD quantity INT NOT NULL, DROP weight, DROP materials, DROP materials_ar');
        $this->addSql('ALTER TABLE product ADD CONSTRAINT FK_D34A04AD52F30384 FOREIGN KEY (hot_degree_id) REFERENCES hot_degree (id)');
        $this->addSql('ALTER TABLE product ADD CONSTRAINT FK_D34A04ADF7BFE87C FOREIGN KEY (sub_category_id) REFERENCES sub_category (id)');
        $this->addSql('CREATE INDEX IDX_D34A04AD52F30384 ON product (hot_degree_id)');
        $this->addSql('CREATE INDEX IDX_D34A04ADF7BFE87C ON product (sub_category_id)');
    }
}
