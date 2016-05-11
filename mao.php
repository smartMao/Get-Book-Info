<?php

function dump( $data )
{
    echo "<pre>";
    var_dump( $data );
    echo "<br/>";
}




include_once 'simple_html_dom.php';

$html = file_get_html('http://product.dangdang.com/20449084.html');
dump( $html );exit;

