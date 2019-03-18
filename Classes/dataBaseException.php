<?php

/**
 * Created by PhpStorm.
 * User: v.sadovnikov
 * Date: 15.03.2019
 * Time: 12:48
 * Определим свой класс исключения
 */
class dataBaseException extends Exception
{

    // Переопределим исключение так, что параметр message станет обязательным
    public function __construct($message) {
        $message = 'Ошибка подключение к базе данных! '.$message;

        // убедитесь, что все передаваемые параметры верны
        parent::__construct($message);
    }

}