<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190917162842 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE fonbet_statistic_by_publishers (id INT AUTO_INCREMENT NOT NULL, wm_id INT NOT NULL, click_id LONGTEXT NOT NULL, registrations INT NOT NULL, deposits_amount LONGTEXT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE archive_rows CHANGE rows rows LONGTEXT NOT NULL, CHANGE sended sended INT DEFAULT NULL');
        $this->addSql('ALTER TABLE test_xml DROP creation_date');
        $this->addSql('ALTER TABLE csv DROP PRIMARY KEY');
        $this->addSql('ALTER TABLE csv ADD id INT AUTO_INCREMENT NOT NULL, CHANGE file_name file_name VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE csv ADD PRIMARY KEY (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE fonbet_statistic_by_publishers');
        $this->addSql('ALTER TABLE archive_rows CHANGE rows rows TEXT DEFAULT NULL COLLATE utf8_general_ci, CHANGE sended sended INT DEFAULT 0');
        $this->addSql('ALTER TABLE csv MODIFY id INT NOT NULL');
        $this->addSql('ALTER TABLE csv DROP PRIMARY KEY');
        $this->addSql('ALTER TABLE csv DROP id, CHANGE file_name file_name VARCHAR(40) NOT NULL COLLATE utf8_general_ci');
        $this->addSql('ALTER TABLE csv ADD PRIMARY KEY (file_name)');
        $this->addSql('ALTER TABLE test_xml ADD creation_date DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL');
    }
}
