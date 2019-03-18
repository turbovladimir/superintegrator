<?php
/**
 * Created by PhpStorm.
 * User: v.sadovnikov
 * Date: 14.03.2019
 * Time: 10:54
 */
function my_autoloader($class) {
    include  'Classes/'.$class .'.php';
}

spl_autoload_register('my_autoloader');
?>