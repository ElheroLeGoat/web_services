<?php



function validateStr($str, $type, $min, $max) {
    switch($type) {
        case "1":
            if (!is_numeric($str) || strlen($str) < $min || strlen($str) > $max) {
                return false;
            }
            else {
                return true;
            }
        break;
        case "2":
         if (strlen($str) < $min || strlen($str) > $max) {
                return false;
            }
            else {
                return true;
            }
        break;
        default:
            return "Don't hit default please";
    }
}