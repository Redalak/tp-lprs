<?php
namespace modele;

class alumni
{
    private $ref_user;
    private $emploi_actuel;
    private $ref_entreprise;


    public function __construct(array $donnees)
    {
        $this->hydrate($donnees);
    }

    private function hydrate(array $donnees) {
        foreach ($donnees as $key => $value) {
            // On récupère le nom du setter correspondant à l'attribut
            $method = 'set'.ucfirst($key);

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

    /**
     * @return mixed
     */
    public function getEmploiActuel()
    {
        return $this->emploi_actuel;
    }

    /**
     * @param mixed $emploi_actuel
     */
    public function setEmploiActuel($emploi_actuel): void
    {
        $this->emploi_actuel = $emploi_actuel;
    }

    /**
     * @return mixed
     */
    public function getRefEntreprise()
    {
        return $this->ref_entreprise;
    }

    /**
     * @param mixed $ref_entreprise
     */
    public function setRefEntreprise($ref_entreprise): void
    {
        $this->ref_entreprise = $ref_entreprise;
    }



}