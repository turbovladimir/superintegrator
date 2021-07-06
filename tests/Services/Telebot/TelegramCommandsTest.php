<?php


namespace App\Tests\Services\Telebot;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class TelegramCommandsTest extends  KernelTestCase
{
    private const ADMIN_USER_ID = 1;
    /**
     * @var \App\Services\TeleBot\Processor
     */
    private $processor;
    private $em;

    protected function setUp(): void {
        $kernel = self::bootKernel();
        $container = $kernel->getContainer();
        $this->processor = $container->get('app.services.telebot.processor.public');
        $this->em = $container->get('doctrine.orm.entity_manager');
        $this->truncateTables();
        define('PHPUNIT_TESTSUITE', 1);
    }

    private function truncateTables() {
        $connection = $this->em->getConnection();
        $connection->query('SET FOREIGN_KEY_CHECKS=0');

        $tables = $connection->fetchAll("SELECT `table_name` FROM information_schema.tables
                        WHERE table_schema = '{$connection->getDatabase()}';");

        foreach ($tables as $table) {
            $connection->query(sprintf('truncate table %s', $table['table_name']));
        }

        $connection->query('SET FOREIGN_KEY_CHECKS=1');
    }

    public function testCommands() {
        foreach (['some text', '/mykeys', '/cancel', '/mykeys', 'save avito pass123', 'get avito', 'delete avito', '/clearchat'] as $text) {
            $result = $this->processor->handle($this->getUpdate($text));
            $this->assertEquals(true, $result->isOk());
        }
    }

    private function getUpdate(string $text) {
        return sprintf('{
   "update_id":%d,
   "message":{
      "message_id":1855,
      "from":{
         "id":%d,
         "is_bot":false,
         "first_name":"Vladimir",
         "username":"turbo_vladimir",
         "language_code":"en"
      },
      "chat":{
         "id":107465278,
         "first_name":"Vladimir",
         "username":"turbo_vladimir",
         "type":"private"
      },
      "date":1625070466,
      "text":"%s",
      "entities":[
         {
            "offset":0,
            "length":8,
            "type":"bot_command"
         }
      ]
   }
}', rand(1, 100000), self::ADMIN_USER_ID, $text);
    }
}