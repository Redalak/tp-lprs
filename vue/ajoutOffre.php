<?php
require_once __DIR__ . '/../src/repository/OffreRepo.php';
require_once __DIR__ . '/../src/repository/EntrepriseRepo.php';
use repository\OffreRepo;
use repository\EntrepriseRepo;
use modele\offre;

// Récupérer la liste des entreprises
$entrepriseRepo = new EntrepriseRepo();
$entreprises = $entrepriseRepo->listeEntreprise();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $offre = new offre([
        'titre' => $_POST['titre'],
        'rue' => $_POST['rue'],
        'cp' => $_POST['cp'],
        'ville' => $_POST['ville'],
        'description' => $_POST['description'],
        'salaire' => $_POST['salaire'],
        'type_offre' => $_POST['type_offre'],
        'etat' => 'en_attente',
        'ref_entreprise' => $_POST['ref_entreprise']
    ]);

    $offreRepo = new OffreRepo();
    $offreRepo->ajoutOffre($offre);
    
    header('Location: adminOffre.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter une offre - Administration</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="css/admin-style.css">
    <style>
        .form-container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .form-group {
            margin-bottom: 1.5rem;
        }
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: #333;
        }
        .form-control {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 1rem;
        }
        textarea.form-control {
            min-height: 150px;
            resize: vertical;
        }
        .btn-submit {
            background-color: #0d6efd;
            color: white;
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 1rem;
            font-weight: 500;
        }
        .btn-submit:hover {
            background-color: #0b5ed7;
        }
    </style>
</head>
<body>
    <header>
        <div class="container">
            <a href="#" class="logo">Administration</a>
            <nav>
                <ul>
                    <li><a href="adminEntreprise.php">Entreprises</a></li>
                    <li><a class="active" href="adminOffre.php">Offres</a></li>
                    <li><a href="adminEvent.php">Événements</a></li>
                    <li><a href="adminUser.php">Utilisateurs</a></li>
                    <li><a href="?deconnexion=1">Déconnexion</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <main class="main-content">
        <div class="container">
            <h1>Ajouter une offre d'emploi</h1>
            
            <div class="form-container">
                <form method="post" action="ajoutOffre.php">
                    <div class="form-group">
                        <label for="titre">Titre du poste *</label>
                        <input type="text" id="titre" name="titre" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label for="ref_entreprise">Entreprise *</label>
                        <select id="ref_entreprise" name="ref_entreprise" class="form-control" required>
                            <option value="">Sélectionnez une entreprise</option>
                            <?php foreach ($entreprises as $entreprise): ?>
                                <option value="<?= $entreprise->getIdEntreprise() ?>">
                                    <?= htmlspecialchars($entreprise->getNom()) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="type_offre">Type de contrat *</label>
                        <select id="type_offre" name="type_offre" class="form-control" required>
                            <option value="CDI">CDI</option>
                            <option value="CDD">CDD</option>
                            <option value="Stage">Stage</option>
                            <option value="Alternance">Alternance</option>
                            <option value="Freelance">Freelance</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="salaire">Salaire (optionnel)</label>
                        <input type="text" id="salaire" name="salaire" class="form-control" 
                               placeholder="Ex: 35000€/an">
                    </div>

                    <div class="form-group">
                        <label for="description">Description du poste *</label>
                        <textarea id="description" name="description" class="form-control" required></textarea>
                    </div>

                    <div class="form-group">
                        <label for="rue">Adresse *</label>
                        <input type="text" id="rue" name="rue" class="form-control" required>
                    </div>

                    <div class="row">
                        <div class="form-group" style="width: 30%; margin-right: 5%;">
                            <label for="cp">Code postal *</label>
                            <input type="text" id="cp" name="cp" class="form-control" required>
                        </div>
                        <div class="form-group" style="width: 65%;">
                            <label for="ville">Ville *</label>
                            <input type="text" id="ville" name="ville" class="form-control" required>
                        </div>
                    </div>

                    <div class="form-actions" style="margin-top: 2rem;">
                        <button type="submit" class="btn-submit">
                            <i class="bi bi-save"></i> Enregistrer l'offre
                        </button>
                        <a href="adminOffre.php" class="btn-cancel" style="margin-left: 1rem;">
                            <i class="bi bi-x-circle"></i> Annuler
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </main>

    <footer>
        <div class="container">
            <p>&copy; <?= date('Y') ?> École Supérieure. Tous droits réservés.</p>
        </div>
    </footer>
</body>
</html>
