<?php


namespace App\Tests\Services\Telebot;


use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Response;
use Longman\TelegramBot\DB;
use Longman\TelegramBot\Request;
use PDO;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ProcessorTest extends  KernelTestCase
{
    private const ADMIN_USER_ID = 1;
    /**
     * @var \App\Services\TeleBot\Processor
     */
    private $processor;

    protected function setUp(): void {
        $kernel = self::bootKernel();
        $container = $kernel->getContainer();
        $this->processor = $container->get('app.services.telebot.processor.public');
        $this->processor->enableAdmin(self::ADMIN_USER_ID);
        define('PHPUNIT_TESTSUITE', 1);
    }

    private function configureClient(array $clientHistory) : ClientInterface {
        $history = Middleware::history($clientHistory);
        $mock = new MockHandler([
            new Response(200, ['X-Foo' => 'Bar'], '{"ok":true,"result":{"message_id":1840,"from":{"id":1629302802,"is_bot":true,"first_name":"Barrymore","username":"pass_keeper_777_bot"},"chat":{"id":107465278,"first_name":"Vladimir","username":"turbo_vladimir","type":"private"},"date":1623878123,"text":"No active conversation!"}}'),
            new Response(200, ['X-Foo' => 'Bar'], '{"ok":true,"result":{"message_id":1840,"from":{"id":1629302802,"is_bot":true,"first_name":"Barrymore","username":"pass_keeper_777_bot"},"chat":{"id":107465278,"first_name":"Vladimir","username":"turbo_vladimir","type":"private"},"date":1623878123,"text":"No active conversation!"}}'),
        ]);
        $handlerStack = HandlerStack::create($mock);
        $handlerStack->push($history);

        return new Client(['handler' => $handlerStack]);
    }

    public function testCommand() {
        $clientHistory = [];
        $client = $this->configureClient($clientHistory);
        Request::setClient($client);
        $this->processor->setCustomInput($this->getUpdateWithCommand('/clearchat'));
        $this->processor->handle();

        exit();
    }

    public function testPdoSelect() {
        $pdo = DB::getPdo();

        $messageIds =
            $pdo->query('select id from message')->fetchAll(PDO::FETCH_COLUMN);

        exit();
    }

    private function getUpdateWithCommand(string $command) {
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
}', rand(1, 100000), self::ADMIN_USER_ID, $command);
    }
}