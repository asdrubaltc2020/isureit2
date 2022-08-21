<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220821040235 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE agent (id INT AUTO_INCREMENT NOT NULL, first_name VARCHAR(50) NOT NULL, last_name VARCHAR(50) NOT NULL, email VARCHAR(255) DEFAULT NULL, birth_date VARCHAR(25) DEFAULT NULL, phone VARCHAR(25) DEFAULT NULL, mobil VARCHAR(25) DEFAULT NULL, social VARCHAR(255) DEFAULT NULL, street VARCHAR(255) DEFAULT NULL, city VARCHAR(255) DEFAULT NULL, county VARCHAR(255) DEFAULT NULL, zip_code VARCHAR(10) DEFAULT NULL, license VARCHAR(50) DEFAULT NULL, npn VARCHAR(255) DEFAULT NULL, agency_str VARCHAR(255) DEFAULT NULL, phone_ext VARCHAR(10) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE agent');
    }
}
