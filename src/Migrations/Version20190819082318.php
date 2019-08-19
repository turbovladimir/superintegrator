<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190819082318 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        
        $handler = fopen('vendor/ddl.sql', 'rb');
        $sqlStr = fread($handler, filesize('vendor/ddl.sql'));
        fclose($handler);
        $sqlArr = explode(';', $sqlStr);
    
        foreach ($sqlArr as $sql) {
            $this->addSql($sql);
        }
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');
    
        $this->addSql('DROP TABLE cityads_country_russia');
        $this->addSql('DROP TABLE cityads_world_region');
        $this->addSql('DROP TABLE cityads_world_region_codes');
        $this->addSql('DROP TABLE postbacktable');
    }
}
