<?php

namespace modele;

class partenaire
{
    private $refUser;
    private $promo;
    private $emploieActuel;
    private $motifPartenaire;
    private $refEntreprise;

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

    /**
     * @return mixed
     */
    public function getPromo()
    {
        return $this->promo;
    }

    /**
     * @param mixed $promo
     */
    public function setPromo($promo): void
    {
        $this->promo = $promo;
    }

    /**
     * @return mixed
     */
    public function getEmploieActuel()
    {
        return $this->emploieActuel;
    }

    /**
     * @param mixed $emploieActuel
     */
    public function setEmploieActuel($emploieActuel): void
    {
        $this->emploieActuel = $emploieActuel;
    }

    /**
     * @return mixed
     */
    public function getMotifPartenaire()
    {
        return $this->motifPartenaire;
    }

    /**
     * @param mixed $motifPartenaire
     */
    public function setMotifPartenaire($motifPartenaire): void
    {
        $this->motifPartenaire = $motifPartenaire;
    }

    /**
     * @return mixed
     */
    public function getRefEntreprise()
    {
        return $this->refEntreprise;
    }

    /**
     * @param mixed $refEntreprise
     */
    public function setRefEntreprise($refEntreprise): void
    {
        $this->refEntreprise = $refEntreprise;
    }

}
