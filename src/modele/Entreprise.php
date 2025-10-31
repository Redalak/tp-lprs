<?php

namespace modele;

class entreprise
{
    private $idEntreprise;
private $nom;
private $adresse;
private $site_web;
private $motifPartenariat;
private $dateInscription;
private $refOffre;
 private $nombre_offres; // nombre d'offres liées à l'entreprise



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
    public function getIdEntreprise()
    {
        return $this->idEntreprise;
    }

    /**
     * @param mixed $idEntreprise
     */
    public function setIdEntreprise($idEntreprise): void
    {
        $this->idEntreprise = $idEntreprise;
    }

    /**
     * @return mixed
     */
    public function getNom()
    {
        return $this->nom;
    }

    /**
     * @param mixed $nom
     */
    public function setNom($nom): void
    {
        $this->nom = $nom;
    }

    /**
     * @return mixed
     */
    public function getAdresse()
    {
        return $this->adresse;
    }

    /**
     * @param mixed $adresse
     */
    public function setAdresse($adresse): void
    {
        $this->adresse = $adresse;
    }

    /**
     * @return mixed
     */
    public function getSiteWeb()
    {
        return $this->site_web;
    }

    /**
     * @param mixed $site_web
     */
    public function setSiteWeb($site_web): void
    {
        $this->site_web = $site_web;
    }

    /**
     * @return mixed
     */
    public function getMotifPartenariat()
    {
        return $this->motifPartenariat;
    }

    /**
     * @param mixed $motifPartenariat
     */
    public function setMotifPartenariat($motifPartenariat): void
    {
        $this->motifPartenariat = $motifPartenariat;
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
    public function getRefOffre()
    {
        return $this->refOffre;
    }

    /**
     * @param mixed $refOffre
     */
    public function setRefOffre($refOffre): void
    {
        $this->refOffre = $refOffre;
    }

    /**
     * @return int|null
     */
    public function getNombreOffres()
    {
        return $this->nombre_offres ?? null;
    }

    /**
     * @param int $nombre
     */
    public function setNombreOffres($nombre): void
    {
        $this->nombre_offres = (int)$nombre;
    }


}