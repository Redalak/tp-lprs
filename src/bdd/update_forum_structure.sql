-- Ajout de la colonne canal à la table p_forum
ALTER TABLE `p_forum` 
ADD COLUMN `canal` ENUM('general', 'alumni_entreprises', 'etudiants_professeurs') NOT NULL DEFAULT 'general' AFTER `ref_user`,
ADD INDEX `idx_canal` (`canal`);

-- Mettre à jour les posts existants pour qu'ils appartiennent au canal général
UPDATE `p_forum` SET `canal` = 'general' WHERE `canal` = '' OR `canal` IS NULL;
