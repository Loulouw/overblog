<?php

function getContent(){
    $url = "http://ltd-rando68.over-blog.com/";
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_HEADER, true);    // we want headers
    curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
    curl_setopt($ch, CURLOPT_TIMEOUT,500);
    $output = curl_exec($ch);
    $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    echo 'HTTP code: ' . $httpcode . "  " . $output;
}

function getContentOverBlog()
{
    $url = "http://ltd-rando68.over-blog.com/";
    $opts = array(
        'http' => array(
            'method' => "GET",
            'header' => "Accept-language: en\r\n" .
                "Cookie: foo=bar\r\n"
        )
    );
    $context = stream_context_create($opts);
    $homepage = file_get_contents($url, false, $context);
    return $homepage;
}

getContent();

//echo getContentOverBlog();