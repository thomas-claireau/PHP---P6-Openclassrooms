DROP DATABASE IF EXISTS blog_thomas_claireau;
CREATE DATABASE blog_thomas_claireau CHARACTER SET 'utf8';

USE blog_thomas_claireau;

-- --------------------------------------------------------

--
-- Structure de la table `User`
--

CREATE TABLE `User` (
  `id` SMALLINT NOT NULL PRIMARY KEY AUTO_INCREMENT,
  `prenom` VARCHAR(255) NOT NULL,
  `nom` VARCHAR(255) NOT NULL,
  `mail` VARCHAR(255) NOT NULL,
  `password` VARCHAR(1000) NOT NULL,
  `admin` TINYINT(1) NOT NULL,
  `actif` TINYINT(1) NOT NULL,
  `avatar_img_path` VARCHAR(1000) DEFAULT NULL,
  `token` VARCHAR(255) DEFAULT NULL,
  `dateToken` datetime NOT NULL
) ENGINE=INNODB DEFAULT CHARSET=utf8;

--
-- Structure de la table `Post`
--

CREATE TABLE `Post` (
  `id` SMALLINT NOT NULL PRIMARY KEY AUTO_INCREMENT,
  `id_user` SMALLINT NOT NULL,
  `title` VARCHAR(255) NOT NULL,
  `date` DATETIME NOT NULL,
  `description` TEXT NOT NULL,
  `content` TEXT NOT NULL,
  `main_img_path` VARCHAR(255) DEFAULT NULL,
  CONSTRAINT fk_post_id_user FOREIGN KEY (`id_user`) REFERENCES User(`id`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;

--
-- Structure de la table `Comment`
--

CREATE TABLE `Comment` (
  `id` SMALLINT NOT NULL PRIMARY KEY AUTO_INCREMENT,
  `id_user` SMALLINT NOT NULL,
  `id_post` SMALLINT NOT NULL,
  `title` VARCHAR(255) NOT NULL,
  `content` TEXT NOT NULL,
  `date` DATETIME NOT NULL,
  CONSTRAINT fk_comment_id_user FOREIGN KEY (`id_user`) REFERENCES User(`id`),
  CONSTRAINT fk_comment_id_post FOREIGN KEY (`id_post`) REFERENCES Post(`id`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;
