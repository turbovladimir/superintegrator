<?php
/**
 * Created by PhpStorm.
 * User: v.sadovnikov
 * Date: 14.08.2019
 * Time: 17:20
 */

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class BaseController extends AbstractController
{
    /**
     *
     * @return Response
     * @throws \Exception
     */
    public function main()
    {
    
        return $this->render('base.html.twig');
    }
    
    /**
     *
     * @return Response
     * @throws \Exception
     */
    public function geo()
    {
        
        return $this->render('geo/geo.html.twig');
    }
    
    /**
     *
     * @return Response
     * @throws \Exception
     */
    public function dataTransformer()
    {
        
        return $this->render('data_transformer/data_transformer.html.twig');
    }
    
    /**
     *
     * @return Response
     * @throws \Exception
     */
    public function aliOrders()
    {
        
        return $this->render('ali_orders/ali_orders.html.twig');
    }
    
    /**
     *
     * @return Response
     * @throws \Exception
     */
    public function sender()
    {
        
        return $this->render('sender/sender.html.twig');
    }
}