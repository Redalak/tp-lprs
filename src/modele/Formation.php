<?php

namespace modele;

class formation
{
    private $idformation;
    private $nomformation;



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
    public function getIdformation()
    {
        return $this->idformation;
    }

    /**
     * @param mixed $idformation
     */
    public function setIdformation($idformation)
    {
        $this->idformation = $idformation;
    }

    /**
     * @return mixed
     */
    public function getNomformation()
    {
        return $this->nomformation;
    }

    /**
     * @param mixed $nomformation
     */
    public function setNomformation($nomformation)
    {
        $this->nomformation = $nomformation;
    }

}