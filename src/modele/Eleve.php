<?php

namespace modele;

class eleve
{
    private $idEleve;
    private $anneePromo;
    private $dateInscription;
    private $classe;
    private $ref_formation;


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
    public function getIdEleve()
    {
        return $this->idEleve;
    }

    /**
     * @param mixed $idEleve
     */
    public function setIdEleve($idEleve): void
    {
        $this->idEleve = $idEleve;
    }

    /**
     * @return mixed
     */
    public function getAnneePromo()
    {
        return $this->anneePromo;
    }

    /**
     * @param mixed $anneePromo
     */
    public function setAnneePromo($anneePromo): void
    {
        $this->anneePromo = $anneePromo;
    }

    /**
     * @return mixed
     */
    public function getDateInscription()
    {
        return $this->dateInscription;
    }

    /**
     * @param mixed $dateInscription
     */
    public function setDateInscription($dateInscription): void
    {
        $this->dateInscription = $dateInscription;
    }

    /**
     * @return mixed
     */
    public function getClasse()
    {
        return $this->classe;
    }

    /**
     * @param mixed $classe
     */
    public function setClasse($classe): void
    {
        $this->classe = $classe;
    }

    /**
     * @return mixed
     */
    public function getRefFormation()
    {
        return $this->ref_formation;
    }

    /**
     * @param mixed $ref_formation
     */
    public function setRefFormation($ref_formation): void
    {
        $this->ref_formation = $ref_formation;
    }


}