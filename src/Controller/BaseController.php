<?php
/**
 * Created by PhpStorm.
 * User: v.sadovnikov
 * Date: 24.12.2019
 * Time: 17:49
 */

namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class BaseController extends AbstractController
{
    
    /**
     * @param array $options
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function mainPage($options = [])
    {
        return $this->render('base.html.twig', $options);
    }
}