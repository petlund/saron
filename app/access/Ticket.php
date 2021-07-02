<?php

    function getAccessTicket(){
        return "'" . random_int(pow(10,floor(log(PHP_INT_MAX)/log(10))), PHP_INT_MAX) . random_int(pow(10,floor(log(PHP_INT_MAX)/log(10))), PHP_INT_MAX) . "'";
    }
