<?php


namespace App\Tests\Services\Telebot;


use App\Services\TeleBot\Entity\InputData;
use App\Services\TeleBot\SecurityProvider;
use App\Services\TeleBot\Sender;
use App\Services\TeleBot\TelebotProcessor;
use Monolog\Logger;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class TelebotProcessorTest extends  KernelTestCase
{
//    /**
//     * @dataProvider dataProvider
//     * @param InputData $inputData
//     */
//    public function testProcess(InputData $inputData) {
//        self::bootKernel();
//        $container = self::$container;
//        $sender = $this->createMock(Sender::class);
//        $sender->method('makeRequest')
//        $security = $this->createMock(SecurityProvider::class);
//        $security->method('checkUser')->willReturn(true);
//        $processor = new TelebotProcessor($this->createMock(Logger::class), $security, $sender);
//        $processor->process($inputData);
//        $params = $sender->getLastRequestParams();
//        exit();
//    }

    public function dataProvider() {
        return [
            [new InputData('{
                   "update_id":698094706,
                   "message":{
                      "message_id":1690,
                      "from":{
                         "id":107465278,
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
                      "date":1617607293,
                      "text":"\/clear_history",
                      "entities":[
                         {
                            "offset":0,
                            "length":14,
                            "type":"bot_command"
                         }
                      ]
                   }
                }')]
        ];
    }
}