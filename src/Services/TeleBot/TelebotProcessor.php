<?php


namespace App\Services\TeleBot;


use GuzzleHttp\Client;
use Psr\Log\LoggerInterface;

class TelebotProcessor
{
    private $domain;
    private $logger;
    private $client;
    private $telebotLogDir;
    private $telebotToken;

    public function __construct($domain, LoggerInterface $telebotLogger, string $telebotLogDir, string $telebotToken) {
        $this->domain = $domain;
        $this->logger = $telebotLogger;
        $this->telebotLogDir = $telebotLogDir;
        $this->telebotToken = $telebotToken;
        $this->client = new Client(['base_url' => 'api.telegram.org']);
    }

    /**
     *
     */
    public function process() {
        $inputData = json_decode(file_get_contents('php://input'), true);

        if (!$inputData) {
            return;
        }

        $this->logger->debug(print_r($inputData, true));
        $messageData = $inputData['message'];

        if (empty($messageData['text'])) {
            return;
        }

        $chatId = $messageData['chat']['id'];
        $text = strtolower($messageData['text']);

        if ($text === 'hi!') {
            $this->sendMessage($chatId, 'Alohaa its work for you!');
        }
    }

    /**
     * @return false|string
     */
    public function fetchDebugLogs() {
        return file_get_contents("{$this->telebotLogDir}/telebot.log");
    }

    public function clearDebugLogs() {
        file_put_contents("{$this->telebotLogDir}/telebot.log", "");
    }

    public function setHook() {
        $this->makeRequest('setwebhook', 'GET', ['url' => "https://{$this->domain}/telebot"]);
    }

    private function sendMessage(int $chatId, string $text) {
        $this->makeRequest(__FUNCTION__, 'POST', [
            'chat_id' => $chatId,
            'method' => __FUNCTION__,
            'text' => $text]);
    }

    private function makeRequest(string $apiMethod, string $httpMethod, array $params = []) {
        if ($httpMethod === 'GET') {
            $query = http_build_query($params);
            $this->client->get("bot{$this->telebotToken}/{$apiMethod}?{$query}");
        } elseif ($httpMethod === 'POST') {
            $this->client->post("bot{$this->telebotToken}/{$apiMethod}", ['json' => $params]);
        }
    }
}