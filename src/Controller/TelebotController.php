<?php


namespace App\Controller;

use App\Services\TeleBot\Entity\InputData;
use App\Services\TeleBot\TelebotProcessor;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class TelebotController extends AbstractController
{
    private $processor;
    private $logger;
    private $env;

    public function __construct(TelebotProcessor $processor, LoggerInterface $logger, string $env) {
        $this->processor = $processor;
        $this->logger = $logger;
        $this->env = $env;
    }

    public function process(Request $request) : Response {
        try {
            $inputData = new InputData();
            $status = 200;

            if (!$request->get('debug')) {
                $message = $this->processor->process($inputData);
            } else {
                $this->processor->debug($inputData);
                return new JsonResponse('Got it!');
            }
        } catch (\InvalidArgumentException $exception) {
            $message = '';
            $status = 400;

            if ($this->env === 'dev') {
                $message = sprintf('Error happen: %s(%d) `%s`',
                    $exception->getFile(),
                    $exception->getLine(),
                    $exception->getMessage());
            }
        }

        return new JsonResponse($message, $status);
    }

    public function showDebugLogs() : JsonResponse {
        return new JsonResponse(explode("\n", $this->processor->fetchDebugLogs()));
    }

    public function clearDebugLogs() : JsonResponse {
        $this->processor->clearDebugLogs();

        return new JsonResponse('Done!');
    }

    public function setHook() : JsonResponse {
        $this->processor->setHook();

        return new JsonResponse('Done!');
    }
}