<?php
namespace modele;

class RForum {
    private $idRForum;
    private $post_id;
    private $author;
    private $contenue;
    private $dateCreation;

    public function __construct(array $d = []) { foreach ($d as $k=>$v) if(property_exists($this,$k)) $this->$k = $v; }

    public function getIdRForum(){return $this->idRForum;}
    public function setIdRForum($v){$this->idRForum=$v;}
    public function getPostId(){return $this->post_id;}
    public function setPostId($v){$this->post_id=$v;}
    public function getAuthor(){return $this->author;}
    public function setAuthor($v){$this->author=$v;}
    public function getContenue(){return $this->contenue;}
    public function setContenue($v){$this->contenue=$v;}
    public function getDateCreation(){return $this->dateCreation;}
    public function setDateCreation($v){$this->dateCreation=$v;}
}
