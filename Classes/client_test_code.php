<?php
include_once 'SPEED_EXEC_TEST.php';
include_once 'PAGE.php';
$my_test = new SPEED_EXEC_TEST('Потестим ка','log_file');

$my_test->start_test();

$i= 0;
while ($i !== 1000000) $i++;
$my_test->end_test();

?>