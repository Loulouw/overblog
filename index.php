<?php

function url_get_contents($url, $useragent = 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_6_8) AppleWebKit/534.30 (KHTML, like Gecko) Chrome/12.0.742.112 Safari/534.30', $headers = false, $follow_redirects = true, $debug = false)
{
    // initialise the CURL library
    $ch = curl_init();
    // specify the URL to be retrieved
    curl_setopt($ch, CURLOPT_URL, $url);
    // we want to get the contents of the URL and store it in a variable
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    // specify the useragent: this is a required courtesy to site owners
    curl_setopt($ch, CURLOPT_USERAGENT, $useragent);
    // ignore SSL errors
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    // return headers as requested
    if ($headers == true) {
        curl_setopt($ch, CURLOPT_HEADER, 1);
    }
    // only return headers
    if ($headers == 'headers only') {
        curl_setopt($ch, CURLOPT_NOBODY, 1);
    }
    // follow redirects - note this is disabled by default in most PHP installs from 4.4.4 up
    if ($follow_redirects == true) {
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    }
    // if debugging, return an array with CURL's debug info and the URL contents
    if ($debug == true) {
        $result['contents'] = curl_exec($ch);
        $result['info'] = curl_getinfo($ch);
    } // otherwise just return the contents as a variable
    else $result = curl_exec($ch);
    // free resources
    curl_close($ch);
    // send back the data
    return $result;
}

function getDomDocument($html)
{
    $doc = new DOMDocument();
    $doc->loadHTML($html);
    $doc->saveHTML();
    return $doc;
}

function getArticle($doc)
{
    $tabArticle = array();
    $xpath = new DOMXPath($doc);
    $tbody = $doc->getElementsByTagName('body')->item(0);
    $q = 'div[@class="Content Content--list"]/div[@class="Content-main"]/div[@class="PostPreview-container"]/div[@class="PostPreview PostPreview--OB"]/div[@class="PostPreview-content"]/h2/a/@href';
    $entries = $xpath->query($q, $tbody);
    foreach ($entries as $entry) {
        array_push($tabArticle, $entry->nodeValue);
    }
    return $tabArticle;
}

function getComment($doc){
  $xpath = new DOMXPath($doc);
  $tbody = $doc->getElementById('ob-comments');
  $q = 'div[@class="ob-list"]/div/div[@class="ob-comment"]/p[@class="ob-message"]/span';
  $q2 = 'div[@class="ob-list"]/div[@class="ob-comment"]/p[@class="ob-message"]/span';
  $entries = $xpath->query($q, $tbody);
  foreach ($entries as $entry) {
      echo $entry->nodeValue . "</br>";
  }
}

$url = "http://ltd-rando68.over-blog.com/";
libxml_use_internal_errors(true);
$html = url_get_contents($url);
$doc = getDomDocument($html);
$articles = getArticle($doc);
foreach($articles as $article){
  $htmlA = url_get_contents($article);
  $docA = getDomDocument($htmlA);
  getComment($docA);
}
