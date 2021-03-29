<?php


namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class LogsController extends AbstractController
{
    public function index(Request $request, string $telebotLogDir) {
        $logs = file_get_contents($telebotLogDir .'/telebot.log');

        return $this->render('tools/logs/index.html.twig', ['log_content' => $logs ?? '']);
    }

    public function clear(Request $request, string $telebotLogDir) {
        if (file_put_contents($telebotLogDir .'/telebot.log', '') !== false) {
            $content = 'All telebot logs are cleared!';
        } else {
            $content = 'Failure!';
        }


        return $this->render('tools/logs/index.html.twig', ['log_content' => $content]);
    }
}