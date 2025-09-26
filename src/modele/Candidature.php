<?php

namespace modele;

class Candidature
{
    private $idCandidature;
    private $motivation;
    private $dateCandidature;


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

}