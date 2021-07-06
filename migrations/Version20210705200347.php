<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210705200347 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        $this->addSql("
                CREATE TABLE conversation 
                (id INT AUTO_INCREMENT NOT NULL, 
                last_modify DATETIME NOT NULL, 
                last_update_id INT NOT NULL, 
                user_id INT NOT NULL, 
                chat_id INT NOT NULL, 
                command VARCHAR(255) default NULL, 
                status enum ('opened', 'closed') NOT NULL, 
                notes text NOT NULL, 
                PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB");
    }

    public function down(Schema $schema) : void
    {
        $this->addSql('DROP TABLE conversation');
    }
}
