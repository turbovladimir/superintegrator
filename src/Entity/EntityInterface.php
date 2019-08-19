<?php
/**
 * Created by PhpStorm.
 * User: v.sadovnikov
 * Date: 19.08.2019
 * Time: 18:31
 */

namespace App\Entity;


interface EntityInterface extends \ArrayAccess
{
    /**
     * @param  bool $datetime_to_string
     *
     * @return array
     */
    public function toArray($datetime_to_string = true);
}