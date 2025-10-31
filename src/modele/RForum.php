<?php
namespace modele;

class RForum {
    private $idReply;
    private $postId;
    private $parentId; // réponse parente (optionnel)
    private $contenue;
    private $dateCreation;
    private $refUser;

    public function __construct(array $d){ $this->hydrate($d); }

    private function hydrate(array $d){
        foreach($d as $k=>$v){
            $m = 'set'.str_replace(' ', '', ucwords(str_replace('_',' ',$k)));
            if(method_exists($this,$m)) $this->$m($v);
        }
    }

    public function getIdReply(){ return $this->idReply; }
    public function setIdReply($v){ $this->idReply = (int)$v; }

    public function getPostId(){ return $this->postId; }
    public function setPostId($v){ $this->postId = (int)$v; }

    public function getParentId(){ return $this->parentId; }
    public function setParentId($v){ $this->parentId = $v !== null ? (int)$v : null; }

    public function getContenue(){ return $this->contenue; }
    public function setContenue($v){ $this->contenue = $v; }

    public function getDateCreation(){ return $this->dateCreation; }
    public function setDateCreation($v){ $this->dateCreation = $v; }

    public function getRefUser(){ return $this->refUser; }
    public function setRefUser($v){ $this->refUser = (int)$v; }
}