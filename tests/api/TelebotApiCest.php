<?php
namespace App\Tests;

use App\Services\TeleBot\Entity\TelegramUpdate;
use App\Services\TeleBot\Http\Driver;
use App\Tests\Tools\DBPurger;
use Codeception\Stub;

class TelebotApiCest
{
    public function _before(ApiTester $I)
    {
        /** @var DBPurger $purger */
        $purger = $I->grabService('app.tests.tools.db_purger');
        $purger->purge();
        $I->haveHttpHeader('Content-Type', 'application/json');

        $mockDriver = Stub::make(Driver::class, ['makeRequest' => function () { return new TelegramUpdate(json_encode($this->update)); }]);
        $container = $I->grabService('service_container');
        $container->set('app.telebot.http.driver', $mockDriver);
    }

    public function errorIncorrectInputData(ApiTester $I)
    {
        $I->sendPost('/telebot/process');
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::BAD_REQUEST);
        $I->seeResponseContainsJson(['error' => 'Invalid or empty data: `null`']);
    }

    public function errorUserDenied(ApiTester $I)
    {
        $I->haveHttpHeader('Content-Type', 'application/json');
        $update = $this->update;
        $update['message']['from']['id'] = 333;
        $I->sendPost('/telebot/process', $update);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::BAD_REQUEST);
        $I->seeResponseContainsJson(['error' => 'I\'m sorry, who are you?']);
    }

    public function errorCommandNotFound(ApiTester $I)
    {
        $I->haveHttpHeader('Content-Type', 'application/json');
        $I->sendPost('/telebot/process', $this->update);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::BAD_REQUEST);
        $I->seeResponseContainsJson(['error' => 'Command not found or not initialize!']);
    }

    public function successCommand(ApiTester $I)
    {
        $update = $this->update;
        $update ['message']['command'] = '/my_command';
        $I->sendPost('/telebot/process', $update);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
    }

    private $update = [
        'update_id' => 1,
        'message' => [
            'message_id' => 1,
            'from' => [
                'id' => 1,
                'first_name' => 'User',
                'username' => 'user_name'
            ],
            'chat' => [
                'id' => 1000,
                'first_name' => 'User',
                'username' => 'user_name'
            ],
            'date' => '1625070466',
            'command' => '/command',
            'text' => ''
        ]
    ];
}
