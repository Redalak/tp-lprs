<?php


require_once __DIR__ . '/../src/repository/OffreRepo.php';
require_once __DIR__ . '/../src/modele/offre.php';

use repository\OffreRepo;
use modele\offre;

$offreRepo = new OffreRepo();
$offres = $offreRepo->listeOffre();

// Traitement du formulaire de création
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_offre'])) {
    $titre        = $_POST['titre'];
    $rue          = $_POST['rue'];
    $cp           = $_POST['cp'];
    $ville        = $_POST['ville'];
    $description  = $_POST['description'];
    $salaire      = !empty($_POST['salaire']) ? $_POST['salaire'] : null;
    $type_offre   = $_POST['type_offre'];
    $etat         = $_POST['etat'];

    $newOffre = new offre([
        'titre'        => $titre,
        'rue'          => $rue,
        'cp'           => $cp,
        'ville'        => $ville,
        'description'  => $description,
        'salaire'      => $salaire,
        'type_offre'   => $type_offre,
        'etat'         => $etat
    ]);

    $offreRepo->ajoutOffre($newOffre);

    header('Location: ' . $_SERVER['PHP_SELF']);
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administration - Gestion des Offres</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="css/admin-style.css">
    <style>
        .badge {
            display: inline-block;
            padding: 0.25em 0.6em;
            font-size: 0.75em;
            font-weight: 600;
            line-height: 1;
            text-align: center;
            white-space: nowrap;
            vertical-align: baseline;
            border-radius: 0.25rem;
        }
        .badge-success {
            background-color: #28a745;
            color: white;
        }
        .badge-warning {
            background-color: #ffc107;
            color: #212529;
        }
        .badge-secondary {
            background-color: #6c757d;
            color: white;
        }
        .badge-info {
            background-color: #17a2b8;
            color: white;
        }
        .description-cell {
            max-width: 200px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            cursor: help;
            position: relative;
        }
        .description-cell:hover::after {
            content: attr(title);
            position: absolute;
            left: 0;
            top: 100%;
            z-index: 1000;
            background: white;
            border: 1px solid #ddd;
            padding: 10px;
            border-radius: 4px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            white-space: normal;
            width: 300px;
            font-size: 0.9em;
            color: #333;
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
        <h1>Gestion des Offres d'Emploi</h1>
        
        <div class="alert alert-info">
            <i class="bi bi-info-circle"></i> Gestion des offres d'emploi et de stage de l'école.
        </div>

        <div class="table-responsive">
            <table>
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Titre</th>
                    <th>Localisation</th>
                    <th>Description</th>
                    <th>Salaire</th>
                    <th>Type</th>
                    <th>État</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach($offres as $offre): 
                    $etatClass = '';
                    switch($offre->getEtat()) {
                        case 'actif':
                        case 'ouvert':
                            $etatClass = 'badge-success';
                            break;
                        case 'brouillon':
                            $etatClass = 'badge-warning';
                            break;
                        case 'ferme':
                        case 'clos':
                            $etatClass = 'badge-secondary';
                            break;
                        default:
                            $etatClass = 'badge-info';
                    }
                ?>
                    <tr>
                        <td><?= $offre->getIdOffre() ?></td>
                        <td><?= htmlspecialchars($offre->getTitre()) ?></td>
                        <td>
                            <div><?= htmlspecialchars($offre->getVille()) ?></div>
                            <small class="text-muted"><?= htmlspecialchars($offre->getCp()) ?></small>
                        </td>
                        <td class="description-cell" title="<?= htmlspecialchars($offre->getDescription()) ?>">
                            <?= htmlspecialchars(substr($offre->getDescription(), 0, 50)) ?>...
                        </td>
                        <td>
                            <?php
                            $sal = $offre->getSalaire();
                            echo ($sal !== null && $sal !== '') ? htmlspecialchars($sal) : '<span class="text-muted">Non spécifié</span>';
                            ?>
                        </td>
                        <td>
                            <span class="badge badge-info"><?= htmlspecialchars($offre->getTypeOffre()) ?></span>
                        </td>
                        <td>
                            <span class="badge <?= $etatClass ?>">
                                <?= ucfirst(htmlspecialchars($offre->getEtat())) ?>
                            </span>
                        </td>
                        <td><?= date('d/m/Y', strtotime($offre->getDateCreation())) ?></td>
                        <td class="actions">
                            <a href="modifOffre.php?id=<?= $offre->getIdOffre() ?>" class="btn btn-sm btn-primary" title="Modifier">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <a href="suppOffre.php?id=<?= $offre->getIdOffre() ?>" 
                               class="btn btn-sm btn-danger" 
                               title="Supprimer"
                               onclick="return confirm('Voulez-vous vraiment supprimer cette offre ?')">
                                <i class="bi bi-trash"></i>
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <section class="mt-5">
            <h2>Créer une nouvelle offre</h2>
            
            <form method="post" class="form-container">
                <input type="hidden" name="create_offre" value="1">
                
                <div class="row">
                    <div class="col">
                        <div class="form-group">
                            <label for="titre">Titre du poste :</label>
                            <input type="text" id="titre" name="titre" class="form-control" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="rue">Adresse :</label>
                            <input type="text" id="rue" name="rue" class="form-control" required>
                        </div>
                        
                        <div class="row">
                            <div class="col">
                                <div class="form-group">
                                    <label for="cp">Code postal :</label>
                                    <input type="text" id="cp" name="cp" class="form-control" required>
                                </div>
                            </div>
                            <div class="col">
                                <div class="form-group">
                                    <label for="ville">Ville :</label>
                                    <input type="text" id="ville" name="ville" class="form-control" required>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col">
                        <div class="form-group">
                            <label for="type_offre">Type d'offre :</label>
                            <select id="type_offre" name="type_offre" class="form-control" required>
                                <option value="">-- Sélectionner --</option>
                                <option value="CDI">CDI</option>
                                <option value="CDD">CDD</option>
                                <option value="Intérim">Intérim</option>
                                <option value="Stage">Stage</option>
                                <option value="Alternance">Alternance</option>
                                <option value="Saisonnier">Saisonnier</option>
                                <option value="Freelance">Freelance</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="etat">État :</label>
                            <select id="etat" name="etat" class="form-control" required>
                                <option value="">-- Sélectionner --</option>
                                <option value="actif">Actif</option>
                                <option value="ferme">Fermé</option>
                                <option value="brouillon">Brouillon</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="salaire">Salaire (facultatif) :</label>
                            <input type="text" id="salaire" name="salaire" class="form-control" 
                                   placeholder="ex: 1900€ brut / mois">
                        </div>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="description">Description du poste :</label>
                    <textarea id="description" name="description" class="form-control" rows="5" required></textarea>
                </div>
                
                <div class="form-actions mt-4">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-send-plus"></i> Publier l'offre
                    </button>
                    <button type="reset" class="btn btn-secondary">
                        <i class="bi bi-x-circle"></i> Réinitialiser
                    </button>
                </div>
            </form>
        </section>
    </div>
</main>

<footer>
    <div class="container">
        <p>&copy; <?= date('Y') ?> École Supérieure. Tous droits réservés.</p>
    </div>
</footer>

<script>
    // Script pour confirmer la suppression
    document.addEventListener('DOMContentLoaded', function() {
        // Mise en forme des dates dans le tableau
        const dateCells = document.querySelectorAll('td:nth-child(8)');
        dateCells.forEach(cell => {
            const date = new Date(cell.textContent);
            if (!isNaN(date.getTime())) {
                cell.textContent = date.toLocaleDateString('fr-FR', {
                    day: '2-digit',
                    month: '2-digit',
                    year: 'numeric'
                });
            }
        });
    });
</script>

</body>
</html>
