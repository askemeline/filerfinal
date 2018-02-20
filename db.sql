-- phpMyAdmin SQL Dump
-- version 4.7.4
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le :  mar. 20 fév. 2018 à 14:50
-- Version du serveur :  5.7.19
-- Version de PHP :  7.1.9

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données :  `filerfinal`
--

-- --------------------------------------------------------

--
-- Structure de la table `files`
--

DROP TABLE IF EXISTS `files`;
CREATE TABLE IF NOT EXISTS `files` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `token` varchar(15) NOT NULL,
  `path` text NOT NULL,
  `id_user` varchar(255) NOT NULL,
  `date_ajout` date NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `creation` datetime NOT NULL,
  `firstname` varchar(255) NOT NULL,
  `lastname` varchar(255) NOT NULL,
  `username` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;

--
-- Déchargement des données de la table `users`
--

INSERT INTO `users` (`id`, `creation`, `firstname`, `lastname`, `username`, `email`, `password`) VALUES
(1, '2018-02-20 14:40:50', 'aa', 'aa', 'aa', 'aa@aa.com', '$2y$10$KbrLAwdNR5d3NXjhioNTv.Z8RH9HAwMdBDrU/u.cPY.tC03uTt4/e'),
(2, '2018-02-20 14:40:50', 'aa', 'aa', 'aa', 'aa@aa.com', '$2y$10$KbrLAwdNR5d3NXjhioNTv.Z8RH9HAwMdBDrU/u.cPY.tC03uTt4/e'),
(3, '2018-02-20 14:41:12', 'aa', 'aa', 'aa', 'aa@cc.com', '$2y$10$539p5wZnaxfSe5loea8ioekKXrRFogpyz.1MyGoo5Br3iahJ3AAV6'),
(4, '2018-02-20 14:41:12', 'aa', 'aa', 'aa', 'aa@cc.com', '$2y$10$539p5wZnaxfSe5loea8ioekKXrRFogpyz.1MyGoo5Br3iahJ3AAV6');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
