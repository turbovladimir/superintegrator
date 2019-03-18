<?php

/**
 * Created by PhpStorm.
 * User: v.sadovnikov
 * Date: 14.03.2019
 * Time: 12:58
 * класс принимает файлы из константы $_FILES проверяет их название и формат, а так же парсит их в массив php
 */
class ExcelToPhp
{
    public    $array;
    protected $files;

    public function __construct($files)
    {
        $this->files = $files;
    }

    /**@return bool   возвращает true если файлы подходят по формату и названиию*/
    public function ExcelChecker($checkWord, $fileFormat):bool
    {
        //собрали файлы в переменную
        $files = $this->files;
        //посчитали количество
        $numberOfFiles = count($files);
        //счетчик ошибок
        $error_file = 0;

        for ($i = 0; $i < $numberOfFiles; $i++) {
            ## check file name
            $file_name = (string)($files[$i]['name']);
            if (stripos($file_name, $checkWord) === false){
                throw new fileException('Файл должен быть скачан в админке процессинга из раздела Archive');
                $error_file++;

            }else if (($files[$i]['type']) !== $fileFormat) {
                throw new fileException('Формат файла должен быть *.csv');
                $error_file++;
            }
        }

        if ($error_file){
            return false;
        } else if ($error_file === 0){
            return true;
        }
    }

    /**@return array  возвращает содержимое файлов в ввиде массива где каждый элемент равен строке*/
    public function toArray():array
    {
        //собрали файлы в переменную
        $files = $this->files;
        //посчитали количество
        $numberOfFiles = count($files);
        //двумерный массив для того чтобы сложить все в $array
        $bufferArrays =[];

        $array =[];

        for ($i = 0; $i < $numberOfFiles; $i++) {
            $file = $files[$i]['tmp_name'];
            $handle = fopen($file, 'rb');

            while (($row = fgetcsv($handle, 10000, "\n")) !== FALSE) {
                $bufferArrays[$i][] = implode(';', $row);
            }

            array_shift($bufferArrays[$i]);
            $array = array_merge($array, $bufferArrays[$i]); // delete header and merge in data
        }

        return $array;
    }


}