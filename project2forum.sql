CREATE DATABASE projet2forum;
use projet2forum;

drop table if exists category;
drop table if exists privilege;
drop table if exists `user`;
drop table if exists `topic`;
drop table if exists  `comment`;

TRUNCATE TABLE category;
TRUNCATE TABLE privilege;
TRUNCATE TABLE `user`;
TRUNCATE TABLE `topic`;
TRUNCATE TABLE `comment`;


CREATE TABLE `privilege`(
   `id` INT AUTO_INCREMENT,
   `name` VARCHAR(50) NOT NULL,
   PRIMARY KEY(`id`),
   UNIQUE(`name`)
)
ENGINE=innodb CHARACTER SET 'utf8';;

CREATE TABLE `category`(
   `id` INT AUTO_INCREMENT,
   `name` VARCHAR(50) NOT NULL,
   `description` TEXT NOT NULL,
   `created_at` DATETIME,
   PRIMARY KEY(`id`)
)
ENGINE=innodb CHARACTER SET 'utf8';;

CREATE TABLE `user`(
   `id` INT AUTO_INCREMENT,
   `name` VARCHAR(50) NOT NULL,
   `lastname` VARCHAR(50) NOT NULL,
   `mail` VARCHAR(50) NOT NULL,
   `created_at` DATETIME NOT NULL,
   `password` VARCHAR(255) NOT NULL,
   `description` TEXT,
   `profile_picture` VARCHAR(50),
   `id_privilege` INT NOT NULL,
   PRIMARY KEY(`id`),
   FOREIGN KEY(`id_privilege`) REFERENCES privilege(`id`)
)
ENGINE=innodb CHARACTER SET 'utf8';;

CREATE TABLE `topic`(
   `id` INT AUTO_INCREMENT,
   `title` VARCHAR(50) NOT NULL,
   `created_at` DATETIME,
   `content` TEXT,
   `picture` VARCHAR(50),
   `is_modified` BOOL default FALSE,
   `id_category` INT NOT NULL,
   `id_user` INT NOT NULL,
   PRIMARY KEY(`id`),
   FOREIGN KEY(id_category) REFERENCES category(`id`),
   FOREIGN KEY(id_user) REFERENCES user(`id`)
)
ENGINE=innodb CHARACTER SET 'utf8';;

CREATE TABLE `comment`(
   `id` INT AUTO_INCREMENT,
   `created_at` DATETIME,
   `content` TEXT,
   `picture` VARCHAR(50),
   `is_modified` BOOL default FALSE,
   `id_user` INT NOT NULL,
   `id_topic` INT NOT NULL,
   PRIMARY KEY(`id`),
   FOREIGN KEY(id_user) REFERENCES `user`(`id`),
   FOREIGN KEY(id_topic) REFERENCES topic(`id`)
)
ENGINE=innodb CHARACTER SET 'utf8';;

CREATE TABLE contact (
    id INT AUTO_INCREMENT,
    name VARCHAR(50) NOT NULL,
    email VARCHAR(100) NOT NULL,
    message TEXT NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id)
)
ENGINE=innodb CHARACTER SET 'utf8';;



-- Insertion des données dans la table `privilege`
INSERT INTO `privilege` (`id`, `name`) VALUES
(1, 'USER'),
(2, 'MODERATOR'),
(3, 'ADMIN');

-- Insertion des données dans la table `category`
INSERT INTO `category` (`id`, `name`, `description`, `created_at`) VALUES
(1, 'La Grande Salle', 'Lieu de rassemblement principal de Poudlard.', NOW()),
(2, 'La Salle Commune', 'Salle réservée aux élèves de chaque maison.', NOW()),
(3, 'La Salle sur Demande', 'Salle magique qui apparaît lorsque quelqu\'un en a besoin.', NOW()),
(4, 'Le Lac Noir', 'Lac situé à côté de Poudlard.', NOW()),
(5, 'La Forêt Interdite', 'Forêt dense et dangereuse située près de Poudlard.', NOW()),
(6, 'Le Chemin de Traverse', 'Rue commerçante cachée dans Londres, réservée aux sorciers.', NOW()),
(7, 'La Chambre des Secrets', 'Chambre mythique construite par Salazar Serpentard.', NOW()),
(8, 'La Cabane de Hagrid', 'Résidence de Rubeus Hagrid, située à la lisière de la Forêt Interdite.', NOW()),
(9, 'La Salle des Trophées', 'Salle où sont exposés les trophées de Poudlard.', NOW()),
(10, 'Le Terrain de Quidditch', 'Terrain où se déroulent les matchs de Quidditch.', NOW());

-- Insertion des données dans la table `user`
INSERT INTO `user` (`id`, `name`, `lastname`, `mail`, `created_at`, `password`, `description`, `profile_picture`, `id_privilege`) VALUES
(1, 'Harry', 'Potter', 'harry.potter@poudlard.com', NOW(), 'motdepasse123', 'L\'Élu.', 'harry.jpg', 1),
(2, 'Hermione', 'Granger', 'hermione.granger@poudlard.com', NOW(), 'motdepasse123', 'Sorcier très talentueuse.', 'hermione.jpg', 1),
(3, 'Ron', 'Weasley', 'ron.weasley@poudlard.com', NOW(), 'motdepasse123', 'Meilleur ami de Harry.', 'ron.jpg', 1),
(4, 'Albus', 'Dumbledore', 'albus.dumbledore@poudlard.com', NOW(), 'motdepasse123', 'Directeur de Poudlard.', 'dumbledore.jpg', 3),
(5, 'Minerva', 'McGonagall', 'minerva.mcgonagall@poudlard.com', NOW(), 'motdepasse123', 'Professeur et directrice de Gryffondor.', 'mcgonagall.jpg', 2);

-- Insertion des données dans la table `topic`
INSERT INTO `topic` (`id`, `title`, `created_at`, `content`, `picture`, `is_modified`, `id_category`, `id_user`) VALUES
(1, 'Réunion dans la Grande Salle', NOW(), 'Rejoignez-nous pour une grande fête ce soir!', NULL, FALSE, 1, 1),
(2, 'Discussion dans la Salle Commune', NOW(), 'Partagez vos aventures du week-end.', NULL, FALSE, 2, 2),
(3, 'Exploration de la Forêt Interdite', NOW(), 'Cherchons des créatures magiques.', NULL, FALSE, 5, 3),
(4, 'Match de Quidditch', NOW(), 'Venez assister au grand match de Quidditch.', NULL, FALSE, 10, 4),
(5, 'Visite du Chemin de Traverse', NOW(), 'Rendez-vous pour acheter des fournitures.', NULL, FALSE, 6, 5);

-- Insertion des données dans la table `comment`
INSERT INTO `comment` (`id`, `created_at`, `content`, `picture`, `is_modified`, `id_user`, `id_topic`) VALUES
(1, NOW(), 'Je serai là!', NULL, FALSE, 2, 1),
(2, NOW(), 'Ça va être génial!', NULL, FALSE, 3, 1),
(3, NOW(), 'J\'ai hâte de discuter avec vous.', NULL, FALSE, 1, 2),
(4, NOW(), 'Attention aux centaures!', NULL, FALSE, 4, 3),
(5, NOW(), 'Quelqu\'un a besoin de balais?', NULL, FALSE, 5, 4),
(6, NOW(), 'Je dois acheter un nouveau chaudron.', NULL, FALSE, 2, 5);


-- Ajout de lignes supplémentaires pour les catégories
INSERT INTO `category` (`id`, `name`, `description`, `created_at`) VALUES
(11, 'Les Serres de Botanique', 'Lieu d\'étude des plantes magiques.', NOW()),
(12, 'La Tour d\'Astronomie', 'Tour utilisée pour les cours d\'astronomie.', NOW()),
(13, 'Le Bureau de Dumbledore', 'Bureau du directeur de Poudlard.', NOW()),
(14, 'La Bibliothèque', 'Endroit où sont conservés tous les livres de Poudlard.', NOW()),
(15, 'Le Pré-au-Lard', 'Seul village entièrement sorcier de Grande-Bretagne.', NOW()),
(16, 'La Salle de Potions', 'Salle où se déroulent les cours de potions.', NOW()),
(17, 'La Volière', 'Lieu où sont gardés les hiboux et autres oiseaux.', NOW()),
(18, 'Le Cours de Métamorphose', 'Salle de classe dédiée à l\'étude de la métamorphose.', NOW()),
(19, 'Le Bureau de McGonagall', 'Bureau de la directrice de Gryffondor.', NOW()),
(20, 'Le Terrain de Duel', 'Endroit réservé aux duels magiques.', NOW());

