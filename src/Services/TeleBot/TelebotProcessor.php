<?php


namespace App\Services\TeleBot;


use App\Services\TeleBot\Entity\InputData;
use GuzzleHttp\Client;
use Psr\Log\LoggerInterface;

class TelebotProcessor
{
    private $client;
    private $domain;
    private $logger;
    private $telebotLogDir;
    private $telebotToken;

    public function __construct($domain, LoggerInterface $telebotLogger, Client $client, string $telebotLogDir, string $telebotToken) {
        $this->client = $client;
        $this->domain = $domain;
        $this->logger = $telebotLogger;
        $this->telebotLogDir = $telebotLogDir;
        $this->telebotToken = $telebotToken;
    }

    public function process() {
        $inputData = new InputData();

        if ($message = $inputData->getMessage()) {

            $keyboard = [
                ['7', '8', '9'],
                ['4', '5', '6'],
                ['1', '2', '3'],
                ['0']
            ];

            $replyMarkup = [
                'keyboard' => $keyboard,
                'resize_keyboard' => true,
                'one_time_keyboard' => true
            ];

            $this->sendMessage($inputData->getChatId(), "You say {$inputData->getMessage()}", $replyMarkup);
        }
    }

    public function debug() {
        $inputData = new InputData();
        $this->sendMessage($inputData->getChatId(), $this->preFormat((string)$inputData));
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
    private function sendMessage(int $chatId, string $text, array $replyMarkup = []) {
        $message = [
            'chat_id' => $chatId,
            'method' => __FUNCTION__,
            'parse_mode' => 'HTML',
            'text' => $text];

        if (!empty($replyMarkup)) {
            $message['reply_markup'] = $replyMarkup;
        }

        $this->makeRequest(__FUNCTION__, 'POST', $message);
    }

    private function preFormat($message) : string {
        return nl2br('<pre>'.$message.'</pre>', false);
    }

    /**
     * @param string $apiMethod
     * @param string $httpMethod
     * @param array $params
     * @throws \Throwable
     */
    private function makeRequest(string $apiMethod, string $httpMethod, array $params = []) {
        $this->logger->debug('Sent request to api!', ['method' => $apiMethod, 'httpMethod' => $httpMethod, 'params' => $params]);

        try {
            if ($httpMethod === 'GET') {
                $query = http_build_query($params);
                $response = $this->client->get("https://api.telegram.org/bot{$this->telebotToken}/{$apiMethod}?{$query}");
            } else {
                $response = $this->client->post("https://api.telegram.org/bot{$this->telebotToken}/{$apiMethod}", ['json' => $params]);
            }

            $this->logger->debug('Getting response info!', ['headers' => $response->getHeaders(), 'body' => $response->getBody()]);
        } catch (\Throwable $exception) {
            $this->logger->error("Catch exception during send request to api: {$exception->getMessage()}");
            throw $exception;
        }
    }
}