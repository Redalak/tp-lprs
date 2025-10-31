<?php

namespace modele;

class event
{
    private $idEvent;
    private $type;
    private $titre;
    private $description;
    private $lieu;
    private $nombrePlace;
    private $dateCreation;
    private $dateEvent;
    private $etat;
    private $ref_user;



    public function __construct(array $donnees)
    {
        $this->hydrate($donnees);
    }

    private function hydrate(array $donnees)
    {
        foreach ($donnees as $key => $value) {
            // Gestion spéciale pour ref_user
            if ($key === 'ref_user') {
                $this->setRefUser($value);
                continue;
            }
            
            // On récupère le nom du setter correspondant à l'attribut
            $method = 'set' . ucfirst($key);

            // Si le setter correspondant existe
            if (method_exists($this, $method)) {
                // On appelle le setter
                $this->$method($value);
            }
        }
    }

    /**
     * @return mixed
     */
    public function getIdEvent()
    {
        return $this->idEvent;
    }

    /**
     * @param mixed $idEvent
     */
    public function setIdEvent($idEvent): void
    {
        $this->idEvent = $idEvent;
    }

    /**
     * @return mixed
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param mixed $type
     */
    public function setType($type): void
    {
        $this->type = $type;
    }

    /**
     * @return mixed
     */
    public function getTitre()
    {
        return $this->titre;
    }

    /**
     * @param mixed $titre
     */
    public function setTitre($titre): void
    {
        $this->titre = $titre;
    }

    /**
     * @return mixed
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param mixed $description
     */
    public function setDescription($description): void
    {
        $this->description = $description;
    }

    /**
     * @return mixed
     */
    public function getLieu()
    {
        return $this->lieu;
    }

    /**
     * @param mixed $lieu
     */
    public function setLieu($lieu): void
    {
        $this->lieu = $lieu;
    }

    /**
     * @return mixed
     */
    public function getNombrePlace()
    {
        return $this->nombrePlace;
    }

    /**
     * @param mixed $nombrePlace
     */
    public function setNombrePlace($nombrePlace): void
    {
        $this->nombrePlace = $nombrePlace;
    }

    /**
     * @return mixed
     */
    public function getDateCreation()
    {
        return $this->dateCreation;
    }

    /**
     * @param mixed $dateCreation
     */
    public function setDateCreation($dateCreation): void
    {
        $this->dateCreation = $dateCreation;
    }

    /**
     * @return string
     */
    public function getDateEvent()
    {
        return $this->dateEvent;
    }

    /**
     * @param mixed $dateEvent
     * @throws \Exception Si le format de date est invalide
     */
    public function setDateEvent($dateEvent): void
    {
        if (empty($dateEvent)) {
            throw new \InvalidArgumentException("La date de l'événement ne peut pas être vide");
        }

        // Si la date est déjà au bon format, on la conserve
        if (preg_match('/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/', $dateEvent)) {
            $this->dateEvent = $dateEvent;
            return;
        }
        
        // Si c'est un format datetime-local (YYYY-MM-DDTHH:MM)
        if (strpos($dateEvent, 'T') !== false) {
            $dateTime = new \DateTime($dateEvent);
            $this->dateEvent = $dateTime->format('Y-m-d H:i:s');
            return;
        }
        
        // Pour tout autre format, on essaie de le convertir
        try {
            $dateTime = new \DateTime($dateEvent);
            $this->dateEvent = $dateTime->format('Y-m-d H:i:s');
        } catch (\Exception $e) {
            throw new \Exception("Format de date invalide: " . $dateEvent);
        }
    }
    /**
     */
    public function getEtat()
    {
        return $this->etat;
    }

    /**
     * @param mixed $etat
     */
    public function setEtat($etat): void
    {
        $this->etat = $etat;
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