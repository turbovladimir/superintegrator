<?php
setlocale(LC_ALL, 'ru_RU.UTF8');


class SPEED_EXEC_TEST
{
  public    $script_name;
  public    $mode; // print or log_file
  protected $start_time;
  protected $elapsed_time;
  protected $file_path;
  protected $report;

  public function __construct($script_name, $mode = 'print')
  {
      $this->script_name = mb_convert_encoding($script_name, "UTF-8");
      $this->mode = $mode;
  }

    public function start_test()
  {
      $this->start_time = microtime(true);
  }

  public function end_test()
  {
      if ($this->mode == 'print')
      {
          printf($this->script_name.' занял %.4F сек.', $this->count_time());
      } elseif ($this->mode == 'log_file')
      {
          $fp = fopen('test_speed_log.txt', 'a');
          $log_date =  date("Y-m-d H:i:s");
          $string = sprintf("log:[%s] script_name:[%s] report:[%s] speed:[%F сек.]", $log_date, $this->script_name, $this->report, $this->count_time());

          fwrite($fp, $string . PHP_EOL);
          fclose($fp);
      }

  }

  protected function count_time()
  {
      $this->elapsed_time = microtime(true) - $this->start_time;
      return $this->elapsed_time;
  }

  public function SetReport($report)
  {
            $this->report = $report;
  }

}
?>