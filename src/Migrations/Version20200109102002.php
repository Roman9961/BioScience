<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200109102002 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE customer ADD shipping_info_name VARCHAR(255) NOT NULL, ADD shipping_info_apt VARCHAR(255) NOT NULL, ADD shipping_info_street VARCHAR(255) NOT NULL, ADD shipping_info_city VARCHAR(255) NOT NULL, ADD shipping_info_state VARCHAR(255) NOT NULL, ADD shipping_info_country VARCHAR(255) NOT NULL, ADD shipping_info_zip_code VARCHAR(255) NOT NULL, DROP shipping_info');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE customer ADD shipping_info LONGTEXT NOT NULL COLLATE utf8mb4_unicode_ci, DROP shipping_info_name, DROP shipping_info_apt, DROP shipping_info_street, DROP shipping_info_city, DROP shipping_info_state, DROP shipping_info_country, DROP shipping_info_zip_code');
    }
}
