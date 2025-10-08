-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:8889
-- Generation Time: Oct 08, 2025 at 08:39 AM
-- Server version: 8.0.40
-- PHP Version: 8.3.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `Tplprs`
--

-- --------------------------------------------------------

--
-- Table structure for table `alumni`
--

CREATE TABLE `alumni` (
  `ref_user` int UNSIGNED NOT NULL,
  `emploi_actuel` varchar(150) DEFAULT NULL,
  `ref_entreprise` varchar(150) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `candidature`
--

CREATE TABLE `candidature` (
  `id_candidature` int UNSIGNED NOT NULL,
  `motivation` text,
  `cv` varchar(255) DEFAULT NULL,
  `date_candidature` date NOT NULL,
  `ref_offre` int UNSIGNED NOT NULL,
  `ref_user` int UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `eleves`
--

CREATE TABLE `eleves` (
  `ref_user` int UNSIGNED NOT NULL,
  `annee_promo` varchar(10) DEFAULT NULL,
  `date_inscription` date NOT NULL,
  `classe` enum('btsUn','btsDeux','terminale','premiere','seconde') NOT NULL,
  `ref_formation` int UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `entreprise`
--

CREATE TABLE `entreprise` (
  `id_entreprise` int UNSIGNED NOT NULL,
  `nom` varchar(150) NOT NULL,
  `adresse` varchar(255) DEFAULT NULL,
  `site_web` varchar(190) DEFAULT NULL,
  `motif_partenariat` varchar(255) NOT NULL,
  `date_inscription` date NOT NULL,
  `ref_offre` int UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `event`
--

CREATE TABLE `event` (
  `id_evenement` int UNSIGNED NOT NULL,
  `type` varchar(100) DEFAULT NULL,
  `titre` varchar(200) NOT NULL,
  `description` text NOT NULL,
  `lieu` varchar(150) DEFAULT NULL,
  `nombre_place` int DEFAULT NULL,
  `date_creation` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `date_event` timestamp NOT NULL,
  `etat` enum('brouillon','publie','archive') DEFAULT 'publie',
  `ref_user` int UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `formation`
--

CREATE TABLE `formation` (
  `id_formation` int UNSIGNED NOT NULL,
  `nom_formation` varchar(150) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `inscrire`
--

CREATE TABLE `inscrire` (
  `re_evenement` int UNSIGNED NOT NULL,
  `ref_user` int UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `offre`
--

CREATE TABLE `offre` (
  `id_offre` int UNSIGNED NOT NULL,
  `titre` varchar(50) NOT NULL,
  `rue` varchar(50) NOT NULL,
  `cp` int NOT NULL,
  `ville` varchar(50) NOT NULL,
  `description` varchar(255) NOT NULL,
  `salaire` decimal(10,2) DEFAULT NULL,
  `type_offre` enum('CDI','CDD','Stage','Alternance','Autre') DEFAULT NULL,
  `date_creation` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `etat` enum('ouvert','ferme','brouillon') DEFAULT 'ouvert',
  `ref_entreprise` int UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `organise`
--

CREATE TABLE `organise` (
  `re_evenement` int UNSIGNED NOT NULL,
  `ref_user` int UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `partenaire`
--

CREATE TABLE `partenaire` (
  `ref_user` int NOT NULL,
  `promo` varchar(9) NOT NULL,
  `emploie_actuel` varchar(50) NOT NULL,
  `motif_partenariat` varchar(50) NOT NULL,
  `ref_entreprise` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `prof`
--

CREATE TABLE `prof` (
  `ref_user` int UNSIGNED NOT NULL,
  `matiere` varchar(15) DEFAULT NULL,
  `date_inscription` date NOT NULL,
  `ref_formation` int UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `p_forum`
--

CREATE TABLE `p_forum` (
  `id_post` int UNSIGNED NOT NULL,
  `titre` varchar(200) NOT NULL,
  `contenue` text,
  `date_creation` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `ref_user` int UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `r_forum`
--

CREATE TABLE `r_forum` (
  `id_reponse_forum` int UNSIGNED NOT NULL,
  `contenue` text NOT NULL,
  `date_creation` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `ref_post_forum` int UNSIGNED NOT NULL,
  `ref_user` int UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `id_user` int UNSIGNED NOT NULL,
  `nom` varchar(100) NOT NULL,
  `prenom` varchar(100) NOT NULL,
  `email` varchar(190) NOT NULL,
  `mdp` varchar(255) NOT NULL,
  `role` enum('admin','prof','alumni','entreprise','etudiant') DEFAULT 'etudiant',
  `ref_entreprise` int UNSIGNED DEFAULT NULL,
  `ref_formation` int UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `alumni`
--
ALTER TABLE `alumni`
  ADD PRIMARY KEY (`ref_user`),
  ADD KEY `ref_user` (`ref_user`),
  ADD KEY `ref_entreprise` (`ref_entreprise`);

--
-- Indexes for table `candidature`
--
ALTER TABLE `candidature`
  ADD PRIMARY KEY (`id_candidature`),
  ADD KEY `ref_offre` (`ref_offre`),
  ADD KEY `ref_user` (`ref_user`);

--
-- Indexes for table `eleves`
--
ALTER TABLE `eleves`
  ADD PRIMARY KEY (`ref_user`),
  ADD KEY `ref_user` (`ref_user`),
  ADD KEY `ref_formation` (`ref_formation`);

--
-- Indexes for table `entreprise`
--
ALTER TABLE `entreprise`
  ADD PRIMARY KEY (`id_entreprise`);

--
-- Indexes for table `event`
--
ALTER TABLE `event`
  ADD PRIMARY KEY (`id_evenement`),
  ADD KEY `ref_user` (`ref_user`);

--
-- Indexes for table `formation`
--
ALTER TABLE `formation`
  ADD PRIMARY KEY (`id_formation`);

--
-- Indexes for table `inscrire`
--
ALTER TABLE `inscrire`
  ADD KEY `re_evenement` (`re_evenement`),
  ADD KEY `ref_user` (`ref_user`);

--
-- Indexes for table `offre`
--
ALTER TABLE `offre`
  ADD PRIMARY KEY (`id_offre`),
  ADD KEY `ref_entreprise` (`ref_entreprise`);

--
-- Indexes for table `organise`
--
ALTER TABLE `organise`
  ADD KEY `re_evenement` (`re_evenement`),
  ADD KEY `ref_user` (`ref_user`);

--
-- Indexes for table `partenaire`
--
ALTER TABLE `partenaire`
  ADD PRIMARY KEY (`ref_user`);

--
-- Indexes for table `prof`
--
ALTER TABLE `prof`
  ADD PRIMARY KEY (`ref_user`),
  ADD UNIQUE KEY `ref_user` (`ref_user`),
  ADD UNIQUE KEY `ref_formation` (`ref_formation`);

--
-- Indexes for table `p_forum`
--
ALTER TABLE `p_forum`
  ADD PRIMARY KEY (`id_post`),
  ADD KEY `ref_user` (`ref_user`);

--
-- Indexes for table `r_forum`
--
ALTER TABLE `r_forum`
  ADD PRIMARY KEY (`id_reponse_forum`),
  ADD KEY `ref_post_forum` (`ref_post_forum`),
  ADD KEY `ref_user` (`ref_user`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id_user`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `role` (`role`),
  ADD KEY `fk_user_entreprise_idx` (`ref_entreprise`),
  ADD KEY `fk_user_formation_idx` (`ref_formation`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `candidature`
--
ALTER TABLE `candidature`
  MODIFY `id_candidature` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `entreprise`
--
ALTER TABLE `entreprise`
  MODIFY `id_entreprise` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `event`
--
ALTER TABLE `event`
  MODIFY `id_evenement` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `formation`
--
ALTER TABLE `formation`
  MODIFY `id_formation` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `offre`
--
ALTER TABLE `offre`
  MODIFY `id_offre` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `p_forum`
--
ALTER TABLE `p_forum`
  MODIFY `id_post` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `r_forum`
--
ALTER TABLE `r_forum`
  MODIFY `id_reponse_forum` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `id_user` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `alumni`
--
ALTER TABLE `alumni`
  ADD CONSTRAINT `alumni_user_fk` FOREIGN KEY (`ref_user`) REFERENCES `user` (`id_user`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `candidature`
--
ALTER TABLE `candidature`
  ADD CONSTRAINT `candidature_ibfk_1` FOREIGN KEY (`ref_offre`) REFERENCES `offre` (`id_offre`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  ADD CONSTRAINT `candidature_ibfk_2` FOREIGN KEY (`ref_user`) REFERENCES `user` (`id_user`) ON DELETE RESTRICT ON UPDATE RESTRICT;

--
-- Constraints for table `eleves`
--
ALTER TABLE `eleves`
  ADD CONSTRAINT `eleves_formation_fk` FOREIGN KEY (`ref_formation`) REFERENCES `formation` (`id_formation`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `eleves_user_fk` FOREIGN KEY (`ref_user`) REFERENCES `user` (`id_user`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `event`
--
ALTER TABLE `event`
  ADD CONSTRAINT `event_user_fk` FOREIGN KEY (`ref_user`) REFERENCES `user` (`id_user`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `inscrire`
--
ALTER TABLE `inscrire`
  ADD CONSTRAINT `fk_inscrire_event` FOREIGN KEY (`re_evenement`) REFERENCES `event` (`id_evenement`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_inscrire_user` FOREIGN KEY (`ref_user`) REFERENCES `user` (`id_user`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `offre`
--
ALTER TABLE `offre`
  ADD CONSTRAINT `fk_offre_entreprise` FOREIGN KEY (`ref_entreprise`) REFERENCES `entreprise` (`id_entreprise`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `organise`
--
ALTER TABLE `organise`
  ADD CONSTRAINT `fk_organise_event` FOREIGN KEY (`re_evenement`) REFERENCES `event` (`id_evenement`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_organise_user` FOREIGN KEY (`ref_user`) REFERENCES `user` (`id_user`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `prof`
--
ALTER TABLE `prof`
  ADD CONSTRAINT `prof_formation_fk` FOREIGN KEY (`ref_formation`) REFERENCES `formation` (`id_formation`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `prof_user_fk` FOREIGN KEY (`ref_user`) REFERENCES `user` (`id_user`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `p_forum`
--
ALTER TABLE `p_forum`
  ADD CONSTRAINT `p_forum_user_fk` FOREIGN KEY (`ref_user`) REFERENCES `user` (`id_user`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `r_forum`
--
ALTER TABLE `r_forum`
  ADD CONSTRAINT `r_forum_post_fk` FOREIGN KEY (`ref_post_forum`) REFERENCES `p_forum` (`id_post`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `r_forum_user_fk` FOREIGN KEY (`ref_user`) REFERENCES `user` (`id_user`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `user`
--
ALTER TABLE `user`
  ADD CONSTRAINT `fk_user_entreprise` FOREIGN KEY (`ref_entreprise`) REFERENCES `entreprise` (`id_entreprise`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_user_formation` FOREIGN KEY (`ref_formation`) REFERENCES `formation` (`id_formation`) ON DELETE SET NULL ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
