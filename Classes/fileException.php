<?php

/**
 * Created by PhpStorm.
 * User: v.sadovnikov
 * Date: 15.03.2019
 * Time: 14:05
 */
class fileException extends Exception
{
    // Переопределим исключение так, что параметр message станет обязательным
    public function __construct($message) {
        $message = 'Неверный файл! '.$message;

        // убедитесь, что все передаваемые параметры верны
        parent::__construct($message);
    }
}