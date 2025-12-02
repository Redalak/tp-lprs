<?php

use controllers\OffreController;
use helpers\AuthHelper;

// Page d'accueil des offres
$router->get('/offres', function() {
    $offreController = new OffreController();
    $offreController->index();
});

// Formulaire de création d'offre
$router->get('/offres/creer', function() {
    $offreController = new OffreController();
    $offreController->createForm();
});

// Traitement de la création d'offre
$router->post('/offres', function() {
    $offreController = new OffreController();
    $offreController->create();
});

// Détail d'une offre
$router->get('/offres/(\d+)', function($id) {
    $offreController = new OffreController();
    $offreController->show($id);
});

// Formulaire de modification d'offre
$router->get('/offres/(\d+)/modifier', function($id) {
    $offreController = new OffreController();
    $offreController->editForm($id);
});

// Traitement de la modification d'offre
$router->post('/offres/(\d+)', function($id) {
    $offreController = new OffreController();
    $offreController->update($id);
});

// Changement d'état d'une offre (AJAX)
$router->post('/api/offres/(\d+)/toggle-status', function($id) {
    $offreController = new OffreController();
    $offreController->toggleStatus($id);
});

// Suppression d'une offre
$router->post('/offres/(\d+)/supprimer', function($id) {
    $offreController = new OffreController();
    $offreController->delete($id);
});
