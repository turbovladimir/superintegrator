<?php
/**
 * Created by PhpStorm.
 * User: v.sadovnikov
 * Date: 15.11.2019
 * Time: 11:36
 */

namespace App\Utils;

/**
 * Класс для работы со строками
 *
 * Class StringHelper
 *
 * @package App\Utils
 */
class StringHelper
{
    public static function splitId($data, $split = ",|\n|\r\n?")
    {
        if (is_array($data)) {
            $clean_data = $data;
        } else {
            $clean_data = preg_split("#{$split}#", $data);
        }
        
        $clean_data = array_map('trim', $clean_data);
        $clean_data = array_unique($clean_data);
        $clean_data = array_filter($clean_data); // remove empty elemets
        $clean_data = array_values($clean_data);
        
        return $clean_data;
    }
}