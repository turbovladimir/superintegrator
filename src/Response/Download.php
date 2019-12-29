<?php
/**
 * Created by PhpStorm.
 * User: v.sadovnikov
 * Date: 18.11.2019
 * Time: 16:56
 */

namespace App\Response;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

/**
 * Формирует инстанс класса для выгрузки файла в браузер
 *
 * Class Download
 *
 * @package App\Controller\Response
 */
class Download
{
    /**
     * @var Response
     */
    private $response;
    
    /**
     * Download constructor.
     *
     * @param string $fileName
     * @param  string $fileContent
     */
    public function __construct($fileName, $fileContent)
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
        
        $this->response = $response;
    }
    
    /**
     * @return Response
     */
    public function get()
    {
        return $this->response;
    }
}