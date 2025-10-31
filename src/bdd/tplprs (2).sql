-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le : ven. 31 oct. 2025 à 10:17
-- Version du serveur : 8.3.0
-- Version de PHP : 8.2.18

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `tplprs`
--

-- --------------------------------------------------------

--
-- Structure de la table `alumni`
--

DROP TABLE IF EXISTS `alumni`;
CREATE TABLE IF NOT EXISTS `alumni` (
  `ref_user` int UNSIGNED NOT NULL,
  `emploi_actuel` varchar(150) DEFAULT NULL,
  `ref_entreprise` varchar(150) DEFAULT NULL,
  PRIMARY KEY (`ref_user`),
  KEY `ref_user` (`ref_user`),
  KEY `ref_entreprise` (`ref_entreprise`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Structure de la table `candidature`
--

DROP TABLE IF EXISTS `candidature`;
CREATE TABLE IF NOT EXISTS `candidature` (
  `id_candidature` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `motivation` text,
  `cv` varchar(255) DEFAULT NULL,
  `date_candidature` date NOT NULL,
  `ref_offre` int UNSIGNED NOT NULL,
  `ref_user` int UNSIGNED NOT NULL,
  PRIMARY KEY (`id_candidature`),
  KEY `ref_offre` (`ref_offre`),
  KEY `ref_user` (`ref_user`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Structure de la table `eleves`
--

DROP TABLE IF EXISTS `eleves`;
CREATE TABLE IF NOT EXISTS `eleves` (
  `ref_user` int UNSIGNED NOT NULL,
  `annee_promo` varchar(10) DEFAULT NULL,
  `date_inscription` date NOT NULL,
  `classe` enum('btsUn','btsDeux','terminale','premiere','seconde') NOT NULL,
  `ref_formation` int UNSIGNED NOT NULL,
  PRIMARY KEY (`ref_user`),
  KEY `ref_user` (`ref_user`),
  KEY `ref_formation` (`ref_formation`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Structure de la table `entreprise`
--

DROP TABLE IF EXISTS `entreprise`;
CREATE TABLE IF NOT EXISTS `entreprise` (
  `id_entreprise` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `nom` varchar(150) NOT NULL,
  `adresse` varchar(255) DEFAULT NULL,
  `site_web` varchar(190) DEFAULT NULL,
  `motif_partenariat` varchar(255) NOT NULL,
  `date_inscription` date NOT NULL,
  `ref_offre` int UNSIGNED DEFAULT NULL,
  PRIMARY KEY (`id_entreprise`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `entreprise`
--

INSERT INTO `entreprise` (`id_entreprise`, `nom`, `adresse`, `site_web`, `motif_partenariat`, `date_inscription`, `ref_offre`) VALUES
(2, 'dbzhbdj', 'cdc', 'https://app.dvf.etalab.gouv.fr/', 'cdc', '2025-10-11', 0),
(3, 'dddc', 'cdsdcds', 'http://localhost:63342/tp-lprs/vue/adminEntreprise.php?_ijt=m2f2022i1gictoq11n73p33i9a&_ij_reload=RELOAD_ON_SAVE', 'sdc', '2025-10-24', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `event`
--

DROP TABLE IF EXISTS `event`;
CREATE TABLE IF NOT EXISTS `event` (
  `id_evenement` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `type` varchar(100) DEFAULT NULL,
  `titre` varchar(200) NOT NULL,
  `description` text NOT NULL,
  `lieu` varchar(150) DEFAULT NULL,
  `nombre_place` int DEFAULT NULL,
  `date_creation` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `date_event` timestamp NOT NULL,
  `etat` enum('brouillon','publie','archive') DEFAULT 'publie',
  `ref_user` int UNSIGNED DEFAULT NULL,
  PRIMARY KEY (`id_evenement`),
  KEY `ref_user` (`ref_user`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `event`
--

INSERT INTO `event` (`id_evenement`, `type`, `titre`, `description`, `lieu`, `nombre_place`, `date_creation`, `date_event`, `etat`, `ref_user`) VALUES
(1, 'atelier', 'cdc', 'cscssc', 'xsssc', 162, '2025-10-30 14:16:17', '2025-10-17 16:16:00', '', NULL),
(2, 'atelier', 'cdc', 'cscssc', 'xsssc', 163, '2025-10-30 14:16:22', '2025-10-17 16:16:00', '', NULL),
(3, 'rthte', 'ggreg', 'fergreg', 'gfgfgfg', 0, '2025-10-31 10:15:48', '2025-10-31 10:15:48', 'publie', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `formation`
--

DROP TABLE IF EXISTS `formation`;
CREATE TABLE IF NOT EXISTS `formation` (
  `id_formation` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `nom_formation` varchar(150) NOT NULL,
  PRIMARY KEY (`id_formation`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Structure de la table `inscription_evenement`
--

DROP TABLE IF EXISTS `inscription_evenement`;
CREATE TABLE IF NOT EXISTS `inscription_evenement` (
  `id_inscription` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `ref_user` int UNSIGNED NOT NULL,
  `ref_evenement` int UNSIGNED NOT NULL,
  `date_inscription` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_inscription`),
  UNIQUE KEY `unique_user_event` (`ref_user`,`ref_evenement`),
  KEY `fk_inscription_event` (`ref_evenement`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `inscription_evenement`
--

INSERT INTO `inscription_evenement` (`id_inscription`, `ref_user`, `ref_evenement`, `date_inscription`) VALUES
(1, 1, 1, '2025-10-31 10:38:10'),
(2, 1, 3, '2025-10-31 11:16:17');

-- --------------------------------------------------------

--
-- Structure de la table `inscrire`
--

DROP TABLE IF EXISTS `inscrire`;
CREATE TABLE IF NOT EXISTS `inscrire` (
  `re_evenement` int UNSIGNED NOT NULL,
  `ref_user` int UNSIGNED NOT NULL,
  KEY `re_evenement` (`re_evenement`),
  KEY `ref_user` (`ref_user`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Structure de la table `offre`
--

DROP TABLE IF EXISTS `offre`;
CREATE TABLE IF NOT EXISTS `offre` (
  `id_offre` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `titre` varchar(50) NOT NULL,
  `rue` varchar(50) NOT NULL,
  `cp` int NOT NULL,
  `ville` varchar(50) NOT NULL,
  `description` varchar(255) NOT NULL,
  `salaire` decimal(10,2) DEFAULT NULL,
  `type_offre` enum('CDI','CDD','Stage','Alternance','Autre') DEFAULT NULL,
  `date_creation` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `etat` enum('ouvert','ferme','brouillon') DEFAULT 'ouvert',
  `ref_entreprise` int UNSIGNED DEFAULT NULL,
  PRIMARY KEY (`id_offre`),
  KEY `ref_entreprise` (`ref_entreprise`)
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `offre`
--

INSERT INTO `offre` (`id_offre`, `titre`, `rue`, `cp`, `ville`, `description`, `salaire`, `type_offre`, `date_creation`, `etat`, `ref_entreprise`) VALUES
(3, 'test', '8 rue d’Hérivaux', 95400, 'Villiers le bel', 'dcdc', 1500.00, '', '2025-10-28 04:33:11', '', NULL),
(4, 'test', '8 rue d’Hérivaux', 95400, 'Villiers le bel', 'dcd', 1500.00, 'Stage', '2025-10-28 04:38:51', '', NULL),
(5, 'cxccc', '8 rue d’Hérivaux', 95400, 'Villiers le bel', 'xdcx', NULL, 'Alternance', '2025-10-28 04:39:22', '', NULL),
(6, 'test', '8 rue d’Hérivaux', 95400, 'Villiers le bel', 'dcd', 1500.00, '', '2025-10-28 04:39:56', 'brouillon', NULL),
(7, 'test', '8 rue d’Hérivaux', 95400, 'Villiers le bel', 'cc', 1500.00, 'Alternance', '2025-10-28 04:40:27', '', NULL),
(8, 'test', '8 rue d’Hérivauxs', 95400, 'Villiers le bel', 'sds', 1500.00, 'Alternance', '2025-10-28 04:47:00', '', NULL),
(9, 'test', '8 rue d’Hérivaux', 95400, 'Villiers le bel', 'sxs', 1500.00, 'Alternance', '2025-10-28 04:47:19', '', NULL),
(10, 'test', '8 rue d’Hérivaux', 95400, 'Villiers le bel', 'cdds', 1500.00, 'Alternance', '2025-10-28 05:00:55', '', NULL),
(11, 'test', '8 rue d’Hérivaux', 95400, 'Villiers le bel', ' x c cc c', 1500.00, '', '2025-10-28 05:01:16', '', NULL),
(12, 'vffe', 'fgfg', 0, 'fegg', 'ffef', 1800.00, '', '2025-10-28 05:01:48', 'brouillon', NULL),
(13, 'cxccc', '8 rue d’Hérivaux', 95400, 'Villiers le bel', 'cvvvvf', 1700.00, 'Alternance', '2025-10-28 05:02:58', '', NULL),
(14, 'vfv', 'vff', 0, 'vfv', 'rfvfr', 1900.00, '', '2025-10-28 05:03:51', '', NULL),
(15, 'cxccc', '8 rue d’Hérivaux', 95400, 'Villiers le bel', 'cdv', 1700.00, '', '2025-10-28 05:18:45', '', NULL),
(16, 'test', '8 rue d’Hérivaux', 95400, 'Villiers le bel', 'dccd', 1800.00, 'Alternance', '2025-10-28 05:19:03', '', NULL),
(18, 'cxccc', '8 rue d’Hérivau', 95400, 'Villiers le bel', 'x c ', 1700.00, 'CDI', '2025-10-28 05:22:15', '', NULL),
(19, 'test', '8 rue d’Hérivaux', 95400, 'Villiers le bel', 'cdv', 1700.00, '', '2025-10-28 05:26:39', '', NULL),
(20, 'test', '8 rue d’Hérivaux', 95400, 'Villiers le bel', 'scdcc', 1800.00, NULL, '2025-10-28 05:30:02', '', NULL),
(21, 'cxccc', '8 rue d’Hérivaux', 95400, 'Villiers le bel', 'sxc', 19900.00, NULL, '2025-10-28 05:31:23', '', NULL),
(23, 'n,,n', ',kn,', 95500, 'dede', 'nnjh', 7878.00, 'CDD', '2025-10-30 14:20:12', 'ferme', NULL),
(24, 'efddv', ',kn,', 0, 'fef', 'cdc', 12313.00, 'CDI', '2025-10-30 14:38:22', 'ferme', 2),
(25, 'eddeddedd', ',kn,', 0, 'fef', 'cvdfv', 12313.00, '', '2025-10-30 14:45:29', '', 2);

-- --------------------------------------------------------

--
-- Structure de la table `organise`
--

DROP TABLE IF EXISTS `organise`;
CREATE TABLE IF NOT EXISTS `organise` (
  `re_evenement` int UNSIGNED NOT NULL,
  `ref_user` int UNSIGNED NOT NULL,
  KEY `re_evenement` (`re_evenement`),
  KEY `ref_user` (`ref_user`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Structure de la table `partenaire`
--

DROP TABLE IF EXISTS `partenaire`;
CREATE TABLE IF NOT EXISTS `partenaire` (
  `ref_user` int NOT NULL,
  `promo` varchar(9) NOT NULL,
  `emploie_actuel` varchar(50) NOT NULL,
  `motif_partenariat` varchar(50) NOT NULL,
  `ref_entreprise` int NOT NULL,
  PRIMARY KEY (`ref_user`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Structure de la table `prof`
--

DROP TABLE IF EXISTS `prof`;
CREATE TABLE IF NOT EXISTS `prof` (
  `ref_user` int UNSIGNED NOT NULL,
  `matiere` varchar(15) DEFAULT NULL,
  `date_inscription` date NOT NULL,
  `ref_formation` int UNSIGNED NOT NULL,
  PRIMARY KEY (`ref_user`),
  UNIQUE KEY `ref_user` (`ref_user`),
  UNIQUE KEY `ref_formation` (`ref_formation`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Structure de la table `p_forum`
--

DROP TABLE IF EXISTS `p_forum`;
CREATE TABLE IF NOT EXISTS `p_forum` (
  `id_post` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `titre` varchar(200) NOT NULL,
  `contenue` text,
  `date_creation` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `ref_user` int UNSIGNED NOT NULL,
  PRIMARY KEY (`id_post`),
  KEY `ref_user` (`ref_user`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Structure de la table `r_forum`
--

DROP TABLE IF EXISTS `r_forum`;
CREATE TABLE IF NOT EXISTS `r_forum` (
  `id_reponse_forum` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `contenue` text NOT NULL,
  `date_creation` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `ref_post_forum` int UNSIGNED NOT NULL,
  `ref_user` int UNSIGNED NOT NULL,
  PRIMARY KEY (`id_reponse_forum`),
  KEY `ref_post_forum` (`ref_post_forum`),
  KEY `ref_user` (`ref_user`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Structure de la table `user`
--

DROP TABLE IF EXISTS `user`;
CREATE TABLE IF NOT EXISTS `user` (
  `id_user` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `nom` varchar(100) NOT NULL,
  `prenom` varchar(100) NOT NULL,
  `email` varchar(190) NOT NULL,
  `mdp` varchar(255) NOT NULL,
  `role` enum('admin','prof','alumni','entreprise','etudiant') DEFAULT 'etudiant',
  `ref_entreprise` int UNSIGNED DEFAULT NULL,
  `ref_formation` int UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_user`),
  UNIQUE KEY `email` (`email`),
  KEY `role` (`role`),
  KEY `fk_user_entreprise_idx` (`ref_entreprise`),
  KEY `fk_user_formation_idx` (`ref_formation`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `user`
--

INSERT INTO `user` (`id_user`, `nom`, `prenom`, `email`, `mdp`, `role`, `ref_entreprise`, `ref_formation`, `created_at`, `updated_at`) VALUES
(1, 'n', 'jb', '23@gmail.com', '$2y$10$5fxf4m3YW0pUJhLsUr0QfusC11VfbIyJLt4171YMbv7NBv0EAOQru', 'admin', NULL, NULL, '2025-10-28 03:48:47', '2025-10-30 14:14:48'),
(2, 'aaa', 'joe', 'isjjsjs@gmail.com', '$2y$10$H79FXwR55mY8o51SFSfCce6GB0RY6kvshPoyYjr7US8uJQO9et.Gm', 'etudiant', NULL, NULL, '2025-10-28 04:02:03', '2025-10-28 04:02:03'),
(3, 'aaa', 'jb', 'bzhakjsjdudkdjjsiiu@gmail10p.com', '$2y$10$Cq6no5k8eEc/9XQlNLOmpef2zVmNKLk3swNRHwZWqSxEnORMlQt7u', 'etudiant', NULL, NULL, '2025-10-28 04:06:24', '2025-10-28 04:06:24'),
(4, 'aaa', 'joe', '321@gmail.com', '$2y$10$BBeZwrwk8EdhHjQwXrLfROVGEDM1L/2akZGBU0taGWpu8AQ1/3CJO', 'etudiant', NULL, NULL, '2025-10-28 04:07:54', '2025-10-28 04:07:54'),
(5, 'bj', 'joe', 'jb@gmail.com', '$2y$10$NWs.RIHhSKQDDsyuOnIeBu3h8.MtFGST9N9NuhY3m5r7CVAHKmDlK', 'etudiant', NULL, NULL, '2025-10-28 04:28:32', '2025-10-28 04:28:32');

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `alumni`
--
ALTER TABLE `alumni`
  ADD CONSTRAINT `alumni_user_fk` FOREIGN KEY (`ref_user`) REFERENCES `user` (`id_user`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `candidature`
--
ALTER TABLE `candidature`
  ADD CONSTRAINT `candidature_ibfk_1` FOREIGN KEY (`ref_offre`) REFERENCES `offre` (`id_offre`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  ADD CONSTRAINT `candidature_ibfk_2` FOREIGN KEY (`ref_user`) REFERENCES `user` (`id_user`) ON DELETE RESTRICT ON UPDATE RESTRICT;

--
-- Contraintes pour la table `eleves`
--
ALTER TABLE `eleves`
  ADD CONSTRAINT `eleves_formation_fk` FOREIGN KEY (`ref_formation`) REFERENCES `formation` (`id_formation`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `eleves_user_fk` FOREIGN KEY (`ref_user`) REFERENCES `user` (`id_user`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `event`
--
ALTER TABLE `event`
  ADD CONSTRAINT `event_user_fk` FOREIGN KEY (`ref_user`) REFERENCES `user` (`id_user`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Contraintes pour la table `inscription_evenement`
--
ALTER TABLE `inscription_evenement`
  ADD CONSTRAINT `fk_inscription_event` FOREIGN KEY (`ref_evenement`) REFERENCES `event` (`id_evenement`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_inscription_user` FOREIGN KEY (`ref_user`) REFERENCES `user` (`id_user`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `inscrire`
--
ALTER TABLE `inscrire`
  ADD CONSTRAINT `fk_inscrire_event` FOREIGN KEY (`re_evenement`) REFERENCES `event` (`id_evenement`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_inscrire_user` FOREIGN KEY (`ref_user`) REFERENCES `user` (`id_user`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `offre`
--
ALTER TABLE `offre`
  ADD CONSTRAINT `fk_offre_entreprise` FOREIGN KEY (`ref_entreprise`) REFERENCES `entreprise` (`id_entreprise`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Contraintes pour la table `organise`
--
ALTER TABLE `organise`
  ADD CONSTRAINT `fk_organise_event` FOREIGN KEY (`re_evenement`) REFERENCES `event` (`id_evenement`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_organise_user` FOREIGN KEY (`ref_user`) REFERENCES `user` (`id_user`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `prof`
--
ALTER TABLE `prof`
  ADD CONSTRAINT `prof_formation_fk` FOREIGN KEY (`ref_formation`) REFERENCES `formation` (`id_formation`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `prof_user_fk` FOREIGN KEY (`ref_user`) REFERENCES `user` (`id_user`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `p_forum`
--
ALTER TABLE `p_forum`
  ADD CONSTRAINT `p_forum_user_fk` FOREIGN KEY (`ref_user`) REFERENCES `user` (`id_user`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `r_forum`
--
ALTER TABLE `r_forum`
  ADD CONSTRAINT `r_forum_post_fk` FOREIGN KEY (`ref_post_forum`) REFERENCES `p_forum` (`id_post`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `r_forum_user_fk` FOREIGN KEY (`ref_user`) REFERENCES `user` (`id_user`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `user`
--
ALTER TABLE `user`
  ADD CONSTRAINT `fk_user_entreprise` FOREIGN KEY (`ref_entreprise`) REFERENCES `entreprise` (`id_entreprise`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_user_formation` FOREIGN KEY (`ref_formation`) REFERENCES `formation` (`id_formation`) ON DELETE SET NULL ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
