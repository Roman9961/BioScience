<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200109104605 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE customer CHANGE shipping_info_name shipping_info_name VARCHAR(255) DEFAULT NULL, CHANGE shipping_info_apt shipping_info_apt VARCHAR(255) DEFAULT NULL, CHANGE shipping_info_street shipping_info_street VARCHAR(255) DEFAULT NULL, CHANGE shipping_info_city shipping_info_city VARCHAR(255) DEFAULT NULL, CHANGE shipping_info_state shipping_info_state VARCHAR(255) DEFAULT NULL, CHANGE shipping_info_country shipping_info_country VARCHAR(255) DEFAULT NULL, CHANGE shipping_info_zip_code shipping_info_zip_code VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE customer CHANGE shipping_info_name shipping_info_name VARCHAR(255) NOT NULL COLLATE utf8mb4_unicode_ci, CHANGE shipping_info_apt shipping_info_apt VARCHAR(255) NOT NULL COLLATE utf8mb4_unicode_ci, CHANGE shipping_info_street shipping_info_street VARCHAR(255) NOT NULL COLLATE utf8mb4_unicode_ci, CHANGE shipping_info_city shipping_info_city VARCHAR(255) NOT NULL COLLATE utf8mb4_unicode_ci, CHANGE shipping_info_state shipping_info_state VARCHAR(255) NOT NULL COLLATE utf8mb4_unicode_ci, CHANGE shipping_info_country shipping_info_country VARCHAR(255) NOT NULL COLLATE utf8mb4_unicode_ci, CHANGE shipping_info_zip_code shipping_info_zip_code VARCHAR(255) NOT NULL COLLATE utf8mb4_unicode_ci');
    }
}
