<?php
namespace modele;

class RForum{
    private $idRepForum;
    private $contenue;
    private $dateCreation;
    private $refPostForum;
    private $refUser;
    public function __construct(array $donnees)
    {
        $this->hydrate($donnees);
    }

    private function hydrate(array $donnees)
    {
        foreach ($donnees as $key => $value) {
            // On récupère le nom du setter correspondant à l'attribut
            $method = 'set' . ucfirst($key);

            // Si le setter correspondant existe.
            if (method_exists($this, $method)) {
                // On appelle le setter
                $this->$method($value);
            }
        }
    }

    /**
     * @return mixed
     */
    public function getIdRepForum()
    {
        return $this->idRepForum;
    }

    /**
     * @param mixed $idRepForum
     */
    public function setIdRepForum($idRepForum): void
    {
        $this->idRepForum = $idRepForum;
    }

    /**
     * @return mixed
     */
    public function getContenue()
    {
        return $this->contenue;
    }

    /**
     * @param mixed $contenue
     */
    public function setContenue($contenue): void
    {
        $this->contenue = $contenue;
    }

    /**
     * @return mixed
     */
    public function getDateCreation()
    {
        return $this->dateCreation;
    }

    /**
     * @param mixed $dateCreation
     */
    public function setDateCreation($dateCreation): void
    {
        $this->dateCreation = $dateCreation;
    }

    /**
     * @return mixed
     */
    public function getRefPostForum()
    {
        return $this->refPostForum;
    }

    /**
     * @param mixed $refPostForum
     */
    public function setRefPostForum($refPostForum): void
    {
        $this->refPostForum = $refPostForum;
    }

    /**
     * @return mixed
     */
    public function getRefUser()
    {
        return $this->refUser;
    }

    /**
     * @param mixed $refUser
     */
    public function setRefUser($refUser): void
    {
        $this->refUser = $refUser;
    }

}
