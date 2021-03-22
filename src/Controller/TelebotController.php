<?php


namespace App\Controller;

use App\Services\TeleBot\TelebotProcessor;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class TelebotController extends AbstractController
{
    private $processor;
    private $logger;

    public function __construct(TelebotProcessor $processor, LoggerInterface $logger) {
        $this->processor = $processor;
        $this->logger = $logger;
    }

    public function index() : Response {
        $this->processor->process();

        return $this->redirectToRoute('main_page');
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