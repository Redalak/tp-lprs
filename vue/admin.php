<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Admin - École Exemple</title>
    <style>
        /* Reset & base */
        * {
            box-sizing: border-box;
        }
        body {
            margin: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f4f6f8;
            color: #333;
        }
        header {
            background-color: #005baa;
            color: white;
            padding: 20px 40px;
            font-size: 1.5em;
            font-weight: bold;
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
            color: #003a70;
            margin-bottom: 30px;
            text-align: center;
        }

        /* Nav tabs */
        nav.tabs {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin-bottom: 40px;
            flex-wrap: wrap;
        }

        nav.tabs button {
            background: #e4e9f2;
            border: none;
            padding: 12px 25px;
            font-size: 1em;
            border-radius: 6px;
            cursor: pointer;
            transition: background 0.3s;
            font-weight: 600;
            color: #005baa;
        }

        nav.tabs button.active,
        nav.tabs button:hover {
            background: #005baa;
            color: white;
        }

        /* Sections */
        section.admin-section {
            display: none;
            background: white;
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.1);
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
            background-color: #005baa;
            color: white;
        }
        table th, table td {
            padding: 12px 15px;
            border: 1px solid #ddd;
            text-align: left;
        }
        table tbody tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        /* Buttons inside tables */
        .btn {
            padding: 6px 12px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 0.9em;
            transition: background 0.3s;
            color: white;
        }

        .btn.edit {
            background-color: #007bff;
        }
        .btn.edit:hover {
            background-color: #0056b3;
        }

        .btn.delete {
            background-color: #dc3545;
        }
        .btn.delete:hover {
            background-color: #a71d2a;
        }

        /* Form styling */
        form.admin-form {
            margin-top: 20px;
            max-width: 600px;
        }
        form.admin-form label {
            display: block;
            margin-bottom: 6px;
            font-weight: 600;
            margin-top: 15px;
        }
        form.admin-form input,
        form.admin-form textarea,
        form.admin-form select {
            width: 100%;
            padding: 10px;
            border-radius: 6px;
            border: 1px solid #ccc;
            font-size: 1em;
            font-family: inherit;
        }
        form.admin-form textarea {
            resize: vertical;
            min-height: 80px;
        }
        form.admin-form button.submit-btn {
            margin-top: 20px;
            background-color: #005baa;
            color: white;
            padding: 12px 20px;
            font-weight: 600;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            transition: background 0.3s;
        }
        form.admin-form button.submit-btn:hover {
            background-color: #003a70;
        }

        /* Responsive */
        @media(max-width: 700px) {
            nav.tabs {
                flex-direction: column;
                align-items: center;
            }
            form.admin-form {
                width: 100%;
            }
            table th, table td {
                font-size: 0.9em;
            }
        }
    </style>
</head>
<body>

<header>Admin - École Exemple</header>

<div class="container">

    <h1>Tableau de bord</h1>

    <nav class="tabs" role="tablist">
        <button class="tab-btn active" data-target="users" role="tab" aria-selected="true">Utilisateurs</button>
        <button class="tab-btn" data-target="contents" role="tab" aria-selected="false">Contenus</button>
        <button class="tab-btn" data-target="jobs" role="tab" aria-selected="false">Offres d'emploi / stage</button>
        <button class="tab-btn" data-target="events" role="tab" aria-selected="false">Événements</button>
    </nav>

    <!-- Utilisateurs -->
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

    <!-- Contenus -->
    <section id="contents" class="admin-section" role="tabpanel" aria-hidden="true">
        <h2>Gestion des contenus</h2>
        <table>
            <thead>
            <tr>
                <th>ID</th><th>Titre</th><th>Type</th><th>Publié le</th><th>Actions</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td>101</td><td>Présentation de l'école</td><td>Page</td><td>15/08/2025</td>
                <td>
                    <button class="btn edit">Modifier</button>
                    <button class="btn delete">Supprimer</button>
                </td>
            </tr>
            <tr>
                <td>102</td><td>Article actualités</td><td>Article</td><td>20/08/2025</td>
                <td>
                    <button class="btn edit">Modifier</button>
                    <button class="btn delete">Supprimer</button>
                </td>
            </tr>
            </tbody>
        </table>
        <form class="admin-form" aria-label="Ajouter un contenu">
            <h3>Ajouter un contenu</h3>
            <label for="content-title">Titre</label>
            <input type="text" id="content-title" name="content-title" required />

            <label for="content-type">Type</label>
            <select id="content-type" name="content-type" required>
                <option value="">-- Sélectionnez un type --</option>
                <option value="page">Page</option>
                <option value="article">Article</option>
                <option value="blog">Blog</option>
            </select>

            <label for="content-body">Contenu</label>
            <textarea id="content-body" name="content-body" required></textarea>

            <button type="submit" class="submit-btn">Ajouter</button>
        </form>
    </section>

    <!-- Offres d'emploi / stage -->
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

    <!-- Événements -->
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
