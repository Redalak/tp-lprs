<?php

namespace controllers;

use helpers\AuthHelper;
use repository\OffreRepo;
use repository\UserRepo;

class OffreController
{
    private $offreRepo;
    private $userRepo;
    private $pdo;

    public function __construct()
    {
        $this->offreRepo = new OffreRepo();
        $this->userRepo = new UserRepo();
        
        // Initialisation de la connexion PDO
        $bdd = new \bdd\Bdd();
        $this->pdo = $bdd->getBdd();
    }

    /**
     * Affiche le formulaire de création d'offre
     */
    public function createForm()
    {
        // Vérifie que l'utilisateur est une entreprise
        AuthHelper::requireRole('entreprise');
        
        // Affiche le formulaire de création d'offre
        require_once __DIR__ . '/../vue/offres/creer.php';
    }

    /**
     * Traite la création d'une nouvelle offre
     */
    public function create()
    {
        AuthHelper::requireRole('entreprise');
        
        // Récupère l'entreprise de l'utilisateur connecté
        $user = $this->userRepo->trouverParId($_SESSION['user_id']);
        
        if (!$user || !$user->getRefEntreprise()) {
            $_SESSION['error'] = 'Aucune entreprise associée à votre compte.';
            header('Location: /mon-compte');
            exit();
        }
        
        // Validation des données
        $errors = [];
        $titre = trim($_POST['titre'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $typeOffre = $_POST['type_offre'] ?? '';
        $salaire = !empty($_POST['salaire']) ? (float)$_POST['salaire'] : null;
        
        if (empty($titre)) {
            $errors[] = 'Le titre est obligatoire';
        }
        
        if (empty($description)) {
            $errors[] = 'La description est obligatoire';
        }
        
        if (empty($typeOffre) || !in_array($typeOffre, ['CDI', 'CDD', 'Stage', 'Alternance', 'Autre'])) {
            $errors[] = 'Type d\'offre invalide';
        }
        
        if (!empty($errors)) {
            $_SESSION['form_errors'] = $errors;
            $_SESSION['form_data'] = $_POST;
            header('Location: /offres/creer');
            exit();
        }
        
        // Création de l'offre
        $data = [
            'titre' => $titre,
            'description' => $description,
            'type_offre' => $typeOffre,
            'salaire' => $salaire,
            'ref_entreprise' => $user->getRefEntreprise(),
            'rue' => $_POST['rue'] ?? '',
            'cp' => (int)($_POST['cp'] ?? 0),
            'ville' => $_POST['ville'] ?? '',
            'etat' => 'ouvert'
        ];
        
        try {
            $this->pdo->beginTransaction();
            
            $offreId = $this->offreRepo->ajouterOffre($data);
            
            $this->pdo->commit();
            
            $_SESSION['success'] = 'L\'offre a été créée avec succès.';
            header('Location: /offres/' . $offreId);
            exit();
            
        } catch (\Exception $e) {
            $this->pdo->rollBack();
            error_log('Erreur lors de la création de l\'offre : ' . $e->getMessage());
            
            $_SESSION['error'] = 'Une erreur est survenue lors de la création de l\'offre.';
            $_SESSION['form_data'] = $_POST;
            header('Location: /offres/creer');
            exit();
        }
    }

    /**
     * Affiche le formulaire de modification d'une offre
     */
    public function editForm($id)
    {
        AuthHelper::requireRole('entreprise');
        
        $offre = $this->offreRepo->trouverParId($id);
        
        if (!$offre) {
            $_SESSION['error'] = 'Offre non trouvée.';
            header('Location: /offres');
            exit();
        }
        
        // Vérifie que l'utilisateur est propriétaire de l'offre
        if (!AuthHelper::isOwnerOfOffer($id, $this->pdo)) {
            $_SESSION['error'] = 'Vous n\'êtes pas autorisé à modifier cette offre.';
            header('Location: /offres');
            exit();
        }
        
        // Affiche le formulaire de modification
        require_once __DIR__ . '/../vue/offres/modifier.php';
    }

    /**
     * Traite la modification d'une offre
     */
    public function update($id)
    {
        AuthHelper::requireRole('entreprise');
        
        // Vérifie que l'utilisateur est propriétaire de l'offre
        if (!AuthHelper::isOwnerOfOffer($id, $this->pdo)) {
            $_SESSION['error'] = 'Vous n\'êtes pas autorisé à modifier cette offre.';
            header('Location: /offres');
            exit();
        }
        
        // Validation des données (similaire à la création)
        // ...
        
        // Mise à jour de l'offre
        // ...
    }

    /**
     * Change l'état d'une offre (ouvert/fermé)
     */
    public function toggleStatus($id)
    {
        AuthHelper::requireRole('entreprise');
        
        // Vérifie que l'utilisateur est propriétaire de l'offre
        if (!AuthHelper::isOwnerOfOffer($id, $this->pdo)) {
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'Non autorisé']);
            exit();
        }
        
        try {
            $offre = $this->offreRepo->trouverParId($id);
            $nouvelEtat = $offre['etat'] === 'ouvert' ? 'ferme' : 'ouvert';
            
            $this->offreRepo->mettreAJourEtat($id, $nouvelEtat);
            
            echo json_encode([
                'success' => true, 
                'nouvelEtat' => $nouvelEtat,
                'libelleEtat' => $nouvelEtat === 'ouvert' ? 'Ouverte' : 'Fermée'
            ]);
            
        } catch (\Exception $e) {
            error_log('Erreur lors du changement d\'état de l\'offre : ' . $e->getMessage());
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Erreur serveur']);
        }
    }
}
