<?php

namespace modele;

class offre
{
    // --- Propriétés ---
    private $idOffre;
    private $titre;
    private $rue;
    private $cp;
    private $ville;
    private $description;
    private $salaire;
    private $typeOffre;
    private $etat;
    private $dateCreation;
    private $refEntreprise;

    // --- Constructeur ---
    public function __construct(array $data = [])
    {
        if (!empty($data)) {
            $this->hydrate($data);
        }
    }

    // --- Hydratation automatique ---
    public function hydrate(array $data)
    {
        foreach ($data as $key => $value) {
            // Adapter les clés de la BDD vers le format camelCase de la classe
            switch ($key) {
                case 'id_offre':       $key = 'idOffre'; break;
                case 'type_offre':     $key = 'typeOffre'; break;
                case 'date_creation':  $key = 'dateCreation'; break;
                case 'ref_entreprise': $key = 'refEntreprise'; break;
            }

            $method = 'set' . ucfirst($key);
            if (method_exists($this, $method)) {
                $this->$method($value);
            }
        }
    }

    // --- Getters / Setters ---

    public function getIdOffre()        { return $this->idOffre; }
    public function setIdOffre($id)     { $this->idOffre = (int)$id; }

    public function getTitre()          { return $this->titre; }
    public function setTitre($titre)    { $this->titre = $titre; }

    public function getRue()            { return $this->rue; }
    public function setRue($rue)        { $this->rue = $rue; }

    public function getCp()             { return $this->cp; }
    public function setCp($cp)          { $this->cp = $cp; }

    public function getVille()          { return $this->ville; }
    public function setVille($ville)    { $this->ville = $ville; }

    public function getDescription()    { return $this->description; }
    public function setDescription($desc) { $this->description = $desc; }

    public function getSalaire()        { return $this->salaire; }
    public function setSalaire($salaire) { $this->salaire = $salaire; }

    public function getTypeOffre()      { return $this->typeOffre; }
    public function setTypeOffre($type) { $this->typeOffre = $type; }

    public function getEtat()           { return $this->etat; }
    public function setEtat($etat)      { $this->etat = $etat; }

    public function getDateCreation()   { return $this->dateCreation; }
    public function setDateCreation($date) { $this->dateCreation = $date; }

    public function getRefEntreprise()  { return $this->refEntreprise; }
    public function setRefEntreprise($ref) { $this->refEntreprise = $ref; }
}
