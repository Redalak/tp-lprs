<?php
// Définir le titre de la page
$pageTitle = 'Accueil';

// Inclure l'en-tête qui gère la session et l'authentification
require_once __DIR__ . '/includes/header.php';

// Inclure les dépendances nécessaires
require_once __DIR__ . "/src/bdd/Bdd.php";
require_once __DIR__ . "/src/repository/EventRepo.php";
require_once __DIR__ . "/src/repository/UserRepo.php";
require_once __DIR__ . "/src/modele/User.php";
require_once __DIR__ . "/src/modele/Actualites.php";
require_once __DIR__ . "/src/repository/ActualitesRepo.php";

use repository\EventRepo;
use repository\UserRepo;
use repository\ActualitesRepo;

// Déconnexion simple via ?deco=true
if (!empty($_GET['deco']) && $_GET['deco'] === 'true') {
    session_destroy();
    header("Location: index.php");
    exit;
}

// Récupération des 3 prochains événements (à venir uniquement)
$eventRepo = new EventRepo();
$latestEvents = $eventRepo->getProchainsEvents(3);

// Récupération des 3 dernières actualités
$actualitesRepo = new ActualitesRepo();
$dernieresActualites = $actualitesRepo->getDernieresActualites();

// NOUVEAU: Récupérer l'utilisateur connecté
$userLoggedIn = null; // Initialiser
if (!empty($_SESSION['connexion']) && $_SESSION['connexion'] === true && !empty($_SESSION['id_user'])) {
    $userRepo = new UserRepo();
    // On suppose que vous avez une méthode getUserById()
    $userLoggedIn = $userRepo->getUserById($_SESSION['id_user']);
}
// Déterminer si l'utilisateur est admin (robuste selon ton modèle)x
$isAdmin = false;

if ($userLoggedIn) {
    if (method_exists($userLoggedIn, 'isAdmin')) {
        $isAdmin = (bool) $userLoggedIn->isAdmin();
    } elseif (method_exists($userLoggedIn, 'getRole')) {
        $role = strtolower((string) $userLoggedIn->getRole());
        $isAdmin = in_array($role, ['admin', 'role_admin'], true);
    } elseif (property_exists($userLoggedIn, 'role')) {
        $role = strtolower((string) $userLoggedIn->role);
        $isAdmin = in_array($role, ['admin', 'role_admin'], true);
    } elseif (!empty($_SESSION['role'])) {
        $role = strtolower((string) $_SESSION['role']);
        $isAdmin = in_array($role, ['admin', 'role_admin'], true);
    }
}

// Déterminer si l'utilisateur est une entreprise
$isEntreprise = false;
if ($userLoggedIn) {
    $roleStr = null;
    if (method_exists($userLoggedIn, 'getRole')) {
        $roleStr = strtolower((string) $userLoggedIn->getRole());
    } elseif (property_exists($userLoggedIn, 'role')) {
        $roleStr = strtolower((string) $userLoggedIn->role);
    } elseif (!empty($_SESSION['role'])) {
        $roleStr = strtolower((string) $_SESSION['role']);
    }
    if ($roleStr !== null) {
        $isEntreprise = in_array($roleStr, ['entreprise', 'role_entreprise'], true);
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>École | Construire Demain</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- Carousel CSS -->
    <link href="assets/css/carousel.css" rel="stylesheet">

    <style>
    /* --- Refined theme tokens --- */
    :root {
        --primary-color: #6C5CE7;         /* Indigo/Violet */
        --secondary-color: #00C2FF;       /* Cyan */
        --accent-color: #FF8A00;          /* Warm accent */
        --background-color: #F6F7FB;      /* Soft gray */
        --surface-color: #FFFFFF;         /* Cards / header */
        --text-color: #1F2937;            /* Slate 800 */
        --light-text-color: #F9FAFB;      /* Near-white */
        --muted-text-color: #6B7280;      /* Slate 500 */
        --shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
        --border-radius: 14px;
        --ring: 0 0 0 8px rgba(108, 92, 231, 0.10);
        --gradient: linear-gradient(135deg, #6C5CE7 0%, #8A5CF7 35%, #00C2FF 100%);
        --soft-gradient: linear-gradient(180deg, rgba(108,92,231,.08), rgba(0,194,255,.08));
    }

    /* --- Base --- */
    * { box-sizing: border-box; }
    html, body { height: 100%; }
    body {
        margin: 0;
        font-family: 'Poppins', system-ui, -apple-system, Segoe UI, Roboto, sans-serif;
        background: var(--background-color);
        color: var(--text-color);
        line-height: 1.7;
        -webkit-font-smoothing: antialiased;
        -moz-osx-font-smoothing: grayscale;
    }
    .container { max-width: 1200px; margin: 0 auto; padding: 0 22px; }

    /* --- Header --- */
    header {
        background: rgba(255,255,255,0.7);
        backdrop-filter: saturate(140%) blur(10px);
        -webkit-backdrop-filter: saturate(140%) blur(10px);
        position: sticky; top: 0; z-index: 1000;
        transition: box-shadow .3s ease, background-color .3s ease, transform .3s ease;
        border-bottom: 1px solid rgba(0,0,0,0.06);
    }
    header.scrolled { box-shadow: var(--shadow); background: rgba(255,255,255,0.9); }
    header .container { display: flex; justify-content: space-between; align-items: center; height: 72px; }

    .logo { font-size: 1.7rem; font-weight: 800; letter-spacing: .2px; background: var(--gradient); -webkit-background-clip: text; background-clip: text; color: transparent; }

    nav ul { list-style: none; display: flex; align-items: center; gap: 28px; padding-left: 0; margin: 0; overflow: visible; }
    nav ul li a {
        text-decoration: none;
        color: var(--text-color);
        font-weight: 600;
        position: relative;
        padding: 6px 0;
        transition: color .25s ease;
    }
    /* Gradient underline that animates in */
    nav ul li a::after {
        content: '';
        position: absolute; left: 0; bottom: -2px; height: 2px; width: 100%;
        background: var(--gradient);
        transform: scaleX(0);
        transform-origin: left;
        transition: transform .35s ease;
        border-radius: 1px;
    }
    nav ul li a:hover { color: #0F172A; }
    nav ul li a:hover::after { transform: scaleX(1); }
    nav ul li a.active { color: #0F172A; }
    nav ul li a.active::after { transform: scaleX(1); }

    /* --- Hero --- */
    .hero {
        background: url('https://source.unsplash.com/1600x900/?university,modern,architecture') center/cover no-repeat;
        height: 520px; position: relative; display: flex; align-items: center; justify-content: center; overflow: hidden;
        border-radius: 0 0 28px 28px;
    }
    .hero::before, .hero::after {
        content: '';
        position: absolute; width: 500px; height: 500px; border-radius: 50%;
        filter: blur(60px); opacity: .55; pointer-events: none;
        animation: floaty 16s ease-in-out infinite;
    }
    .hero::before { background: radial-gradient(circle at 30% 30%, #6C5CE7, transparent 60%); top: -120px; left: -120px; }
    .hero::after  { background: radial-gradient(circle at 70% 70%, #00C2FF, transparent 60%); bottom: -120px; right: -120px; animation-duration: 20s; }

    .hero-overlay { background: linear-gradient(45deg, rgba(16,24,40,0.7), rgba(0,194,255,0.38)); position: absolute; inset: 0; }
    .hero-content { color: var(--light-text-color); text-align: center; position: relative; z-index: 2; padding: 0 16px; transform: translateZ(0); }
    .hero-content h2 { font-size: clamp(2.2rem, 5vw, 3.8rem); font-weight: 800; margin: 0 0 14px; letter-spacing: .3px; line-height: 1.05; background: linear-gradient(90deg,#fff, #e5ecff); -webkit-background-clip: text; background-clip: text; color: transparent; text-shadow: 0 6px 30px rgba(0,0,0,.2);
    }
    .hero-content p { font-size: clamp(1.05rem, 2.4vw, 1.35rem); opacity: .95; margin: 0 0 28px; }

    .cta-button {
        background-image: var(--gradient);
        color: white; padding: 12px 26px; border-radius: 999px; text-decoration: none; font-weight: 700;
        box-shadow: var(--ring), 0 8px 20px rgba(0,0,0,.12);
        display: inline-block; transition: transform .25s ease, box-shadow .25s ease;
        position: relative; overflow: hidden;
    }
    .cta-button::before { content: ''; position: absolute; inset: 0; background: linear-gradient(120deg, rgba(255,255,255,.0) 0%, rgba(255,255,255,.35) 50%, rgba(255,255,255,.0) 100%); transform: translateX(-120%); }
    .cta-button:hover { transform: translateY(-2px) scale(1.02); box-shadow: 0 15px 35px rgba(108,92,231,.35); }
    .cta-button:hover::before { animation: shine 850ms ease forwards; }

    section { padding: 84px 0; }
    .section-title { text-align: center; font-size: clamp(2rem, 4vw, 2.6rem); color: #2B2F77; margin: 0 0 50px; letter-spacing: .2px; }

    /* --- Cards / Actus & Events --- */
    .actus-events { display: flex; gap: 30px; justify-content: center; flex-wrap: wrap; }
    .card {
        background: var(--surface-color);
        padding: 26px 28px; border-radius: var(--border-radius);
        box-shadow: var(--shadow);
        flex: 1 1 320px; max-width: 460px; transition: transform .3s ease, box-shadow .3s ease, border-color .3s ease;
        border: 1px solid rgba(0,0,0,0.05);
        position: relative;
    }
    .card::after { content: ''; position: absolute; inset: 0; border-radius: inherit; background: var(--soft-gradient); opacity: 0; transition: opacity .3s ease; pointer-events: none; }
    .card:hover { transform: translateY(-8px) scale(1.01); box-shadow: 0 18px 40px rgba(0,0,0,.12); }
    .card:hover::after { opacity: 1; }

    .card h3 { color: #2B2F77; margin: 0 0 16px; padding-bottom: 10px; display: inline-block; border-bottom: 2px solid transparent; background-image: linear-gradient(90deg, var(--secondary-color), var(--primary-color)); background-size: 100% 2px; background-repeat: no-repeat; background-position: left bottom; }
    .card ul { list-style: none; padding-left: 0; margin: 0; }
    .card ul li { margin-bottom: 14px; color: var(--muted-text-color); }
    .card ul li::before { content: '→'; margin-right: 10px; color: var(--secondary-color); }
    .card a { color: #0F172A; text-decoration: none; font-weight: 600; transition: color .2s ease; }
    .card a:hover { color: var(--secondary-color); }

    .more-wrapper { margin-top: 18px; }
    .btn-more { display: inline-block; padding: 10px 16px; border-radius: 10px; background: #EEF6FF; color: #1e40af; font-weight: 700; text-decoration: none; transition: transform .2s ease, background-color .2s ease; }
    .btn-more:hover { transform: translateY(-2px); background: #DBECFF; }

    /* --- Presentation blocks --- */
    .presentation { background: var(--surface-color); border-top: 1px solid rgba(0,0,0,0.06); }
    .presentation-content { max-width: 820px; margin: 0 auto 36px; text-align: center; color: #334155; }
    .mv { display: flex; justify-content: center; flex-wrap: wrap; gap: 28px; }
    .mv > div { flex: 1 1 300px; background: #FBFCFE; padding: 28px; border-radius: var(--border-radius); border: 1px solid rgba(0,0,0,0.06); box-shadow: 0 6px 18px rgba(0,0,0,.06); }
    .mv h4 { color: #2B2F77; margin: 0 0 10px; font-size: 1.25rem; }

    /* --- Footer --- */
    footer { background: #1C1F3B; color: var(--light-text-color); padding: 60px 0 20px; text-align: center; position: relative; overflow: hidden; }
    footer::before { content: ''; position: absolute; inset: -20% -10% auto -10%; height: 200px; background: radial-gradient(600px 200px at 50% 0%, rgba(108,92,231,.35), transparent); pointer-events: none; }
    .footer-grid { display: flex; flex-wrap: wrap; gap: 40px; justify-content: space-around; margin-bottom: 38px; }
    .footer-grid > div { flex: 1 1 220px; }
    footer h4 { font-weight: 700; margin: 0 0 12px; color: #fff; }
    footer ul { list-style: none; padding-left: 0; margin: 0; }
    footer li { margin-bottom: 8px; }
    footer a { color: rgba(255, 255, 255, 0.9); text-decoration: none; transition: opacity .2s ease; }
    footer a:hover { opacity: .9; text-decoration: underline; }
    .footer-socials { display: flex; justify-content: center; gap: 14px; }
    .footer-socials a { font-size: 1rem; background: rgba(255,255,255,.1); padding: 8px 10px; border-radius: 8px; }
    .footer-bottom { padding-top: 26px; border-top: 1px solid rgba(255, 255, 255, 0.2); font-size: .92em; color: rgba(255, 255, 255, 0.85); }

    /* --- Profile dropdown (hover & click friendly) --- */
    .profile-dropdown { position: relative; display: inline-block; }
    .profile-icon { font-size: 1.45rem; cursor: pointer; padding: 4px 6px; border-radius: 8px; }
    .profile-icon:focus { outline: none; box-shadow: 0 0 0 3px rgba(0,194,255,.3); }
    .profile-icon::after { display: none !important; }

    .dropdown-content {
        display: none; position: absolute; right: 0; top: calc(100% + 10px);
        background: var(--surface-color); min-width: 220px; box-shadow: var(--shadow);
        border-radius: 12px; padding: 18px; z-index: 1001; text-align: center; border: 1px solid rgba(0,0,0,.05);
        transform-origin: top right; transform: scale(.98) translateY(-6px);
        transition: transform .18s ease, opacity .18s ease; opacity: 0;
    }
    .profile-dropdown:hover .dropdown-content, .profile-dropdown.open .dropdown-content { display: block; opacity: 1; transform: scale(1) translateY(0); }

    .dropdown-content span { display: block; font-size: 1.05rem; font-weight: 700; color: #2B2F77; margin-bottom: 12px; white-space: nowrap; }

    .dropdown-content a { display: block; width: auto; padding: 10px 14px; margin-bottom: 8px; border-radius: 10px; text-decoration: none; font-weight: 600; transition: transform .2s ease, background-color .2s ease, color .2s ease; color: var(--light-text-color) !important; }
    .dropdown-content a::after { display: none; }

    .profile-button { background: var(--secondary-color); }
    .profile-button:hover { background: #08a8e6; transform: translateY(-1px); }

    .logout-button { background: #e74c3c; }
    .logout-button:hover { background: #c0392b; transform: translateY(-1px); }

    /* --- Carousel touches --- */
    .carousel-container { position: relative; border-radius: 22px; overflow: hidden; box-shadow: var(--shadow); margin-top: 26px; }
    .carousel-arrow { position: absolute; top: 50%; transform: translateY(-50%); width: 44px; height: 44px; border: 0; border-radius: 999px; display: grid; place-items: center; background: rgba(255,255,255,0.85); box-shadow: 0 6px 18px rgba(0,0,0,.12); cursor: pointer; transition: transform .15s ease, background-color .15s ease; }
    .carousel-arrow:hover { transform: translateY(-50%) scale(1.06); background: #fff; }
    .carousel-arrow.prev { left: 12px; }
    .carousel-arrow.next { right: 12px; }

    .carousel-nav { position: absolute; bottom: 14px; left: 50%; transform: translateX(-50%); display: flex; gap: 10px; }
    .carousel-dot { width: 10px; height: 10px; border-radius: 50%; background: rgba(255,255,255,.65); border: 2px solid rgba(0,0,0,.1); transition: transform .2s ease, background-color .2s ease; }
    .carousel-dot.active, .carousel-dot:hover { background: #fff; transform: scale(1.12); }

    .carousel-content { position: absolute; inset: 0; display: grid; place-content: center; text-align: center; padding: 22px; }

    /* --- Reveal on scroll --- */
    .reveal { opacity: 0; transform: translateY(24px); will-change: opacity, transform; }
    .reveal.show { opacity: 1; transform: translateY(0); transition: opacity .7s ease, transform .7s ease; }

    /* --- A11y: reduce motion --- */
    @media (prefers-reduced-motion: reduce) {
        .hero::before, .hero::after, .cta-button:hover::before { animation: none !important; }
        .cta-button, .card, .carousel-arrow { transition: none !important; }
    }

    /* --- Keyframes --- */
    @keyframes floaty { 0%,100% { transform: translate(0,0) scale(1); } 50% { transform: translate(0,-12px) scale(1.02); } }
    @keyframes shine { to { transform: translateX(120%); } }
    /* Toast animé pour confirmation candidature */
    .toast-pop {
        position: fixed; top: 20px; right: 20px; z-index: 2000;
        background: #10b981; color: #fff; padding: 14px 18px; border-radius: 12px;
        box-shadow: 0 10px 30px rgba(16,185,129,.35); font-weight: 700;
        transform: translateY(-12px); opacity: 0;
        display: flex; gap: 10px; align-items: center;
        animation: toastIn .35s ease forwards, toastStay 2.6s linear 0.35s forwards, toastOut .35s ease 3s forwards;
    }
    .toast-pop svg { width: 20px; height: 20px; }
    @keyframes toastIn { from { opacity: 0; transform: translateY(-12px) scale(.98); } to { opacity:1; transform: translateY(0) scale(1); } }
    @keyframes toastOut { to { opacity: 0; transform: translateY(-12px) scale(.98); } }
    @keyframes toastStay { to { opacity: 1; } }
</style>
</head>
<body>

<header>
    <div class="container">
        <a  class="logo">École Sup.</a>
        <nav>
            <ul>
                <li><a class="active" href="index.php">Accueil</a></li>
                <li><a href="vue/formations.php">Formations</a></li>
                <li><a href="vue/entreprise.php">Entreprises</a></li>
                <li><a href="vue/offres.php">Offres</a></li>
                <li><a href="vue/evenement.php">Evenement</a></li>
                <li><a href="vue/supportContact.php">Contact</a></li>

                <?php if ($userLoggedIn): ?>

                    <li><a href="vue/forum.php">Forum</a></li>

                    <?php if ($isEntreprise): ?>
                        <li><a href="vue/candidatures.php">Candidatures</a></li>
                    <?php endif; ?>

                    <?php if ($isAdmin): ?>
                        <li><a href="vue/admin.php">Admin</a></li>
                    <?php endif; ?>

                    <li class="profile-dropdown">
                        <a href="vue/profilUser.php" class="profile-icon">👤</a>
                        <div class="dropdown-content">
                            <span>Bonjour, <?= htmlspecialchars($userLoggedIn->getPrenom()) ?> !</span>
                            <a href="vue/profilUser.php" class="profile-button">Mon Profil</a>
                            <a href="?deco=true" class="logout-button">Déconnexion</a>
                        </div>
                    </li>
                <?php else: ?>
                    <li><a href="vue/connexion.php">Connexion</a></li>
                    <li><a href="vue/inscription.php">Inscription</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </div>
</header>

<div class="container">
    <!-- Hero Section with Carousel -->
    <div class="carousel-container">
        <!-- Flèche précédente -->
        <div class="carousel-slide-wrapper">
            <div class="carousel-slide">
                <img src="assets/img/entree.jpeg" >
                <img src="assets/img/WhatsApp-Image-2022-11-25-at-14.55.33-7.jpeg">
                <img src="assets/img/3EME-PREPAPRO.jpeg">
            </div>
        </div>

        <!-- Flèche précédente -->
        <button class="carousel-arrow prev" aria-label="Image précédente">
            &lt;
        </button>

        <!-- Flèche suivante -->
        <button class="carousel-arrow next" aria-label="Image suivante">
            &gt;
        </button>

        <div class="carousel-content">
            <h2>Construire les mondes de demain</h2>
            <p>Formation. Recherche. Innovation.</p>
            <a href="vue/formations.php" class="cta-button">Découvrir nos formations</a>
        </div>

        <div class="carousel-nav">
            <div class="carousel-dot active" data-slide="0"></div>
            <div class="carousel-dot" data-slide="1"></div>
            <div class="carousel-dot" data-slide="2"></div>
        </div>
    </div>

    <section class="actus-events">
        <div class="card actus">
            <h3>Actualités</h3>
            <ul>
                <?php foreach($dernieresActualites as $actualite): ?>
                    <li><a href="#"><?= htmlspecialchars($actualite->getContexte()) ?></a></li>
                <?php endforeach; ?>
                <?php if (empty($dernieresActualites)): ?>
                    <li>Aucune actualité pour le moment</li>
                <?php endif; ?>
            </ul>
            <?php if ($isAdmin): ?>
                <div class="more-wrapper">
                    <a class="btn-more" href="vue/admin.php?section=actualites">Gérer les actualités</a>
                </div>
            <?php endif; ?>
        </div>
        <div class="card events">
            <h3>Événements à venir</h3>
            <ul>
                <?php foreach($latestEvents as $event): ?>
                    <li><?= htmlspecialchars($event->getTitre()) ?> — <?= date('d/m/Y H:i', strtotime($event->getDateEvent())) ?></li>
                <?php endforeach; ?>
            </ul>
            <div class="more-wrapper">
                <a class="btn-more" href="vue/evenement.php">Voir plus</a>
            </div>
        </div>
    </section>
</div>

<section class="presentation">
    <div class="container">
        <h2 class="section-title">Notre École</h2>
        <div class="presentation-content">
            <p>Nous formons les ingénieurs, chercheurs et innovateurs de demain, prêts à relever les défis technologiques, sociaux et environnementaux d'un monde en pleine mutation.</p>
        </div>
        <div class="mv">
            <div class="mission">
                <h4>Notre mission</h4>
                <p>Former des experts engagés, dotés d'un esprit critique et d'une forte capacité d'adaptation pour innover de manière responsable.</p>
            </div>
            <div class="vision">
                <h4>Notre vision</h4>
                <p>Être un pôle d'excellence reconnu à l’international, où la recherche et la pédagogie se conjuguent pour construire un avenir durable.</p>
            </div>
            <div class="valeurs">
                <h4>Nos valeurs</h4>
                <p>Innovation, ouverture, solidarité et respect de l'environnement guident chaque projet que nous entreprenons.</p>
            </div>
        </div>
    </div>
</section>

<footer>
    <div class="footer-grid container">
        <div>
            <h4>École Sup.</h4>
            <p>15 avenue du Général De Gaulle, Dugny</p>
            <p>Contact2jrs@gmail.com</p>
        </div>
        <div>
            <h4>Liens rapides</h4>
            <ul>
                <li><a href="index.php">Accueil</a></li>
                <li><a href="vue/formations.php">Formations</a></li>
                <li><a href="vue/supportContact.php">Contact</a></li>
            </ul>
        </div>
        <div>
            <h4>Réseaux sociaux</h4>
            <div class="footer-socials">
                <a href="#">FB</a>
                <a href="#">TW</a>
                <a href="#">IG</a>
            </div>
        </div>
    </div>
    <div class="footer-bottom">
        &copy; 2025 École Sup. Tous droits réservés.
    </div>
</footer>

    <script>
(function(){
  const prefersReduced = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;

  // Header shadow on scroll
  const header = document.querySelector('header');
  const onScroll = () => {
    if (!header) return;
    if (window.scrollY > 4) header.classList.add('scrolled');
    else header.classList.remove('scrolled');
  };
  window.addEventListener('scroll', onScroll, { passive: true });
  onScroll();

  // Reveal on scroll
  const toReveal = [];
  document.querySelectorAll('.card, .section-title, .mv > div').forEach(el => {
    el.classList.add('reveal');
    toReveal.push(el);
  });
  if (!prefersReduced && 'IntersectionObserver' in window) {
    const io = new IntersectionObserver((entries)=>{
      entries.forEach(entry=>{
        if (entry.isIntersecting) {
          entry.target.classList.add('show');
          io.unobserve(entry.target);
        }
      });
    }, { threshold: 0.15 });
    toReveal.forEach(el=> io.observe(el));
  } else {
    toReveal.forEach(el=> el.classList.add('show'));
  }

  // Subtle parallax for hero text
  const hero = document.querySelector('.hero');
  const heroContent = document.querySelector('.hero-content');
  if (hero && heroContent && !prefersReduced) {
    hero.addEventListener('mousemove', (e)=>{
      const r = hero.getBoundingClientRect();
      const x = (e.clientX - r.left) / r.width - 0.5;
      const y = (e.clientY - r.top) / r.height - 0.5;
      heroContent.style.transform = `translate3d(${x * 12}px, ${y * 12}px, 0)`;
    });
    hero.addEventListener('mouseleave', ()=>{
      heroContent.style.transform = 'translate3d(0,0,0)';
    });
  }

  // Keyboard support for carousel arrows if present
  const prevBtn = document.querySelector('.carousel-arrow.prev');
  const nextBtn = document.querySelector('.carousel-arrow.next');
  document.addEventListener('keydown', (e)=>{
    if (e.key === 'ArrowLeft' && prevBtn) prevBtn.click();
    if (e.key === 'ArrowRight' && nextBtn) nextBtn.click();
  });

  // Click-to-open dropdown (works alongside :hover)
  const dropdown = document.querySelector('.profile-dropdown');
  const icon = dropdown ? dropdown.querySelector('.profile-icon') : null;
  if (dropdown && icon) {
    icon.addEventListener('click', (e)=>{
      e.preventDefault();
      dropdown.classList.toggle('open');
    });
    window.addEventListener('click', (e)=>{
      if (!dropdown.contains(e.target)) dropdown.classList.remove('open');
    });
  }

  // Toast de remerciement après candidature (?applied=1)
  try {
    const params = new URLSearchParams(window.location.search);
    if (params.get('applied') === '1') {
      const toast = document.createElement('div');
      toast.className = 'toast-pop';
      toast.innerHTML = `
        <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M12 22C6.477 22 2 17.523 2 12 2 6.477 6.477 2 12 2s10 4.477 10 10-4.477 10-10 10Z" fill="rgba(255,255,255,.15)"/><path d="M10.2 14.8 7.7 12.3a1 1 0 1 0-1.4 1.4l3.2 3.2a1 1 0 0 0 1.414 0l6.6-6.6a1 1 0 1 0-1.414-1.414l-5.9 5.9Z" fill="#fff"/></svg>
        <span>Merci pour votre candidature !</span>
      `;
      document.body.appendChild(toast);
      // Retirer le paramètre de l'URL sans recharger
      params.delete('applied');
      const url = window.location.pathname + (params.toString() ? `?${params}` : '') + window.location.hash;
      window.history.replaceState({}, '', url);
      // Nettoyage DOM après l’animation
      setTimeout(()=>{ toast.remove(); }, 3450);
    }
  } catch(_) { /* no-op */ }
})();
</script>
    <!-- Carousel JavaScript -->
    <script src="assets/js/carousel.js"></script>
</body>
</html>