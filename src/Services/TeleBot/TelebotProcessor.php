<?php


namespace App\Services\TeleBot;


use GuzzleHttp\Client;
use Psr\Log\LoggerInterface;

class TelebotProcessor
{
    private $domain;
    private $logger;
    private $telebotLogDir;
    private $telebotToken;

    public function __construct($domain, LoggerInterface $telebotLogger, string $telebotLogDir, string $telebotToken) {
        $this->domain = $domain;
        $this->logger = $telebotLogger;
        $this->telebotLogDir = $telebotLogDir;
        $this->telebotToken = $telebotToken;
    }

    /**
     *
     */
    public function process() {
        $inputData = json_decode(file_get_contents('php://input'), true);

        if (!$inputData) {
            return;
        }

        $this->logger->debug('Get message from api: ' . print_r($inputData, true));
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

    /**
     * @param int $chatId
     * @param string $text
     * @throws \Throwable
     */
    private function sendMessage(int $chatId, string $text) {
        $this->makeRequest(__FUNCTION__, 'POST', [
            'chat_id' => $chatId,
            'method' => __FUNCTION__,
            'text' => $text]);
    }

    /**
     * @param string $apiMethod
     * @param string $httpMethod
     * @param array $params
     * @throws \Throwable
     */
    private function makeRequest(string $apiMethod, string $httpMethod, array $params = []) {
        $this->logger->debug('Sent request to api!', ['method' => $apiMethod, 'httpMethod' => $httpMethod, 'params' => $params]);
        $client = new Client();

        try {
            if ($httpMethod === 'GET') {
                $query = http_build_query($params);
                $client->get("https://api.telegram.org/bot{$this->telebotToken}/{$apiMethod}?{$query}");
            } elseif ($httpMethod === 'POST') {
                $client->post("https://api.telegram.org/bot{$this->telebotToken}/{$apiMethod}", ['json' => $params]);
            }
        } catch (\Throwable $exception) {
            $this->logger->debug("Catch exception during send request to api: {$exception->getMessage()}");
            throw $exception;
        }
    }
}