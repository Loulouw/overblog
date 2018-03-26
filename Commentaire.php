<?php
/**
 * Created by IntelliJ IDEA.
 * User: Loulouw
 * Date: 26/03/2018
 * Time: 10:32
 */

class Commentaire
{
  private $_auteur;
  private $_date;
  private $_contenu;
  private $_titre;
  private $_url;

  public function getXml(){
    return '<commentaire>
      <auteur>' . $this->_auteur . '</auteur>
      <date>' . $this->_date . '</date>
      <contenu>' . $this->_contenu . '</contenu>
      <titre>' . $this->_titre . '</titre>
      <url>' . $this->_url . '</url>
    </commentaire>';
  }
}
