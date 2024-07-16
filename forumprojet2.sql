drop database if exists projet2forum;
create database projet2forum;
use projet2forum;
drop table if exists category;
drop table if exists privilege;
drop table if exists `user`;
drop table if exists `topic`;
drop table if exists  `comment`;
drop table if exists `privilleges`;

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
   `profile_picture` VARCHAR(50),
   `id_privilege` INT NOT NULL,
   PRIMARY KEY(`id`),
   FOREIGN KEY(`id_privilege`) REFERENCES privilege(`id`)
);

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
   FOREIGN KEY(id_user) REFERENCES `user`(`id`),
   FOREIGN KEY(id_topic) REFERENCES topic(`id`)
);
use projet2forum;


INSERT INTO `privilege` VALUES
(3,'ADMIN'),
(2,'MODERATOR'),
(1,'USER');

INSERT INTO `user` VALUES 
(5,'John','Doe','john.doe@gmail.com','2024-07-11 12:39:23','$argon2i$v=19$m=131072,t=4,p=2$bnlCUXN5Zm16dGd6L3pUUw$F3l/QRr2ZIF+NVnAVmnxOl3HyimLfc5bve0FKyx7xPw',NULL,3),
(6,'John','Cena','tadadadaaa@unmail.com','2024-07-11 17:44:15','$argon2i$v=19$m=131072,t=4,p=2$bkNoUFZLcVFpaWpoVllZYw$9+yu+STMN2ufKHZChQQRQarfEw/2ml5hrZFVW1wEY+o',NULL,1),
(7,'Chuck','Norris','God@unmail.com','2024-07-11 17:45:24','$argon2i$v=19$m=131072,t=4,p=2$czBzUW1uZjJHTHMxWHhqQw$VQZe4CpY6kGQx1LY4+PRko1KnyhmRYyMZHat2hUL4gA',NULL,1),
(8,'Tom','Jedusor','voldemort@unmail.com','2024-07-11 17:47:11','$argon2i$v=19$m=131072,t=4,p=2$cGFjczA3YU54MWxqNWdJdA$683ASk3r0pejmGeRutKEVAuL4sYVLmRYvKc0PkMR8jE',NULL,1),
(9,'Albus','Dumbledor','HogwartsPatreon@unmail.com','2024-07-11 17:47:59','$argon2i$v=19$m=131072,t=4,p=2$NTN0ZTBjbmoySTVCbGE0Wg$yB9rJsIyOHSgT/AbdKNiNPAkEYnXOdYwImrEgHRyIYI',NULL,1),
(10,'Rubéus','Agrid','dresseurDepokemon@unmail.com','2024-07-11 18:05:03','$argon2i$v=19$m=131072,t=4,p=2$ZVo0aWNHR2FpODFxdk5PUQ$FDYPgQ/DLAu/+/4zTWOtKjSQQ8OkKd+wd2uvmrsiVC4',NULL,1),
(11,'Severus','Rogue','AlchemistCooker@unmail.com','2024-07-11 18:07:25','$argon2i$v=19$m=131072,t=4,p=2$Y1lES3RtYVpmMnRtdU5nRA$C0mxkuN3aCe7H4xEq6vYfnsCtb2/SVmavVjgpiKswOs',NULL,1);

INSERT INTO `category` VALUES
(1,'La magie','On dirait de la magie !','2024-07-11 12:58:37'),
(2,'La magie noir !','C\'est de la magie noir !','2024-07-11 13:37:06'),
(3,'Hogwarts\' Legacy','Tout sur le jeu basé sur l\'univers H.P.','2024-07-11 13:37:42'),
(4,'Alchimie','C\'est ici le vendeur Alchimiste ?','2024-07-11 14:10:51'),
(5,'Les animaux fantastique','les animaux de l\'univers H.P. basé sur le bestiaire du livre éponyme.','2024-07-11 14:46:48');

INSERT INTO `topic`(title,content,created_at,id_category,id_user) VALUES
('les potions, ça marche comment ?', "J'ai rien contre vos histoires de magie et tout le patatra, mais votre alchimie j'ai rien pigé.",now(),4,6),
("Sniff le nifleur : a vendre", "Voici Sniff, mon snifleur que j'ai depuis 34 ans, il à pas mal de bouteille mais il est encore en pleine forme. Prix : 400 gallons.",now(),5,10),
("toufu le chien à trois têtes", " Toufu, une brave bête, un peu soupe au lait, mais adorable quand on s'y attache, suite à la découverte de la pierre philosophale, l'école veux s'en débarrasser et je n'ai pas de terrains assez grand pour un chien de son gabarit, qui veux l'adopter ?",now(),5,10),
("Tuto : Avada Kedavra !", "Alors aujourd'hui j'vais vous montrer comment marche le sort Avada Kedavra, avec Kenny qui me servira de cobaye pour l'éxercice.",now(),2,8),
("Tuto : Wingardium Leviosah !", "Aujourd'hui je vous apprend le sortillège de lévitation.",now(),1,9);

INSERT INTO `comment`( content, id_user, id_topic, created_at ) VALUES
("Noob !", 11, 1, now()),
("Mais ! qu'est ce qu'il fait là lui ?", 8, 1, now()),
("bah j'ai vu de la lumière, et j'avais pas de matches de booké.", 6, 1, now()),
("Nan mais Severus à raison, noob !", 8, 1, now()),
("Bande de c***** !", 11, 1, now()),
("Sinon une bestiole pareil, ça mange quoi ?", 9,3,now()),
("des gens pourquoi ?", 10,3,now()),
("Tu m'interesses pour le coup là", 8,3,now());



