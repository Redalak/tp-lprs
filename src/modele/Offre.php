<?php

namespace modele;

class offre
{
private $idOffre;
private $titre;
private $rue;
private $cp;
private $ville;
private $description;
private $salaire;
private $typeOffre;
private $dateCreation;
private $etat;
private $ref_entreprise;


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
    }/**
 * @return mixed
 */
public function getIdOffre()
{
    return $this->idOffre;
}/**
 * @param mixed $idOffre
 */
public function setIdOffre($idOffre)
{
    $this->idOffre = $idOffre;
}/**
 * @return mixed
 */
public function getTitre()
{
    return $this->titre;
}/**
 * @param mixed $titre
 */
public function setTitre($titre)
{
    $this->titre = $titre;
}/**
 * @return mixed
 */
public function getDescription()
{
    return $this->description;
}/**
 * @param mixed $description
 */
public function setDescription($description)
{
    $this->description = $description;
}/**
 * @return mixed
 */
public function getMission()
{
    return $this->mission;
}/**
 * @param mixed $mission
 */
public function setMission($mission)
{
    $this->mission = $mission;
}/**
 * @return mixed
 */
public function getSalaire()
{
    return $this->salaire;
}/**
 * @param mixed $salaire
 */
public function setSalaire($salaire)
{
    $this->salaire = $salaire;
}/**
 * @return mixed
 */
public function getTypeOffre()
{
    return $this->typeOffre;
}/**
 * @param mixed $typeOffre
 */
public function setTypeOffre($typeOffre)
{
    $this->typeOffre = $typeOffre;
}/**
 * @return mixed
 */
public function getDateCreation()
{
    return $this->dateCreation;
}/**
 * @param mixed $dateCreation
 */
public function setDateCreation($dateCreation)
{
    $this->dateCreation = $dateCreation;
}/**
 * @return mixed
 */
public function getEtat()
{
    return $this->etat;
}/**
 * @param mixed $etat
 */
public function setEtat($etat)
{
    $this->etat = $etat;
}

    /**
     * @return mixed
     */
    public function getRue()
    {
        return $this->rue;
    }

    /**
     * @param mixed $rue
     */
    public function setRue($rue): void
    {
        $this->rue = $rue;
    }

    /**
     * @return mixed
     */
    public function getCp()
    {
        return $this->cp;
    }

    /**
     * @param mixed $cp
     */
    public function setCp($cp): void
    {
        $this->cp = $cp;
    }

    /**
     * @return mixed
     */
    public function getVille()
    {
        return $this->ville;
    }

    /**
     * @param mixed $ville
     */
    public function setVille($ville): void
    {
        $this->ville = $ville;
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