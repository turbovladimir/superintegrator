<?php


namespace App\Tests\Services\Telebot;


use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Response;
use Longman\TelegramBot\Request;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ProcessorTest extends  KernelTestCase
{
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

    /**
     * @dataProvider dataProvider
     */
    public function testCommandCleanup(array $input, string $expected) {
        $kernel = self::bootKernel();
        $container = $kernel->getContainer();
        $clientHistory = [];
        $client = $this->configureClient($clientHistory);
        Request::setClient($client);
        $processor = $container->get('app.services.telebot.processor.public');
        $processor->setCustomInput(json_encode($input));
        $processor->enableAdmin(1);
        $processor->handle();

        exit();
    }

    public function dataProvider() {
        return [
            [[
                'update_id' => 698094793,
                'message' =>
                    [
                        'message_id' => 1842,
                        'from' =>
                            [
                                'id' => 1,
                                'is_bot' => false,
                                'first_name' => 'Vladimir',
                                'username' => 'turbo_vladimir',
                                'language_code' => 'en',
                           ],
                        'chat' =>
                            [
                                'id' => 1,
                                'first_name' => 'Vladimir',
                                'username' => 'turbo_vladimir',
                                'type' => 'private',
                           ],
                        'date' => 1623879074,
                        'text' => '/cleanup',
                        'entities' =>
                            [
                                0 =>
                                    [
                                        'offset' => 0,
                                        'length' => 8,
                                        'type' => 'bot_command',
                                   ],
                           ],
                   ],
           ],
                '']
        ];
    }
}