<?php
/**
 * Created by PhpStorm.
 * User: v.sadovnikov
 * Date: 22.11.2019
 * Time: 12:23
 */

namespace App\Controller;


use App\Response\AlertMessageCollection;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

abstract class BaseController extends AbstractController
{
    
    /**
     * @param        $message
     * @param string $level
     *
     * @return Response
     */
    protected function getOnlyAlertResponse($message, $level = AlertMessageCollection::ALERT_TYPE_SUCCESS)
    {
        $response = new AlertMessageCollection();
        $response->addAlert($message, null, $level);
        
        return $this->render("base.html.twig", ['response' => $response->getMessages()]);
    }
}