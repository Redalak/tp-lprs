-- Création de la table de liaison entre utilisateur et entreprise
CREATE TABLE IF NOT EXISTS `user_entreprise` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `ref_user` int UNSIGNED NOT NULL,
  `ref_entreprise` int UNSIGNED NOT NULL,
  `date_liaison` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_user_entreprise` (`ref_user`, `ref_entreprise`),
  KEY `ref_entreprise` (`ref_entreprise`),
  CONSTRAINT `user_entreprise_ibfk_1` FOREIGN KEY (`ref_user`) REFERENCES `user` (`id_user`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `user_entreprise_ibfk_2` FOREIGN KEY (`ref_entreprise`) REFERENCES `entreprise` (`id_entreprise`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
