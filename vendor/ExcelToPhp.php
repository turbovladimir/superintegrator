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
        public $array;
        protected $files;
        
        public function __construct($files)
        {
            $this->files = $files;
        }
        
        /**@return bool   возвращает true если файлы подходят по формату и названиию */
        public function ExcelChecker($checkWord, $fileFormat): bool
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
                if (stripos($file_name, $checkWord) === false) {
                    throw new fileException('Файл должен быть скачан в админке процессинга из раздела Archive');
                    $error_file++;
                    
                } else {
                    if (($files[$i]['type']) !== $fileFormat) {
                        throw new fileException('Формат файла должен быть *.csv');
                        $error_file++;
                    }
                }
            }
            
            if ($error_file) {
                return false;
            } else {
                if ($error_file === 0) {
                    return true;
                }
            }
        }
        
        /**@return array  возвращает содержимое файлов в ввиде массива где каждый элемент равен строке */
        public function toArray(): array
        {
            //собрали файлы в переменную
            $files = $this->files;
            //посчитали количество
            $numberOfFiles = count($files);
            //двумерный массив для того чтобы сложить все в $array
            $bufferArrays = [];
            
            $array = [];
            
            for ($i = 0; $i < $numberOfFiles; $i++) {
                $file = $files[$i]['tmp_name'];
                $handle = fopen($file, 'rb');
                
                while (($row = fgetcsv($handle, 10000, "\n")) !== false) {
                    $bufferArrays[$i][] = implode(';', $row);
                }
    
                $bufferArrays[$i]= self::filterArray($bufferArrays[$i], 'request_type', 'request_url');
                $array = array_merge($array, $bufferArrays[$i]); // delete header and merge in data
            }
            
            return $array;
        }
        
        protected function filterArray($array, $filterTypeColumn, $filterParamsColumn)
        {
            $filterArr = [];
            $arrayHead = explode(';', $array[0]); // получаем шапку таблицы в виде массива
            $arrayCounter = count($array);
            $headColumnsCounter = count($arrayHead);
            
            // определяем какие колонки это тип и параметры реквестов
            
            for ($typeColumn = 0; $typeColumn < $headColumnsCounter; $typeColumn++) {
                if ($arrayHead[$typeColumn] === $filterTypeColumn) {
                    break;
                }
            }
            
            for ($paramColumn = 0; $paramColumn < $headColumnsCounter; $paramColumn++) {
                if ($arrayHead[$paramColumn] === $filterParamsColumn) {
                    break;
                }
            }
            
            // собираем массив урлов
            for ($i = 0; $i < $arrayCounter; $i++)
            {
                $line = $array[$i];
                $line = explode(';', $line);
                
                $type = $line[$typeColumn];
                $params =  $line[$paramColumn];
                        
                if ($type === 'postback') {
                    $urlPath = "http://cityads.ru";
                    $url = $urlPath.$params;
                    $url = self::paramEncode($url);
                    $filterArr[] = $url;
        
                } elseif ($type === 'pixel') {
                    $urlPath = "http://cityadspix.com";
                    $url = $urlPath.$params;
                    $url = self::paramEncode($url);
                    $filterArr[] = $url;
                }
            }
            
            return $filterArr;
        }
        
        protected function paramEncode($url)
        {
            $urlArr = explode('?', $url);
            $params = trim($urlArr[1], '"');
            $params = str_replace('""', '"', $params);
            $encodeParams = urlencode($params);
            
            return $urlArr[0].'?'.$encodeParams;
            
        }
    }