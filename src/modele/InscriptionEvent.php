<?php

namespace modele;

class InscriptionEvent
{
    private $idInscription;
    private $refUser;
    private $refEvenement;
    private $dateInscription;

    public function __construct(array $donnees)
    {
        $this->hydrate($donnees);
    }

    private function hydrate(array $donnees)
    {
        foreach ($donnees as $key => $value) {
            $method = 'set' . ucfirst($key);

            if (method_exists($this, $method)) {
                $this->$method($value);
            }
        }
    }

    /**
     * @return int
     */
    public function getIdInscription(): int
    {
        return $this->idInscription;
    }

    /**
     * @param int $idInscription
     */
    public function setIdInscription(int $idInscription): void
    {
        $this->idInscription = $idInscription;
    }

    /**
     * @return int
     */
    public function getRefUser(): int
    {
        return $this->refUser;
    }

    /**
     * @param int $refUser
     */
    public function setRefUser(int $refUser): void
    {
        $this->refUser = $refUser;
    }

    /**
     * @return int
     */
    public function getRefEvenement(): int
    {
        return $this->refEvenement;
    }

    /**
     * @param int $refEvenement
     */
    public function setRefEvenement(int $refEvenement): void
    {
        $this->refEvenement = $refEvenement;
    }

    /**
     * @return string
     */
    public function getDateInscription(): string
    {
        return $this->dateInscription;
    }

    /**
     * @param string $dateInscription
     */
    public function setDateInscription(string $dateInscription): void
    {
        $this->dateInscription = $dateInscription;
    }
}
