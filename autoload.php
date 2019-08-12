<?php
/**
 * Created by PhpStorm.
 * User: v.sadovnikov
 * Date: 14.03.2019
 * Time: 10:54
 */
include 'config.php';
define('ROOT_DIR', __DIR__);
define('MIGRATION_FOLDER', ROOT_DIR . '/' .'migrations/');

spl_autoload_register(function ($class) {
    include  'vendor/'.$class .'.php';
});

require 'vendor/autoload.php';
?>