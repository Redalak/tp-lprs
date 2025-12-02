<?php
// Traitement de création de candidature
session_start();
$base_path = '/lprs/tp-lprs';
require_once __DIR__ . '/../bdd/Bdd.php';

use bdd\Bdd;

// Vérifier connexion
if (empty($_SESSION['connexion']) || $_SESSION['connexion'] !== true) {
    $_SESSION['error'] = "Veuillez vous connecter pour postuler.";
    header('Location: ' . $base_path . '/vue/connexion.php');
    exit();
}

// Récupérer l'id utilisateur supportant deux clés possibles
$userId = $_SESSION['user_id'] ?? $_SESSION['id_user'] ?? null;
if (!$userId) {
    $_SESSION['error'] = "Session utilisateur invalide.";
    header('Location: ' . $base_path . '/index.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . $base_path . '/index.php');
    exit();
}

$refOffre   = isset($_POST['ref_offre']) ? (int)$_POST['ref_offre'] : 0;
$motivation = trim($_POST['motivation'] ?? '');

$errors = [];
if ($refOffre <= 0) $errors[] = "Offre inconnue.";
if ($motivation === '') $errors[] = "La lettre de motivation est obligatoire.";
if (empty($_FILES['cv']) || $_FILES['cv']['error'] !== UPLOAD_ERR_OK) $errors[] = "Le CV est obligatoire.";

// Validation fichier CV
$cvFilename = null;
if (empty($errors)) {
    $file = $_FILES['cv'];
    if ($file['size'] > 2 * 1024 * 1024) { // 2 Mo
        $errors[] = "Le CV doit faire moins de 2 Mo.";
    }
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime = $finfo->file($file['tmp_name']);
    if ($mime !== 'application/pdf') {
        $errors[] = "Le CV doit être un PDF.";
    }
}

if (!empty($errors)) {
    $_SESSION['error'] = implode("\n", $errors);
    header('Location: ' . $base_path . '/vue/offres/detail.php?id=' . urlencode((string)$refOffre));
    exit();
}

// Déplacer le fichier
$uploadDir = __DIR__ . '/../../uploads/cv';
if (!is_dir($uploadDir)) {
    @mkdir($uploadDir, 0775, true);
}
$cvFilename = sprintf('cv_%d_%s.pdf', $userId, bin2hex(random_bytes(6)));
$destPath = $uploadDir . '/' . $cvFilename;
if (!move_uploaded_file($_FILES['cv']['tmp_name'], $destPath)) {
    $_SESSION['error'] = "Impossible d'enregistrer le CV.";
    header('Location: ' . $base_path . '/vue/offres/detail.php?id=' . urlencode((string)$refOffre));
    exit();
}

// Insertion BDD
try {
    $pdo = (new Bdd())->getBdd();
    $stmt = $pdo->prepare('INSERT INTO candidature (motivation, cv, date_candidature, ref_offre, ref_user) VALUES (:motivation, :cv, NOW(), :ref_offre, :ref_user)');
    $stmt->execute([
        'motivation' => $motivation,
        'cv' => $cvFilename,
        'ref_offre' => $refOffre,
        'ref_user' => $userId,
    ]);

    $_SESSION['success'] = "Votre candidature a été envoyée avec succès.";
    header('Location: ' . $base_path . '/vue/offres/detail.php?id=' . urlencode((string)$refOffre));
    exit();
} catch (Throwable $e) {
    error_log('Erreur candidature: ' . $e->getMessage());
    $_SESSION['error'] = "Erreur serveur lors de l\'envoi de la candidature.";
    header('Location: ' . $base_path . '/vue/offres/detail.php?id=' . urlencode((string)$refOffre));
    exit();
}
