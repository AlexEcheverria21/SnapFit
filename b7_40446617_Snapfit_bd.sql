-- phpMyAdmin SQL Dump
-- version 4.9.0.1
-- https://www.phpmyadmin.net/
--
-- Hôte : sql100.byethost7.com
-- Généré le :  mar. 25 nov. 2025 à 03:22
-- Version du serveur :  10.6.22-MariaDB
-- Version de PHP :  7.2.22

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données :  `b7_40446617_Snapfit_bd`
--

-- --------------------------------------------------------

--
-- Structure de la table `AJOUTER`
--

CREATE TABLE `AJOUTER` (
  `id_utilisateur` int(11) NOT NULL,
  `id_favori` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Déchargement des données de la table `AJOUTER`
--

INSERT INTO `AJOUTER` (`id_utilisateur`, `id_favori`) VALUES
(1, 1),
(1, 2),
(2, 2),
(3, 3),
(5, 4);

-- --------------------------------------------------------

--
-- Structure de la table `FAVORI`
--

CREATE TABLE `FAVORI` (
  `id_favori` int(11) NOT NULL,
  `url` varchar(255) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `categorie` varchar(100) DEFAULT NULL,
  `marque` varchar(100) DEFAULT NULL,
  `date_fav` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Déchargement des données de la table `FAVORI`
--

INSERT INTO `FAVORI` (`id_favori`, `url`, `image`, `categorie`, `marque`, `date_fav`) VALUES
(1, 'https://siteA.com/produit/abc', 'img/prod_abc.jpg', 'Électronique', 'TechCorp', '2025-11-01'),
(2, 'https://blogmode.fr/article/def', 'img/art_def.png', 'Mode', 'StyleLibre', '2025-11-05'),
(3, 'https://recettes.net/gateau/ghi', 'img/recette_ghi.jpeg', 'Cuisine', 'RecettesPlus', '2025-10-28'),
(4, 'https://voyage.com/guide/jkl', 'img/guide_jkl.jpg', 'Voyage', 'GlobeTrotter', '2025-11-10'),
(5, 'https://sportshop.net/equipement/mno', 'img/sport_mno.gif', 'Sport', 'ActiveGear', '2025-11-15');

-- --------------------------------------------------------

--
-- Structure de la table `RECHERCHE`
--

CREATE TABLE `RECHERCHE` (
  `id_recherche` int(11) NOT NULL,
  `id_utilisateur` int(11) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `date_recherche` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Déchargement des données de la table `RECHERCHE`
--

INSERT INTO `RECHERCHE` (`id_recherche`, `id_utilisateur`, `image`, `date_recherche`) VALUES
(1, 1, 'url/image1_chaussure.jpg', '2025-11-15 01:16:41'),
(2, 1, 'url/image2_sac.jpg', '2025-11-17 01:16:41'),
(3, 2, 'url/image3_montre.png', '2025-11-17 20:16:41'),
(4, 3, 'url/image4_robe.jpg', '2025-11-16 01:16:41'),
(5, 2, 'url/image5_voiture.jpg', '2025-11-18 00:16:41');

-- --------------------------------------------------------

--
-- Structure de la table `SITE`
--

CREATE TABLE `SITE` (
  `id_site` int(11) NOT NULL,
  `url` varchar(255) NOT NULL,
  `nom` varchar(100) NOT NULL,
  `type` varchar(50) DEFAULT NULL,
  `fiabilite` int(11) DEFAULT NULL,
  `id_utilisateur` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Déchargement des données de la table `SITE`
--

INSERT INTO `SITE` (`id_site`, `url`, `nom`, `type`, `fiabilite`, `id_utilisateur`) VALUES
(1, 'https://devtool.io', 'DevTool', 'Développement', 9, 1),
(2, 'https://publicnews.com', 'Public News', 'Actualités', 7, NULL),
(3, 'https://weatherdata.org', 'WeatherData', 'API Météo', 8, NULL),
(4, 'https://freesoftware.net', 'Free Software', 'Téléchargement', 6, NULL),
(5, 'https://designstudio.co', 'Design Studio', 'Création', 10, 3);

-- --------------------------------------------------------

--
-- Structure de la table `UTILISATEUR`
--

CREATE TABLE `UTILISATEUR` (
  `id_utilisateur` int(11) NOT NULL,
  `nom` varchar(100) NOT NULL,
  `prenom` varchar(100) NOT NULL,
  `mot_de_passe_hash` varchar(255) NOT NULL,
  `role` varchar(50) DEFAULT NULL,
  `date_inscription` date NOT NULL,
  `email` varchar(150) NOT NULL,
  `nom_connexion` varchar(100) NOT NULL,
  `sexe` char(1) DEFAULT NULL,
  `pays` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Déchargement des données de la table `UTILISATEUR`
--

INSERT INTO `UTILISATEUR` (`id_utilisateur`, `nom`, `prenom`, `mot_de_passe_hash`, `role`, `date_inscription`, `email`, `nom_connexion`, `sexe`, `pays`) VALUES
(1, 'Dupont', 'Alice', '482c811da5d5b4bc6d497ffa98491e38', 'Admin', '0000-00-00', 'alice.dupont@mail.com', 'alice_d', 'F', 'France'),
(2, 'Smith', 'Bob', 'bb77d0d3b3f239fa5db73bdf27b8d29a', 'Membre', '0000-00-00', 'bob.smith@mail.com', 'bob_s', 'M', 'Canada'),
(3, 'Garcia', 'Sofia', '2943e482d39e00faf6be02a6e3491662', 'Membre', '0000-00-00', 'sofia.garcia@mail.com', 'sofia_g', 'F', 'Espagne'),
(4, 'Chen', 'Wei', 'f3ec6eb7e38a245c66f39a1c42c8d9b7', 'Invité', '0000-00-00', 'wei.chen@mail.com', 'wei_c', 'M', 'Chine'),
(5, 'Rossi', 'Marco', '313450344e7bf15ae524b7b4ab5edbfe', 'Membre', '0000-00-00', 'marco.rossi@mail.com', 'marco_r', 'M', 'Italie');

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `AJOUTER`
--
ALTER TABLE `AJOUTER`
  ADD PRIMARY KEY (`id_utilisateur`,`id_favori`),
  ADD KEY `fk_ajouter_favori` (`id_favori`);

--
-- Index pour la table `FAVORI`
--
ALTER TABLE `FAVORI`
  ADD PRIMARY KEY (`id_favori`),
  ADD UNIQUE KEY `url` (`url`);

--
-- Index pour la table `RECHERCHE`
--
ALTER TABLE `RECHERCHE`
  ADD PRIMARY KEY (`id_recherche`),
  ADD KEY `fk_recherche_utilisateur` (`id_utilisateur`);

--
-- Index pour la table `SITE`
--
ALTER TABLE `SITE`
  ADD PRIMARY KEY (`id_site`),
  ADD UNIQUE KEY `url` (`url`),
  ADD KEY `fk_site_utilisateur` (`id_utilisateur`);

--
-- Index pour la table `UTILISATEUR`
--
ALTER TABLE `UTILISATEUR`
  ADD PRIMARY KEY (`id_utilisateur`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `nom_connexion` (`nom_connexion`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `FAVORI`
--
ALTER TABLE `FAVORI`
  MODIFY `id_favori` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT pour la table `RECHERCHE`
--
ALTER TABLE `RECHERCHE`
  MODIFY `id_recherche` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT pour la table `SITE`
--
ALTER TABLE `SITE`
  MODIFY `id_site` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT pour la table `UTILISATEUR`
--
ALTER TABLE `UTILISATEUR`
  MODIFY `id_utilisateur` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `AJOUTER`
--
ALTER TABLE `AJOUTER`
  ADD CONSTRAINT `fk_ajouter_favori` FOREIGN KEY (`id_favori`) REFERENCES `FAVORI` (`id_favori`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_ajouter_utilisateur` FOREIGN KEY (`id_utilisateur`) REFERENCES `UTILISATEUR` (`id_utilisateur`) ON DELETE CASCADE;

--
-- Contraintes pour la table `RECHERCHE`
--
ALTER TABLE `RECHERCHE`
  ADD CONSTRAINT `fk_recherche_utilisateur` FOREIGN KEY (`id_utilisateur`) REFERENCES `UTILISATEUR` (`id_utilisateur`) ON DELETE CASCADE;

--
-- Contraintes pour la table `SITE`
--
ALTER TABLE `SITE`
  ADD CONSTRAINT `fk_site_utilisateur` FOREIGN KEY (`id_utilisateur`) REFERENCES `UTILISATEUR` (`id_utilisateur`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
