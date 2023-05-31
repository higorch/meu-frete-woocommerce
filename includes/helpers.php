<?php

if (!defined('ABSPATH')) {
    exit;
}

function strip_number_format($number)
{
    $number = str_replace('.', '', $number);

    return  str_replace(',', '.', $number);
}
