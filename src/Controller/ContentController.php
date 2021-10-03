<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\JsonResponse;

class ContentController extends AbstractController
{

    public function getSticker(string $sticker, string $projectDir) {
        $file = $projectDir . "/public/images/telebot/sticker/{$sticker}";

        if (!is_readable($file)) {
            return new JsonResponse(sprintf('The file `%s` not found', $file));
        }

        return new BinaryFileResponse($file);
    }
}