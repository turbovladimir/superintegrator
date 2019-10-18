<?php
/**
 * Created by PhpStorm.
 * User: v.sadovnikov
 * Date: 18.10.2019
 * Time: 11:08
 */

namespace App\Entity;


interface EntityInterface
{
    public function getId();
    
    public function getAll();
    
    public function getOne($filter);
    
    public function save();
    
    public function executeQuery($sql);
}