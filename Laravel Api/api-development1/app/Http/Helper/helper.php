<?php

use Illuminate\Support\Facades\Hash;

if(!function_exists('p')){
    function p($data){
        echo "<pre>";
        
        print_r($data);
      
        echo "</pre>";
    }
}