<?php


namespace App\Controller;

use App\Services\TeleBot\Processor;
use Longman\TelegramBot\Exception\TelegramException;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class TelebotController extends AbstractController
{
    private $processor;
    private $logger;

    public function __construct(Processor $telebotProcessor, LoggerInterface $logger) {
        $this->processor = $telebotProcessor;
        $this->logger = $logger;
    }

    public function process() : Response {
        try {
            $statusCode = 200;
            $this->processor->handle();
            $message = 'nice!';
        } catch (TelegramException $exception) {
            $statusCode = 400;
            $message = $exception->getMessage();
        }

        return new JsonResponse($message, $statusCode);
    }

    public function setHook(Request $request) : JsonResponse {

        if (!$hookUrl = $request->get('hook_url') || empty($hookUrl)) {
            return new JsonResponse('The hook url not set in request!', 403);
        }

        try {
            $statusCode = 200;
            $response = $this->processor->setWebhook($hookUrl);

            if ($response->isOk()) {
                $responseData = 'Done!';
            } else {
                $statusCode = $response->getErrorCode();
                $responseData = "Response error : {$response->getDescription()}";
            }
        } catch (TelegramException $exception) {
            $responseData = $exception->getMessage();
            $statusCode = 400;
        }

        return new JsonResponse($responseData, $statusCode);
    }
}