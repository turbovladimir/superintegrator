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
        CREATE TABLE `telebot_key` (
                               `id` int(11) NOT NULL AUTO_INCREMENT,
                               `added_at` datetime NOT NULL COMMENT \'(DC2Type:datetime_immutable)\',
                               `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
                               `user_id` int(11) NOT NULL,
                               `value` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
                               PRIMARY KEY (`id`),
                               UNIQUE KEY `ix_name_user` (`name`(15), `user_id`)
)');
    }

    public function down(Schema $schema) : void
    {
        $this->addSql('DROP TABLE telebot_key');
    }
}
