<?php

namespace modele;

class Candidature
{
    private $idCandidature;
    private $motivation;
    private $cv;
    private $dateCandidature;
    private $ref_offre;
    private $ref_user;


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
    public function getIdCandidature()
    {
        return $this->idCandidature;
    }

    /**
     * @param mixed $idCandidature
     */
    public function setIdCandidature($idCandidature)
    {
        $this->idCandidature = $idCandidature;
    }

    /**
     * @return mixed
     */
    public function getMotivation()
    {
        return $this->motivation;
    }

    /**
     * @param mixed $motivation
     */
    public function setMotivation($motivation)
    {
        $this->motivation = $motivation;
    }

    /**
     * @return mixed
     */
    public function getDateCandidature()
    {
        return $this->dateCandidature;
    }

    /**
     * @param mixed $dateCandidature
     */
    public function setDateCandidature($dateCandidature)
    {
        $this->dateCandidature = $dateCandidature;
    }

    /**
     * @return mixed
     */
    public function getCv()
    {
        return $this->cv;
    }

    /**
     * @param mixed $cv
     */
    public function setCv($cv): void
    {
        $this->cv = $cv;
    }

    /**
     * @return mixed
     */
    public function getRefOffre()
    {
        return $this->ref_offre;
    }

    /**
     * @param mixed $ref_offre
     */
    public function setRefOffre($ref_offre): void
    {
        $this->ref_offre = $ref_offre;
    }

    /**
     * @return mixed
     */
    public function getRefUser()
    {
        return $this->ref_user;
    }

    /**
     * @param mixed $ref_user
     */
    public function setRefUser($ref_user): void
    {
        $this->ref_user = $ref_user;
    }



}