-- Création de la table des matières
CREATE TABLE IF NOT EXISTS `matiere` (
  `id_matiere` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `libelle` varchar(100) NOT NULL,
  `description` text,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_matiere`),
  UNIQUE KEY `libelle` (`libelle`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Insertion de quelques matières de base
INSERT INTO `matiere` (`libelle`, `description`) VALUES
('Informatique', 'Informatique générale et programmation'),
('Mathématiques', 'Mathématiques appliquées et théoriques'),
('Physique', 'Physique fondamentale et appliquée'),
('Chimie', 'Chimie organique et inorganique'),
('Biologie', 'Biologie cellulaire et moléculaire'),
('Économie', 'Économie et gestion'),
('Droit', 'Droit des affaires et propriété intellectuelle'),
('Langues', 'Langues étrangères et linguistique'),
('Génie électrique', 'Électronique et électrotechnique'),
('Mécanique', 'Mécanique des solides et des fluides');

-- Modification de la table prof pour utiliser une clé étrangère vers matiere
ALTER TABLE `prof` 
MODIFY `matiere` int UNSIGNED DEFAULT NULL,
ADD CONSTRAINT `fk_prof_matiere` FOREIGN KEY (`matiere`) REFERENCES `matiere` (`id_matiere`) ON DELETE SET NULL;
