<?php
/**
 * Created by PhpStorm.
 * User: v.sadovnikov
 * Date: 15.11.2019
 * Time: 19:51
 */

namespace App\Utils;


use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer as SymfonySerializer;

class Serializer
{
    
    /**
     * @return SymfonySerializer
     */
    public static function get()
    {
        $encoders = [new XmlEncoder(), new JsonEncoder()];
        $normalizers = [new ObjectNormalizer()];
    
        return new SymfonySerializer($normalizers, $encoders);
    }
}