<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190819082952 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        $files = ['vendor\sk8kilay_test_cityads_country_russia.sql', 'vendor/sk8kilay_test_cityads_world_region.sql', 'vendor/sk8kilay_test_cityads_world_region_codes.sql'];
        
        foreach ($files as $filename) {
            $handler = fopen($filename, 'rb');
            $sqlStr = fread($handler, filesize($filename));
            fclose($handler);
            $sqlArr = explode(';', $sqlStr);
    
            foreach ($sqlArr as $sql) {
                $this->addSql($sql);
            }
        }

    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('TRUNCATE TABLE cityads_world_region;');
        $this->addSql('TRUNCATE TABLE cityads_world_region_codes;');
        $this->addSql('TRUNCATE TABLE cityads_country_russia;');
        $this->addSql('TRUNCATE TABLE postbacktable;');
    }
}
