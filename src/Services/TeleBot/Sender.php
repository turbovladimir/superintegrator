<?php


namespace App\Services\TeleBot;


use GuzzleHttp\Client;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;

class Sender
{
    /**
     * @var Client
     */
    private $client;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var string
     */
    private $telebotToken;

    public function __construct(string $telegramApiUrl, LoggerInterface $telebotLogger, string $telebotToken) {
        $this->client = new Client(['base_uri' => $telegramApiUrl]);
        $this->logger = $telebotLogger;
        $this->telebotToken = $telebotToken;
    }


    /**
     * @param int $chatId
     * @param string $text
     * @throws \Throwable
     * @return ResponseInterface
     */
    public function sendMessage(int $chatId, string $text, array $replyMarkup = []) : ResponseInterface {
        $message = [
            'chat_id' => $chatId,
            'method' => __FUNCTION__,
            'parse_mode' => 'HTML',
            'text' => $text];

        if (!empty($replyMarkup)) {
            $message['reply_markup'] = $replyMarkup;
        }

        return $this->makeRequest(__FUNCTION__, $message);
    }

    /**
     * @param int $chatId
     * @param int $messageId
     * @throws \Throwable
     */
    public function deleteMessage(int $chatId, int $messageId) {
        $this->makeRequest('deleteMessage', ['chat_id' => $chatId, 'message_id' => $messageId]);
    }

    /**
     * @param string $apiMethod
     * @param array $params
     * @throws \Throwable
     * @return ResponseInterface
     */
    private function makeRequest(string $apiMethod, array $params = []) : ResponseInterface {
        $this->logger->debug('Sent request to api!', ['method' => $apiMethod, 'params' => $params]);

        try {
            return $this->client->post("/bot{$this->telebotToken}/{$apiMethod}", ['json' => $params]);
        } catch (\Throwable $exception) {
            $this->logger->error("Catch exception during send request to api: {$exception->getMessage()}");
            throw $exception;
        }
    }
}