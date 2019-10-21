<?php
/**
 * Created by PhpStorm.
 * User: v.sadovnikov
 * Date: 21.10.2019
 * Time: 14:48
 */

namespace App\Services;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

/**
 * Функционал для выдачи ответа в виде файла
 *
 * Trait DownloadFileTrait
 *
 * @package App\Services
 */
trait DownloadFileTrait
{
    /**
     * @param $fileName
     * @param $fileContent
     *
     * @return Response
     */
    public function giveFile($fileName, $fileContent)
    {
        $response = new Response(
            '',
            Response::HTTP_OK,
            ['content-type' => 'text/html']
        );
    
        $disposition = $response->headers->makeDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            $fileName
        );
        $response->headers->set('Content-Disposition', $disposition);
        $response->setContent($fileContent);
        
        return $response;
    }
}