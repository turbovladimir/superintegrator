<?php
include_once '../Classes/func.php';
download_send_headers("ali_data_export_" . date("Y-m-d H:i:s") . ".csv");

if (!empty($_POST['data'])){

        $data = $_POST['data'];
        $data = explode(',', $data);
        if (count($data) > 100){

            $data = array_chunk($data, 100);

            for ($i = 0; $i < count($data); $i++){

                $str = '';

                for ($n = 0; $n < count($data[$i]); $n++){
                    $str .= ','.$data[$i][$n];
                }
                $str = substr($str, 1);
                $url = "https://gw.api.alibaba.com/openapi/param2/2/portals.open/api.getOrderStatus/30056?appSignature=9FIO77dDIidM&orderNumbers=". $str;

             $result =  curl($url);
            }
        } else{
            $str = '';
            for ($i = 0; $i < count($data); $i++){

                $str .= ','.$data[$i];
            }
            $str = substr($str, 1);
            $url = "https://gw.api.alibaba.com/openapi/param2/2/portals.open/api.getOrderStatus/30056?appSignature=9FIO77dDIidM&orderNumbers=". $str;

            $result =  curl($url);

        }

        $result = json_decode($result, TRUE);

        $array = $result['result']['orders'];

        echo array2csv($array);
    }
?>