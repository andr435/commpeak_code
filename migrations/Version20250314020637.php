<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250314020637 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Created CallReport tebale for storing call reports from customers';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE call_report (
            id INT AUTO_INCREMENT NOT NULL, 
            customer_id INT NOT NULL, 
            call_date DATETIME NOT NULL, 
            duration INT NOT NULL, 
            dialed_number VARCHAR(20) NOT NULL, 
            customer_ip VARCHAR(40) NOT NULL, 
            internal_call TINYINT(1) NOT NULL, 
            INDEX idx_customer (customer_id),
            PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE call_report');
    }
}
