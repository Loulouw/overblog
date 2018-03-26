<?php

class Commentaire
{
    private $_auteur;
    private $_date;
    private $_contenu;
    private $_titre;
    private $_url;

    /**
     * Commentaire constructor.
     * @param $_auteur
     * @param $_date
     * @param $_contenu
     * @param $_titre
     * @param $_url
     */
    public function __construct($_auteur, $_date, $_contenu, $_titre, $_url)
    {
        $this->_auteur = $_auteur;
        $this->_date = $_date;
        $this->_contenu = $_contenu;
        $this->_titre = $_titre;
        $this->_url = $_url;
    }


    public function getXml()
    {
        return '<commentaire>
      <auteur>' . $this->_auteur . '</auteur>
      <date>' . $this->_date . '</date>
      <contenu>' . $this->_contenu . '</contenu>
      <titre>' . $this->_titre . '</titre>
      <url>' . $this->_url . '</url>
    </commentaire>';
    }

}

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

function getTitreArticle($doc)
{
    $nom = "NULL";
    $xpath = new DOMXPath($doc);
    $tbody = $doc->getElementsByTagName('body')->item(0);
    $q = 'div[@class="Content Content--page"]/div[@class="Content-main"]/div[@class="Post Post--OB"]/div[@class="Post-header PostHeader"]/div[@class="PostHeader-content"]/h2';
    $entries = $xpath->query($q, $tbody);
    foreach ($entries as $entry) {
        $nom = $entry->nodeValue;
    }
    return $nom;
}


function getComment($doc)
{
    $res = array();
    $xpath = new DOMXPath($doc);
    $tbody = $doc->getElementById('ob-comments');
    $q = 'div[@class="ob-list"]/div[@class="ob-comment"]';
    $q2 = 'div[@class="ob-list"]/div[@class="ob-comment-replies"]/div[@class="ob-comment"]';
    $entries1 = $xpath->query($q, $tbody);
    $entries2 = $xpath->query($q2, $tbody);
    foreach ($entries1 as $e) {
        array_push($res, $e->ownerDocument->saveHTML($e));
    }
    foreach ($entries2 as $e) {
        array_push($res, $e->ownerDocument->saveHTML($e));
    }
    return $res;
}

function getAuteurComment($doc){
    $res = "NULL";
    $xpath = new DOMXPath($doc);
    $q = 'div/p[@class="ob-info"]/span[@class="ob-user"]/span[@class="ob-name"]/span';
    $entries = $xpath->query($q);
    foreach ($entries as $e){
        $res = $e->nodeValue;
    }
    return $res;
}

function getDateComment($doc){
    $res = "NULL";
    $xpath = new DOMXPath($doc);
    $q = 'div/p[@class="ob-info"]/span[@class="ob-user"]/span[@class="ob-date"]';
    $entries = $xpath->query($q);
    foreach ($entries as $e){
        $res = $e->nodeValue;
    }
    return $res;
}

function getContentComment($doc){
    $res = "NULL";
    $xpath = new DOMXPath($doc);
    $q = 'div/p[@class="ob-message"]/span';
    $entries = $xpath->query($q);
    foreach ($entries as $e){
        $res = $e->nodeValue;
    }
    return $res;
}

$url = "http://ltd-rando68.over-blog.com/";
libxml_use_internal_errors(true);

$commentaires = array();

$html = url_get_contents($url);
$doc = getDomDocument($html);
$articles = getArticle($doc);
foreach ($articles as $article) {
    $htmlA = url_get_contents($article);
    $docA = getDomDocument($htmlA);
    $titre = getTitreArticle($docA);
    foreach (getComment($docA) as $c) {
        $docC = getDomDocument($c);
        $auteur = getAuteurComment($docC);
        $date = getDateComment($docC);
        $content = getContentComment($docC);
        array_push($commentaires,new Commentaire($auteur,$date,$content,$titre,$article));
    }
}

foreach ($commentaires as $c){
    echo $c->getXml() . "<br><br>";
}
