<?php
$error_file = 0;
$file_count;
for ($file_count = 0; $file_count < count($_FILES); $file_count++) {
    ## check file name
    $file_name = strval($_FILES[$file_count]['name']);
    $word = 'archive';
    $file_type = strval($_FILES[$file_count]['type']);

    if ((check_str($file_name, $word) == FALSE) || ($file_type != "application/vnd.ms-excel")) {
        $error_file = + 1;
    }
}
echo $file_count;
?>