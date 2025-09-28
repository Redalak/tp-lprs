<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Admin - École Sup.</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        /* Définition de la palette de couleurs et des variables globales de la page principale */
        :root {
            --primary-color: #0A4D68; /* Bleu profond */
            --secondary-color: #088395; /* Turquoise */
            --accent-color: #F39C12; /* Accent orange/jaune (optionnel) */
            --background-color: #f8f9fa; /* Fond clair de la page principale */
            --surface-color: #ffffff;
            --text-color: #343a40;
            --light-text-color: #f8f9fa;
            --shadow: 0 4px 15px rgba(0, 0, 0, 0.07);
            --border-radius: 8px;
        }

        /* Reset & base */
        * {
            box-sizing: border-box;
        }
        body {
            margin: 0;
            /* Utilisation de Poppins */
            font-family: 'Poppins', sans-serif;
            background: var(--background-color);
            color: var(--text-color);
        }

        /* --- HEADER --- */
        header {
            /* Couleur primaire de la page principale */
            background-color: var(--primary-color);
            color: var(--light-text-color);
            padding: 20px 40px;
            font-size: 1.5em;
            font-weight: 700; /* Utilisation d'un poids de police de Poppins */
            text-align: center;
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        /* Layout */
        .container {
            max-width: 1200px;
            margin: 40px auto;
            padding: 0 20px;
        }

        h1 {
            /* Couleur primaire pour les titres importants */
            color: var(--primary-color);
            margin-bottom: 30px;
            text-align: center;
            font-size: 2.5rem;
            font-weight: 600;
        }

        h2 {
            color: var(--secondary-color);
            margin-top: 0;
            margin-bottom: 25px;
            font-weight: 600;
            border-bottom: 2px solid var(--secondary-color);
            padding-bottom: 8px;
            display: inline-block;
        }

        /* Nav tabs */
        nav.tabs {
            display: flex;
            justify-content: center;
            gap: 15px;
            margin-bottom: 40px;
            flex-wrap: wrap;
        }

        nav.tabs button {
            background: var(--surface-color);
            border: 1px solid var(--primary-color);
            padding: 10px 20px;
            font-size: 0.95em;
            border-radius: var(--border-radius);
            cursor: pointer;
            transition: all 0.3s;
            font-weight: 500;
            color: var(--primary-color);
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        }

        nav.tabs button.active,
        nav.tabs button:hover {
            /* Couleurs inversées ou accentuées */
            background: var(--primary-color);
            color: var(--light-text-color);
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }

        nav.tabs button:hover:not(.active) {
            background: var(--secondary-color);
            border-color: var(--secondary-color);
        }

        /* Sections */
        section.admin-section {
            display: none;
            background: var(--surface-color);
            padding: 30px;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
        }
        section.admin-section.active {
            display: block;
        }

        /* Tables */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        table thead {
            background-color: var(--secondary-color); /* Utilisation de la couleur secondaire pour le header du tableau */
            color: white;
            font-weight: 600;
        }
        table th, table td {
            padding: 12px 15px;
            border: 1px solid #dee2e6;
            text-align: left;
        }
        table tbody tr:nth-child(even) {
            background-color: #f6f7f9; /* Légèrement plus clair que le fond */
        }

        /* Buttons inside tables (styles de la page principale pour les CTA/boutons) */
        .btn {
            padding: 6px 12px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 0.9em;
            transition: background 0.3s, transform 0.2s;
            color: white;
            font-weight: 500;
        }

        .btn.edit {
            background-color: var(--secondary-color); /* Bleu turquoise */
        }
        .btn.edit:hover {
            background-color: #066d7c;
            transform: translateY(-1px);
        }

        .btn.delete {
            background-color: #dc3545; /* Rouge standard pour danger */
        }
        .btn.delete:hover {
            background-color: #a71d2a;
            transform: translateY(-1px);
        }

        /* Form styling */
        form.admin-form {
            margin-top: 40px;
            max-width: 600px;
            padding: 20px;
            border: 1px solid #e9ecef;
            border-radius: var(--border-radius);
            background: #fcfcfc;
        }
        form.admin-form h3 {
            color: var(--primary-color);
            margin-top: 0;
            border-bottom: 1px solid #dee2e6;
            padding-bottom: 10px;
        }
        form.admin-form label {
            display: block;
            margin-bottom: 6px;
            font-weight: 500;
            margin-top: 15px;
            color: var(--text-color);
        }
        form.admin-form input,
        form.admin-form textarea,
        form.admin-form select {
            width: 100%;
            padding: 10px;
            border-radius: 4px;
            border: 1px solid #ced4da;
            font-size: 1em;
            font-family: 'Poppins', sans-serif;
            transition: border-color 0.3s, box-shadow 0.3s;
        }
        form.admin-form input:focus,
        form.admin-form textarea:focus,
        form.admin-form select:focus {
            border-color: var(--secondary-color);
            box-shadow: 0 0 0 0.2rem rgba(8, 131, 149, 0.25);
            outline: none;
        }
        form.admin-form textarea {
            resize: vertical;
            min-height: 80px;
        }
        form.admin-form button.submit-btn {
            margin-top: 25px;
            background-color: var(--primary-color);
            color: white;
            padding: 12px 20px;
            font-weight: 600;
            border: none;
            border-radius: 50px; /* Bord arrondi style CTA de la page principale */
            cursor: pointer;
            transition: all 0.3s;
            box-shadow: 0 4px 10px rgba(10, 77, 104, 0.2);
        }
        form.admin-form button.submit-btn:hover {
            background-color: var(--secondary-color);
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(8, 131, 149, 0.2);
        }

        /* Responsive */
        @media(max-width: 700px) {
            nav.tabs {
                gap: 10px;
                padding: 0 10px;
            }
            nav.tabs button {
                flex-grow: 1;
                font-size: 0.9em;
            }
            form.admin-form {
                width: 100%;
            }
            table th, table td {
                font-size: 0.85em;
                padding: 10px 8px;
            }
        }
    </style>
</head>
<body>

<header>Admin - École Supérieure</header>

<div class="container">

    <h1>Tableau de bord de l'Administration</h1>

    <nav class="tabs" role="tablist">
        <button class="tab-btn active" data-target="users" role="tab" aria-selected="true">Utilisateurs</button>
        <button class="tab-btn" data-target="jobs" role="tab" aria-selected="false">Offres d'emploi / stage</button>
        <button class="tab-btn" data-target="events" role="tab" aria-selected="false">Événements</button>
    </nav>

    <section id="users" class="admin-section active" role="tabpanel">
        <h2>Gestion des utilisateurs</h2>
        <table>
            <thead>
            <tr>
                <th>ID</th><th>Nom</th><th>Email</th><th>Rôle</th><th>Actions</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td>1</td><td>Dupont Jean</td><td>jean.dupont@email.com</td><td>Administrateur</td>
                <td>
                    <button class="btn edit">Modifier</button>
                    <button class="btn delete">Supprimer</button>
                </td>
            </tr>
            <tr>
                <td>2</td><td>Martin Claire</td><td>claire.martin@email.com</td><td>Utilisateur</td>
                <td>
                    <button class="btn edit">Modifier</button>
                    <button class="btn delete">Supprimer</button>
                </td>
            </tr>
            </tbody>
        </table>
        <form class="admin-form" aria-label="Ajouter un utilisateur">
            <h3>Ajouter un utilisateur</h3>
            <label for="user-name">Nom complet</label>
            <input type="text" id="user-name" name="user-name" required />

            <label for="user-email">Email</label>
            <input type="email" id="user-email" name="user-email" required />

            <label for="user-role">Rôle</label>
            <select id="user-role" name="user-role" required>
                <option value="">-- Sélectionnez un rôle --</option>
                <option value="admin">Administrateur</option>
                <option value="user">Utilisateur</option>
            </select>

            <button type="submit" class="submit-btn">Ajouter</button>
        </form>
    </section>



    <section id="jobs" class="admin-section" role="tabpanel" aria-hidden="true">
        <h2>Gestion des offres d'emploi / stage</h2>
        <table>
            <thead>
            <tr>
                <th>ID</th><th>Titre</th><th>Type</th><th>Date de publication</th><th>Actions</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td>201</td><td>Développeur Web</td><td>Emploi</td><td>10/09/2025</td>
                <td>
                    <button class="btn edit">Modifier</button>
                    <button class="btn delete">Supprimer</button>
                </td>
            </tr>
            <tr>
                <td>202</td><td>Stage Marketing</td><td>Stage</td><td>05/09/2025</td>
                <td>
                    <button class="btn edit">Modifier</button>
                    <button class="btn delete">Supprimer</button>
                </td>
            </tr>
            </tbody>
        </table>
        <form class="admin-form" aria-label="Ajouter une offre">
            <h3>Ajouter une offre</h3>
            <label for="job-title">Titre de l'offre</label>
            <input type="text" id="job-title" name="job-title" required />

            <label for="job-type">Type</label>
            <select id="job-type" name="job-type" required>
                <option value="">-- Sélectionnez un type --</option>
                <option value="emploi">Emploi</option>
                <option value="stage">Stage</option>
            </select>

            <label for="job-description">Description</label>
            <textarea id="job-description" name="job-description" required></textarea>

            <button type="submit" class="submit-btn">Ajouter</button>
        </form>
    </section>

    <section id="events" class="admin-section" role="tabpanel" aria-hidden="true">
        <h2>Gestion des événements</h2>
        <table>
            <thead>
            <tr>
                <th>ID</th><th>Titre</th><th>Date</th><th>Lieu</th><th>Actions</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td>301</td><td>Journée Portes Ouvertes</td><td>25/09/2025</td><td>Campus Principal</td>
                <td>
                    <button class="btn edit">Modifier</button>
                    <button class="btn delete">Supprimer</button>
                </td>
            </tr>
            <tr>
                <td>302</td><td>Conférence Tech</td><td>10/10/2025</td><td>Auditorium</td>
                <td>
                    <button class="btn edit">Modifier</button>
                    <button class="btn delete">Supprimer</button>
                </td>
            </tr>
            </tbody>
        </table>
        <form class="admin-form" aria-label="Ajouter un événement">
            <h3>Ajouter un événement</h3>
            <label for="event-title">Titre</label>
            <input type="text" id="event-title" name="event-title" required />

            <label for="event-date">Date</label>
            <input type="date" id="event-date" name="event-date" required />

            <label for="event-location">Lieu</label>
            <input type="text" id="event-location" name="event-location" required />

            <label for="event-description">Description</label>
            <textarea id="event-description" name="event-description" required></textarea>

            <button type="submit" class="submit-btn">Ajouter</button>
        </form>
    </section>

</div>

<script>
    // Gestion des onglets
    const tabs = document.querySelectorAll('.tab-btn');
    const sections = document.querySelectorAll('.admin-section');

    tabs.forEach(tab => {
        tab.addEventListener('click', () => {
            // Décocher tous les onglets
            tabs.forEach(t => {
                t.classList.remove('active');
                t.setAttribute('aria-selected', 'false');
            });
            // Cacher toutes les sections
            sections.forEach(section => {
                section.classList.remove('active');
                section.setAttribute('aria-hidden', 'true');
            });

            // Activer l'onglet cliqué
            tab.classList.add('active');
            tab.setAttribute('aria-selected', 'true');

            // Afficher la section correspondante
            const target = tab.getAttribute('data-target');
            const activeSection = document.getElementById(target);
            activeSection.classList.add('active');
            activeSection.setAttribute('aria-hidden', 'false');
        });
    });
</script>

</body>
</html>