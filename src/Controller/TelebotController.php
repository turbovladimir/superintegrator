<?php


namespace App\Controller;

use App\Services\TeleBot\Exception\ChatError;
use App\Services\TeleBot\Exception\ChatWarning;
use App\Services\TeleBot\Processor;
use Longman\TelegramBot\Exception\TelegramException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class TelebotController extends AbstractController
{
    private $processor;
    /**
     * @var EventDispatcherInterface
     */
    private $dispatcher;

    public function __construct(Processor $telebotProcessor, EventDispatcherInterface $dispatcher) {
        $this->processor = $telebotProcessor;
        $this->dispatcher = $dispatcher;
    }

    public function process(Request $request) : Response {
        $updateData = $request->getContent();

        if (empty($updateData) || !($updateData = json_decode($updateData, true))) {
            return new JsonResponse(['error' => 'Empty body data!'], 400);
        }

        try {
            $this->processor->handle($updateData);
        } catch (ChatError|ChatWarning $error) {
            return new JsonResponse(['error' => $error->getMessage()], 400);
        } catch (\Throwable $error) {
            return new JsonResponse(['error' => $error->getMessage()], 500);
        }

        return new JsonResponse(['message' => 'nice!'], 200);
    }

    public function setHook(Request $request) : JsonResponse {

        if (!($hookUrl = $request->get('hook_url')) || empty($hookUrl)) {
            return new JsonResponse('The hook url not set in request!', 403);
        }

        try {
            $statusCode = 200;
            $response = $this->processor->setWebhook($hookUrl);

            if ($response->isOk()) {
                $responseData = sprintf('Done! Webhook on url %s was set!', $hookUrl);
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