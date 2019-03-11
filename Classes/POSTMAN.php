<?php

    include_once '../db_connect/safemysql.class.php';
    include_once 'MultiCurl.php';

    class POSTMAN
    {
        public      $opt;
        public      $TableName;
        public      $DbConnect;

        protected   $urls; // массив с урлами
        protected   $url_amount;
        protected   $LogTableName;
        protected   $FILE_SOURCE;

        public function __construct($host, $dbname, $dbuser, $dbpass, $TableName, $LogTableName = 'table_log')
        {
            $opt = array("host" => "$host", "user" => "$dbuser", "pass" => "$dbpass", "db" => "$dbname", "charset" => "utf8");
            $this->opt = $opt;
            $DbConnect = new SafeMySQL($opt);
            $this->DbConnect = $DbConnect;
            $this->TableName = $TableName;
            $this->LogTableName = $LogTableName;

        }

        public function SendFromDb($ChunkLength, $reportMODE= 0) //логировать отправку если $reportMODE= 1
        {
            // check count table
            $getCount= $this->DbConnect->getCol("SELECT COUNT(*) FROM ?n;", $this->TableName);
            $this->url_amount = $getCount[0];

            // update log table:
            $this->UpdateLogTable( $this->opt['db'], $this->LogTableName,'url_amount', $this->url_amount);

            // sent to city
            if ($this->url_amount != 0){
                $this->urls = $this->DbConnect->getCol("SELECT `url` FROM ?n LIMIT ?i", $this->TableName, $ChunkLength);

                //delete chunk
                $this->DbConnect->query("DELETE  FROM ?n LIMIT ?i", $this->TableName, $ChunkLength);

                //write logs and send urls
                $multiCuRL = new MultiCurl($this->urls,$reportMODE);
                $multiCuRL->Start();
                if ($reportMODE === 1) return $multiCuRL->GetReport();

            }

            // check count table
            $getCount= $this->DbConnect->getCol("SELECT COUNT(*) FROM ?n;", $this->TableName);
            $this->url_amount = $getCount[0];

            // update log table:
            $this->UpdateLogTable( $this->opt['db'], $this->LogTableName,'url_amount', $this->url_amount);
        }

        public function SendtoDb($checkword, $FILE_SOURCE){
            $this->FILE_SOURCE = $FILE_SOURCE; // здесь храним массив с файлами
            // если файлы ок:
            if ($this->FileChecker($checkword, $FILE_SOURCE)){
                echo 'test FileChecker valid';
                $this->FileToArray();

                ## отправляем в БД

                $reset_table = "TRUNCATE TABLE ?n";

                $this->DbConnect->query($reset_table, $this->TableName);

                $sql_insert_table = "INSERT INTO ?n (`id`, `url`) VALUES (NULL, ?s);";

                for ($z =0; $z < count($this->urls); $z++){

                    $this->DbConnect->query($sql_insert_table, $this->TableName, $this->urls[$z]);

                }
## пишем логи
                $sql_update = "UPDATE ?n SET `url_amount`= ?i where `id`= 1";
                $this->DbConnect->query($sql_update, $this->LogTableName, count($this->urls));
            } else{
                echo 'test FileChecker error';
            }
        }

        protected function FileChecker($checkword, $myFILES){
            $error_file = 0;

            for ($i = 0; $i < count($myFILES); $i++) {
                ## check file name
                $file_name = strval($myFILES[$i]['name']);
                $file_type = strval($myFILES[$i]['type']);
                if ((stristr($file_name, $checkword) == false) || ($file_type != "application/vnd.ms-excel")) $error_file = + 1;

            }

            if ($error_file) return false;
            elseif ($error_file === 0) return true;

        }

        protected function FileToArray(){
            ## в цикле забираем из файлс в переменную массив данные построчно
            $RowData = array();

            for ($i = 0; $i < count($this->FILE_SOURCE); $i++) {
                $file = $this->FILE_SOURCE[$i]['tmp_name'];
                $handle = fopen($file, "r");

                while (($row = fgetcsv($handle, 10000, "\n")) !== FALSE) {
                    $get_file_arr[$i][] = implode(";", $row);
                }

                array_shift($get_file_arr[$i]);
                $RowData = array_merge($RowData, $get_file_arr[$i]); // delete header and merge in data
            }

            ## $RowData наша переменная с массивом

            $urls = []; // запишем в нее наши потсбэки и пиксели, писаться будет всё с фильтром по наличию определенных значнеий в строке
            for ($i = 0; $i < count($RowData); $i++) {
                $line = $RowData[$i];
                $line_arr = explode(";", $line);
                if ($line_arr[5] != '' && $line_arr[16] != ''){
                    @$type  = strval($line_arr[5]);
                    @$req   = strval($line_arr[16]);
                }
                $req    = trim($req, '"');
                $req    =  str_replace('""','"', $req);
                if ($type !== "" && $type !== "request_url" && $type == "postback") {
                    $request = "http://cityads.ru";
                    $str = $request . $req;
                    array_push($urls, $str);
                }
                if ($type !== "" && $type !== "request_url" && $type == "pixel") {
                    $request = "http://cityadspix.com";
                    $str = $request . $req;
                    array_push($urls, $str);
                }

            }

            $this->urls = $urls;
        }

        protected function isTableExist()
        {
            $parse_tables = $this->DbConnect->getAll("SELECT * FROM INFORMATION_SCHEMA.TABLES");
            for ($i = 0; $i < count($parse_tables); $i++){
                if ($parse_tables[$i]['TABLE_NAME'] == $this->TableName) $find_table = true;
            }
            if ($find_table == true) return true;
            else return false;
        }

        protected function UpdateLogTable($dbName, $tableName, $ColumnName, $Value)
        {
            // update log table:
            $sql_update = "UPDATE ?n.?n SET ?n = ?i  WHERE `id` = 1";
            $this->DbConnect->query($sql_update,$dbName, $tableName, $ColumnName, $Value);

        }
    }
?>