<?php

if(!function_exists('dump')) {
    function dump($data) {
        echo "<pre>", var_dump($data) , "</pre>";
    }
}