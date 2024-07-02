-- phpMyAdmin SQL Dump
-- version 4.5.4.1deb2ubuntu2
-- http://www.phpmyadmin.net
--
-- Client :  localhost
-- Généré le :  Jeu 26 Octobre 2017 à 13:53
-- Version du serveur :  5.7.19-0ubuntu0.16.04.1
-- Version de PHP :  7.0.22-0ubuntu0.16.04.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données :  `simple-mvc`
--

-- --------------------------------------------------------

--
-- Structure de la table `item`
--

-- CREATE TABLE `item` (
--   `id` int(11) UNSIGNED NOT NULL,
--   `title` varchar(255) NOT NULL
-- ) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Contenu de la table `item`
--

-- INSERT INTO `item` (`id`, `title`) VALUES
-- (1, 'Stuff'),
-- (2, 'Doodads');

--
-- Index pour les tables exportées
--

--
-- Index pour la table `item`
--
-- ALTER TABLE `item`
--   ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT pour les tables exportées
--

--
-- AUTO_INCREMENT pour la table `item`
--
-- ALTER TABLE `item`
--   MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;


CREATE TABLE `role`(
   `id_role` INT AUTO_INCREMENT,
   `name` VARCHAR(50) NOT NULL,
   PRIMARY KEY(`id_role`),
   UNIQUE(`name`)
);

CREATE TABLE `category`(
   `id_category` INT AUTO_INCREMENT,
   `name` VARCHAR(50) NOT NULL,
   `description` TEXT NOT NULL,
   `created_at` DATETIME,
   PRIMARY KEY(`id_category`)
);

CREATE TABLE `user`(
   `id_user` INT AUTO_INCREMENT,
   `name` VARCHAR(50) NOT NULL,
   `lastname` VARCHAR(50) NOT NULL,
   `mail` VARCHAR(50) NOT NULL,
   `created_at` DATETIME NOT NULL,
   `password` VARCHAR(255) NOT NULL,
   `profile_picture` VARCHAR(50),
   `id_role` INT NOT NULL,
   PRIMARY KEY(`id_user`),
   FOREIGN KEY(`id_role`) REFERENCES role(`id_role`)
);

CREATE TABLE `topic`(
   `id_topic` INT AUTO_INCREMENT,
   `title` VARCHAR(50) NOT NULL,
   `created_at` DATETIME,
   `content` TEXT,
   `picture` VARCHAR(50),
   `is_modified` BOOL default FALSE,
   `id_category` INT NOT NULL,
   `id_user` INT NOT NULL,
   PRIMARY KEY(`id_topic`),
   FOREIGN KEY(`id_category`) REFERENCES category(`id_category`),
   FOREIGN KEY(`id_user`) REFERENCES user(`id_user`)
);

CREATE TABLE `comment`(
   `id_comment` INT AUTO_INCREMENT,
   `created_at` DATETIME,
   `content` TEXT,
   `picture` VARCHAR(50),
   `is_modified` BOOL default FALSE,
   `id_user` INT NOT NULL,
   `id_topic` INT NOT NULL,
   PRIMARY KEY(`id_comment`),
   FOREIGN KEY(`id_user`) REFERENCES user(`id_user`),
   FOREIGN KEY(`id_topic`) REFERENCES topic(`id_topic`)
);



