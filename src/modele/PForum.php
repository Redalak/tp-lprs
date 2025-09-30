<?php
namespace modele;

class PForum {
    private $idPost;
    private $author;
    private $titre;
    private $contenue;
    private $dateCreation;

    public function __construct(array $d = []) { foreach ($d as $k=>$v) if(property_exists($this,$k)) $this->$k = $v; }

    public function getIdPost(){return $this->idPost;}
    public function setIdPost($v){$this->idPost=$v;}
    public function getAuthor(){return $this->author;}
    public function setAuthor($v){$this->author=$v;}
    public function getTitre(){return $this->titre;}
    public function setTitre($v){$this->titre=$v;}
    public function getContenue(){return $this->contenue;}
    public function setContenue($v){$this->contenue=$v;}
    public function getDateCreation(){return $this->dateCreation;}
    public function setDateCreation($v){$this->dateCreation=$v;}
}
