<?php
namespace modele;

class Actualites
{
    private $id_actu;
    private $contexte;


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
    public function getIdActu()
    {
        return $this->id_actu;
    }

    /**
     * @param mixed $id_actu
     */
    public function setIdActu($id_actu): void
    {
        $this->id_actu = $id_actu;
    }


    /**
     * @return mixed
     */
    public function getContexte()
    {
        return $this->contexte;
    }

    /**
     * @param mixed $contexte
     */
    public function setContexte($contexte): void
    {
        $this->contexte = $contexte;
    }




}