<?php
namespace modele;

class alumni
{
    private $idAlumni;
    private $promotion;
    private $emploiActuel;
    private $entrepriseActuel;


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
    public function getIdAlumni()
    {
        return $this->idAlumni;
    }

    /**
     * @param mixed $idAlumni
     */
    public function setIdAlumni($idAlumni)
    {
        $this->idAlumni = $idAlumni;
    }

    /**
     * @return mixed
     */
    public function getPromotion()
    {
        return $this->promotion;
    }

    /**
     * @param mixed $promotion
     */
    public function setPromotion($promotion)
    {
        $this->promotion = $promotion;
    }

    /**
     * @return mixed
     */
    public function getEmploiActuel()
    {
        return $this->emploiActuel;
    }

    /**
     * @param mixed $emploiActuel
     */
    public function setEmploiActuel($emploiActuel)
    {
        $this->emploiActuel = $emploiActuel;
    }

    /**
     * @return mixed
     */
    public function getEntrepriseActuel()
    {
        return $this->entrepriseActuel;
    }

    /**
     * @param mixed $entrepriseActuel
     */
    public function setEntrepriseActuel($entrepriseActuel)
    {
        $this->entrepriseActuel = $entrepriseActuel;
    }

}