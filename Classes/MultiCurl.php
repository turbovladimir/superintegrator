<?php
    /**
     * Created by PhpStorm.
     * User: v.sadovnikov
     * Date: 04.03.2019
     * Time: 12:00
     */

    class MultiCurl
    {
        public $nodes; // одномерный массив с урлами
        public $reportMODE;

        protected $report;

        public function __construct($nodes, $reportMODE = 0)
        {
            $this->nodes = $nodes;
            $this->reportMODE = $reportMODE;

        }
        public function Start()
        {

            $mh = curl_multi_init();
            $curl_array = array();
            foreach($this->nodes as $i => $url)
            {
                // basket encode start
                $request = explode('&basket=', $url);
                $encode_bas = urlencode($request[1]);
                $url = $request[0].'&basket='.$encode_bas;
                // basket encode end

                $curl_array[$i] = curl_init($url);
                curl_setopt($curl_array[$i], CURLOPT_HEADER, 1);
                curl_setopt($curl_array[$i], CURLOPT_NOBODY, 1);
                curl_setopt($curl_array[$i], CURLOPT_CONNECTTIMEOUT, 5);
                curl_setopt($curl_array[$i], CURLOPT_RETURNTRANSFER, true);
                curl_multi_add_handle($mh, $curl_array[$i]);
            }
            $running = NULL;
            do {
                usleep(10000);
                curl_multi_exec($mh,$running);
            } while($running > 0);


            // report mode ON
            if ($this->reportMODE === 1){
                $res = array();

                foreach($this->nodes as $i => $url)
                {
                    $res[$url] = curl_multi_getcontent($curl_array[$i]);
                }
                $this->SetReport($res);
            }

            foreach($this->nodes as $i => $url){
                curl_multi_remove_handle($mh, $curl_array[$i]);
            }
            curl_multi_close($mh);

        }

        public function GetReport()
        {
            return $this->report;
        }

        protected function SetReport($res)
        {

            $report_arr = [];
            $error_details = '';

            foreach($res as $url => $response){

                $RequestStatus = 'HTTP/1.1 200 OK';

                if (stristr($response, $RequestStatus) == true) $report_arr['ok'][] = $response;
                elseif (stristr($response, $RequestStatus) == false)  $report_arr['error'][] = sprintf('url: [%s]; response: [%s]', $url, $response);
            }
            print_r($res);
            echo 'Кол-во индексов в рес :'.count($res);
            $textReport = sprintf('Количество одобрений: %s ', count($report_arr['ok']));

            if (!empty($report_arr['error'])){
                foreach ($report_arr['error'] as $value)  $error_details .= $value. '\r\n'; // подробности ошибок
                $textReport .= sprintf('Количество ошибок: $s, Подробности ошибок: [$s]', count($report_arr['error']), $error_details);
            }


            $this->report = $textReport;
        }

    }