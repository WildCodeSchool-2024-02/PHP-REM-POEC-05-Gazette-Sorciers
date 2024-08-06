CREATE DATABASE projet2forum;
use projet2forum;

drop table if exists category;
drop table if exists privilege;
drop table if exists `user`;
drop table if exists `topic`;
drop table if exists  `comment`;

-- pour qui ne souhaite pas supprimer ces tables mais juste les vidées --
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
);

CREATE TABLE `category`(
   `id` INT AUTO_INCREMENT,
   `name` VARCHAR(50) NOT NULL,
   `description` TEXT NOT NULL,
   `picture` VARCHAR(50),
   `created_at` DATETIME,
   PRIMARY KEY(`id`)
);

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
);

CREATE TABLE `token`(
	`id` INT AUTO_INCREMENT,
    `key` VARCHAR(255) NOT NULL,
    `id_user` INT NOT NULL,
    `created_at` DATETIME NOT NULL,
    PRIMARY KEY(`id`),
    FOREIGN KEY(`id_user`) REFERENCES user(`id`)
); 

CREATE TABLE `topic`(
   `id` INT AUTO_INCREMENT,
   `title` VARCHAR(200) NOT NULL,
   `created_at` DATETIME,
   `content` TEXT,
   `picture` VARCHAR(50),
   `is_modified` BOOL default FALSE,
   `id_category` INT NOT NULL,
   `id_user` INT NOT NULL,
   PRIMARY KEY(`id`),
   FOREIGN KEY(id_category) REFERENCES category(`id`),
   FOREIGN KEY(id_user) REFERENCES user(`id`) ON UPDATE CASCADE
);

CREATE TABLE notification (
    id INT(10) NOT NULL AUTO_INCREMENT,
    id_user INT(10) NOT NULL,
    id_topic INT(10) NOT NULL,
    created_at DATETIME NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    CONSTRAINT FK_notification_topic FOREIGN KEY (id_topic) REFERENCES topic (id) ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT notification_ibfk_1 FOREIGN KEY (id_user) REFERENCES user (id) ON UPDATE CASCADE ON DELETE CASCADE
);

CREATE TABLE `comment`(
   `id` INT AUTO_INCREMENT,
   `created_at` DATETIME,
   `content` TEXT,
   `picture` VARCHAR(50),
   `is_modified` BOOL default FALSE,
   `id_user` INT NOT NULL,
   `id_topic` INT NOT NULL,
   PRIMARY KEY(`id`),
   FOREIGN KEY(id_user) REFERENCES `user`(`id`) ON UPDATE CASCADE,
   FOREIGN KEY(id_topic) REFERENCES topic(`id`) ON UPDATE CASCADE
);

CREATE TABLE contact (
    id INT AUTO_INCREMENT,
    name VARCHAR(50) NOT NULL,
    email VARCHAR(100) NOT NULL,
    message TEXT NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id)
);
-- Insertion des données dans la table `privilege`
INSERT INTO `privilege` (`name`) VALUES
('USER'),
('MODERATOR'),
('ADMIN');

-- Insertion des données dans la table `category` --
INSERT INTO `category` (`name`, `description`, `picture`, `created_at`) VALUES
('La Grande Salle', 'Discussions générales et annonces du forum.', 'category1.jpg', NOW()),
('La Salle Commune', 'Discussions sur les différentes adaptations de Harry Potter.', 'category2.jpg', NOW()),
('La Bibliothèque', 'Espace pour parler de littérature, livres et fanfictions.', 'category3.jpg', NOW()),
('Le Bureau de Dumbledore', 'Discussions sur les théories et analyses des personnages et de l\'intrigue.', 'category4.jpg', NOW()),
('Le Terrain de Quidditch', 'Pour parler de sport, et compétitions de quidditch irl, mais aussi des jeux vidéos.', 'category5.jpg', NOW()),
('La Forêt Interdite', 'Envie de parler de tout et de rien? Les discussions non liées à HP, c\'est ici!', 'category6.jpg', NOW()),
('Les Cuisines de Poudlard', 'Pour parler de recettes et de la nourriture inspirées par l\'univers de Harry Potter.', 'category7.jpg', NOW()),
('Le Chaudron Baveur', 'Espace pour les rencontres entre membres, discussions sociales, les événements communautaires et les conventions.', 'category8.jpg', NOW()),
('La Salle sur Demande', 'Projets créatifs et fanarts.', 'category9.jpg', NOW()),
('Le Chemin de Traverse', 'Votre dernière trouvaille, une nouvelle boutique? Pour parler des nouveautés et des achats relatifs à l\'univers de Harry Potter.', 'category10.jpg', NOW()),
('La Volière', 'Fan de relations épistolaires? Envie d\'échanger sur papier avec des fans? C\'est ici!', 'category11.jpg', NOW());

-- Insertion des utilisateurs
INSERT INTO `user` (`name`, `lastname`, `mail`, `created_at`, `password`, `description`, `profile_picture`, `id_privilege`) VALUES
('Jean', 'Dupont', 'jean.dupont@gmail.com', NOW(), '$2a$12$gHS6NsmqYqBj6YHyY4T6yOOBXJoEC9L2AD1UWp5T0hxLJNXIivPLW', 'Grand fan de Harry Potter depuis l\'enfance.', 'profile1.jpg', 1),
('Marie', 'Durand', 'marie.durand@gmail.com', NOW(), '$2a$12$gHS6NsmqYqBj6YHyY4T6yOOBXJoEC9L2AD1UWp5T0hxLJNXIivPLW', 'Passionnée par les fanfictions et les théories sur Harry Potter.', 'profile2.jpg', 2),
('Paul', 'Martin', 'paul.martin@gmail.com', NOW(), '$2a$12$gHS6NsmqYqBj6YHyY4T6yOOBXJoEC9L2AD1UWp5T0hxLJNXIivPLW', 'J\'adore discuter des adaptations cinématographiques de Harry Potter.', 'profile3.jpg', 3);

-- Insertion des sujets (topics)
INSERT INTO `topic` (`title`, `created_at`, `content`, `picture`, `is_modified`, `id_category`, `id_user`) VALUES
('Présentation des membres', NOW(), 'Présentez-vous ici!', 'topic1.jpg', FALSE, 1, 1),
('Règles du forum', NOW(), 'Veuillez lire les règles avant de poster.', 'topic2.jpg', FALSE, 1, 2),
('Suggestions pour le forum', NOW(), 'Donnez-nous vos suggestions pour améliorer le forum.', 'topic3.jpg', FALSE, 1, 3),
('La nouvelle série HP sur HBO Max', NOW(), 'Quelles sont vos attentes pour la nouvelle série?', 'topic4.jpg', FALSE, 2, 1),
('HP et l\'Enfant Maudit: top ou flop?', NOW(), 'Discutez de la pièce de théâtre et de son impact.', 'topic5.jpg', FALSE, 2, 2),
('Vos TOP 10 de fanfictions sur AO3!', NOW(), 'Partagez vos fanfictions préférées.', 'topic6.jpg', FALSE, 3, 3),
('Pourquoi Dumbledore a-t-il laissé Harry chez les Dursley?', NOW(), 'Analyse et théorie sur cette décision de Dumbledore.', 'topic7.jpg', FALSE, 4, 1),
('Programme de compèt Quidditch, saison 2024', NOW(), 'Calendrier et résultats des compétitions de quidditch.', 'topic8.jpg', FALSE, 5, 2),
('Filmographie Emma Watson', NOW(), 'Discussions sur les films d\'Emma Watson en dehors de HP.', 'topic9.jpg', FALSE, 6, 3),
('Recherche: "La Cuisine pour les Sorciers" de Gastronogeek', NOW(), 'Quelqu\'un a-t-il ce livre? Que vaut-il?', 'topic10.jpg', FALSE, 7, 1),
('Rencontre IRL de fans sur Paris', NOW(), 'Organisons une rencontre entre fans à Paris.', 'topic11.jpg', FALSE, 8, 2),
('Que pensez-vous de cette fanvid sur Les Maraudeurs?', NOW(), 'Partagez et discutez de vos créations vidéo.', 'topic12.jpg', FALSE, 9, 3),
('Le Défi des Sorciers ferme à Lille 😦', NOW(), 'Triste nouvelle pour les fans à Lille.', 'topic13.jpg', FALSE, 10, 1),
('Recherche d\'un correspondant Anglophone', NOW(), 'Envie d\'échanger des lettres avec un fan anglophone?', 'topic14.jpg', FALSE, 11, 2);

-- Insertion des commentaires --
INSERT INTO `comment` (`created_at`, `content`, `picture`, `is_modified`, `id_user`, `id_topic`) VALUES
(NOW(), 'Bienvenue à tous les nouveaux membres!', 'comment1.jpg', FALSE, 1, 1),
(NOW(), 'Merci de respecter les règles pour une bonne ambiance sur le forum.', 'comment2.jpg', FALSE, 2, 2),
(NOW(), 'J\'ai une suggestion pour améliorer la section fanfictions.', 'comment3.jpg', FALSE, 3, 3),
(NOW(), 'Je suis super excité pour la nouvelle série HBO!', 'comment4.jpg', FALSE, 1, 4),
(NOW(), 'Personnellement, j\'ai trouvé "L\'Enfant Maudit" plutôt décevant.', 'comment5.jpg', FALSE, 2, 5),
(NOW(), 'Voici ma liste de fanfictions préférées sur AO3.', 'comment6.jpg', FALSE, 3, 6),
(NOW(), 'Je pense que Dumbledore avait ses raisons, mais c\'est discutable.', 'comment7.jpg', FALSE, 1, 7),
(NOW(), 'Hâte de voir les matchs de quidditch cette saison!', 'comment8.jpg', FALSE, 2, 8),
(NOW(), 'Emma Watson est fantastique dans tous ses rôles.', 'comment9.jpg', FALSE, 3, 9),
(NOW(), 'J\'ai ce livre et il est génial! Je le recommande.', 'comment10.jpg', FALSE, 1, 10),
(NOW(), 'Qui est partant pour une rencontre à Paris le mois prochain?', 'comment11.jpg', FALSE, 2, 11),
(NOW(), 'Cette fanvid sur Les Maraudeurs est incroyablement bien faite!', 'comment12.jpg', FALSE, 3, 12),
(NOW(), 'Vraiment triste que le Défi des Sorciers ferme.', 'comment13.jpg', FALSE, 1, 13),
(NOW(), 'Je serais intéressé par un correspondant anglophone. Qui est partant?', 'comment14.jpg', FALSE, 2, 14);