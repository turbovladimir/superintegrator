<?php
/**
 * Created by PhpStorm.
 * User: v.sadovnikov
 * Date: 14.03.2019
 * Time: 10:54
 */

define('ROOT_DIR', __DIR__);
define('MIGRATION_FOLDER', ROOT_DIR . 'migrations/');

spl_autoload_register(function ($class) {
    include  'vendor/'.$class .'.php';
});

include_once 'config.php';
require 'vendor/autoload.php';
?>