<?php


namespace App\Controller;

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

    public function __construct(TelebotProcessor $processor, LoggerInterface $logger) {
        $this->processor = $processor;
        $this->logger = $logger;
    }

    public function process(Request $request) : Response {
        if (!$request->get('debug')) {
            $this->processor->process();
        } else {
            $this->processor->debug();
        }

        return new JsonResponse('Got it!');
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