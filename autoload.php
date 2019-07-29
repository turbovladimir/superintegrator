<?php
/**
 * Created by PhpStorm.
 * User: v.sadovnikov
 * Date: 14.03.2019
 * Time: 10:54
 */

spl_autoload_register(function ($class) {
    include  'vendor/'.$class .'.php';
});

include_once 'config.php';
require 'vendor/autoload.php';
?>