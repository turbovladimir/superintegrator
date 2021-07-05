<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210702133851 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        $this->addSql('
        CREATE TABLE telebot_key 
        (id INT AUTO_INCREMENT NOT NULL, 
        added_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', 
        name VARCHAR(255) NOT NULL, 
        value LONGTEXT NOT NULL, 
        PRIMARY KEY(id)) 
        DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('create unique index ix_name_value on telebot_key(name, value);');
    }

    public function down(Schema $schema) : void
    {
        $this->addSql('DROP TABLE telebot_key');
    }
}
