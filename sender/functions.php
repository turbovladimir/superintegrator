<?php
function check_str ($str, $check_words){
    if(stristr($str, $check_words) == TRUE) return TRUE;
    else return FALSE;
}


