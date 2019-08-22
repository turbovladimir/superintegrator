<?php
/**
 * Created by PhpStorm.
 * User: v.sadovnikov
 * Date: 22.08.2019
 * Time: 12:46
 */

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Debug\Exception\FlattenException;
use Monolog\Logger;

class ExceptionController extends AbstractController
{
    const CONTROLLER_EXCEPTION = 'Symfony\Component\HttpKernel\Exception\ControllerDoesNotReturnResponseException';
    
    /**
     * @param FlattenException $exception
     * @param Logger           $logger
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showAction(FlattenException $exception,Logger $logger)
    {
        if ($exception->getClass() === self::CONTROLLER_EXCEPTION) {
            return $this->render("page_not_found.html.twig");
        }
    }
}