-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Hôte : db
-- Généré le : lun. 19 mai 2025 à 14:59
-- Version du serveur : 9.3.0
-- Version de PHP : 8.2.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `dondesang`
--

-- --------------------------------------------------------

--
-- Structure de la table `agents`
--

CREATE TABLE `agents` (
  `id` int NOT NULL,
  `users_id` int NOT NULL,
  `banques_id` int NOT NULL,
  `fonction` smallint NOT NULL,
  `status` tinyint(1) NOT NULL,
  `slug` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `annalyse`
--

CREATE TABLE `annalyse` (
  `id` int NOT NULL,
  `don_id` int NOT NULL,
  `enregistreur_id` int NOT NULL,
  `banques_id` int NOT NULL,
  `vih` tinyint(1) NOT NULL,
  `syphilis` tinyint(1) NOT NULL,
  `hepatite_b` tinyint(1) NOT NULL,
  `hepatite_c` tinyint(1) NOT NULL,
  `groupe_sanguin` smallint NOT NULL,
  `rhesus` smallint NOT NULL,
  `disponibilite` smallint NOT NULL,
  `slug` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `banque`
--

CREATE TABLE `banque` (
  `id` int NOT NULL,
  `denominateur` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `adresse` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `telephone` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` smallint NOT NULL,
  `departement` smallint NOT NULL,
  `slug` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `borderaux`
--

CREATE TABLE `borderaux` (
  `id` int NOT NULL,
  `enregistreur_id` int NOT NULL,
  `expiditeur_id` int NOT NULL,
  `destinateur_id` int NOT NULL,
  `nbm_cgr` smallint NOT NULL,
  `nbm_pfc` smallint NOT NULL,
  `nbm_cpa` smallint NOT NULL,
  `image_borderaux` varchar(180) COLLATE utf8mb4_unicode_ci NOT NULL,
  `image_update` tinyint(1) NOT NULL,
  `codification` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `demande_de_sang`
--

CREATE TABLE `demande_de_sang` (
  `id` int NOT NULL,
  `patients_id` int NOT NULL,
  `hopital_id` int NOT NULL,
  `enregistreur_id` int NOT NULL,
  `diagnostic` longtext COLLATE utf8mb4_unicode_ci,
  `type_besoin` smallint NOT NULL,
  `niveau_urgence` smallint NOT NULL,
  `groupe_sanguin` smallint NOT NULL,
  `rhesus` smallint NOT NULL,
  `quantite_cgr` int DEFAULT NULL,
  `quantite_pfc` int DEFAULT NULL,
  `quantite_cpa` int DEFAULT NULL,
  `besoin_phéenotype` tinyint(1) NOT NULL,
  `statut` smallint NOT NULL,
  `slug` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `doctrine_migration_versions`
--

CREATE TABLE `doctrine_migration_versions` (
  `version` varchar(191) COLLATE utf8mb3_unicode_ci NOT NULL,
  `executed_at` datetime DEFAULT NULL,
  `execution_time` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Déchargement des données de la table `doctrine_migration_versions`
--

INSERT INTO `doctrine_migration_versions` (`version`, `executed_at`, `execution_time`) VALUES
('DoctrineMigrations\\Version20250519145804', '2025-05-19 14:58:13', 67768);

-- --------------------------------------------------------

--
-- Structure de la table `donateur`
--

CREATE TABLE `donateur` (
  `id` int NOT NULL,
  `enregistreur_id` int NOT NULL,
  `banques_id` int NOT NULL,
  `phenotypes_id` int DEFAULT NULL,
  `nom` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `prenom` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `telephone` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `adresse` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `sexe` smallint NOT NULL,
  `poids` double NOT NULL,
  `taille` double NOT NULL,
  `date_de_naissance` date NOT NULL,
  `piece` smallint NOT NULL,
  `no_secu` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `legibilite` tinyint(1) NOT NULL,
  `groupe` smallint NOT NULL,
  `rhesus` smallint NOT NULL,
  `dernier_dons` date NOT NULL,
  `slug` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `dons`
--

CREATE TABLE `dons` (
  `id` int NOT NULL,
  `donateurs_id` int NOT NULL,
  `banques_id` int NOT NULL,
  `enregistreur_id` int NOT NULL,
  `volume` smallint NOT NULL,
  `date_collecte` date NOT NULL,
  `type_de_dons` smallint NOT NULL,
  `codification` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `annalyse` tinyint(1) NOT NULL,
  `utilisable` tinyint(1) NOT NULL,
  `expiration` tinyint(1) NOT NULL,
  `slug` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `messenger_messages`
--

CREATE TABLE `messenger_messages` (
  `id` bigint NOT NULL,
  `body` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `headers` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue_name` varchar(190) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `available_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `delivered_at` datetime DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `patient`
--

CREATE TABLE `patient` (
  `id` int NOT NULL,
  `hopital_id` int NOT NULL,
  `enregistreur_id` int NOT NULL,
  `nom` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `sexe` smallint NOT NULL,
  `telephone` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `adresse` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `no_secu` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `date_naissance` date DEFAULT NULL,
  `slug` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `phenotype`
--

CREATE TABLE `phenotype` (
  `id` int NOT NULL,
  `donneurs_id` int NOT NULL,
  `c` smallint NOT NULL,
  `e` smallint NOT NULL,
  `c_maj` smallint NOT NULL,
  `e_maj` smallint NOT NULL,
  `kell` smallint NOT NULL,
  `duffy` smallint NOT NULL,
  `kidd` smallint NOT NULL,
  `vel` smallint NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `reset_password_request`
--

CREATE TABLE `reset_password_request` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `selector` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `hashed_token` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `requested_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `expires_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `stock_derive`
--

CREATE TABLE `stock_derive` (
  `id` int NOT NULL,
  `don_id` int NOT NULL,
  `enregistreur_id` int NOT NULL,
  `banques_id` int NOT NULL,
  `phenotypes_id` int DEFAULT NULL,
  `type_produit` smallint NOT NULL,
  `groupe` smallint NOT NULL,
  `rhesus` smallint NOT NULL,
  `status` smallint NOT NULL,
  `date_expiration` date NOT NULL,
  `slug` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `transfert`
--

CREATE TABLE `transfert` (
  `id` int NOT NULL,
  `borderau_id` int NOT NULL,
  `porches_id` int NOT NULL,
  `enregistreur_id` int NOT NULL,
  `expiditeur_id` int NOT NULL,
  `destinateur_id` int NOT NULL,
  `slug` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `user`
--

CREATE TABLE `user` (
  `id` int NOT NULL,
  `banques_id` int NOT NULL,
  `email` varchar(180) COLLATE utf8mb4_unicode_ci NOT NULL,
  `roles` json NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nom` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `prenom` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_verified` tinyint(1) NOT NULL,
  `slug` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `agents`
--
ALTER TABLE `agents`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_9596AB6E67B3B43D` (`users_id`),
  ADD KEY `IDX_9596AB6E184937D5` (`banques_id`);

--
-- Index pour la table `annalyse`
--
ALTER TABLE `annalyse`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_275CDCA57B3C9061` (`don_id`),
  ADD KEY `IDX_275CDCA5749678EB` (`enregistreur_id`),
  ADD KEY `IDX_275CDCA5184937D5` (`banques_id`);

--
-- Index pour la table `banque`
--
ALTER TABLE `banque`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `borderaux`
--
ALTER TABLE `borderaux`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UNIQ_A0A2C655655F6CC` (`codification`),
  ADD KEY `IDX_A0A2C655749678EB` (`enregistreur_id`),
  ADD KEY `IDX_A0A2C655A0FBFEF` (`expiditeur_id`),
  ADD KEY `IDX_A0A2C655C631C63F` (`destinateur_id`);

--
-- Index pour la table `demande_de_sang`
--
ALTER TABLE `demande_de_sang`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_282FD663CEC3FD2F` (`patients_id`),
  ADD KEY `IDX_282FD663CC0FBF92` (`hopital_id`),
  ADD KEY `IDX_282FD663749678EB` (`enregistreur_id`);

--
-- Index pour la table `doctrine_migration_versions`
--
ALTER TABLE `doctrine_migration_versions`
  ADD PRIMARY KEY (`version`);

--
-- Index pour la table `donateur`
--
ALTER TABLE `donateur`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UNIQ_9CD3DE501D398C02` (`phenotypes_id`),
  ADD KEY `IDX_9CD3DE50749678EB` (`enregistreur_id`),
  ADD KEY `IDX_9CD3DE50184937D5` (`banques_id`);

--
-- Index pour la table `dons`
--
ALTER TABLE `dons`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_E4F955FADE789267` (`donateurs_id`),
  ADD KEY `IDX_E4F955FA184937D5` (`banques_id`),
  ADD KEY `IDX_E4F955FA749678EB` (`enregistreur_id`);

--
-- Index pour la table `messenger_messages`
--
ALTER TABLE `messenger_messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_75EA56E0FB7336F0` (`queue_name`),
  ADD KEY `IDX_75EA56E0E3BD61CE` (`available_at`),
  ADD KEY `IDX_75EA56E016BA31DB` (`delivered_at`);

--
-- Index pour la table `patient`
--
ALTER TABLE `patient`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_1ADAD7EBCC0FBF92` (`hopital_id`),
  ADD KEY `IDX_1ADAD7EB749678EB` (`enregistreur_id`);

--
-- Index pour la table `phenotype`
--
ALTER TABLE `phenotype`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UNIQ_482C09B41B5FBCDB` (`donneurs_id`);

--
-- Index pour la table `reset_password_request`
--
ALTER TABLE `reset_password_request`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_7CE748AA76ED395` (`user_id`);

--
-- Index pour la table `stock_derive`
--
ALTER TABLE `stock_derive`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UNIQ_7BDE155B1D398C02` (`phenotypes_id`),
  ADD KEY `IDX_7BDE155B7B3C9061` (`don_id`),
  ADD KEY `IDX_7BDE155B749678EB` (`enregistreur_id`),
  ADD KEY `IDX_7BDE155B184937D5` (`banques_id`);

--
-- Index pour la table `transfert`
--
ALTER TABLE `transfert`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UNIQ_1E4EACBB97CD0554` (`porches_id`),
  ADD KEY `IDX_1E4EACBB37F05903` (`borderau_id`),
  ADD KEY `IDX_1E4EACBB749678EB` (`enregistreur_id`),
  ADD KEY `IDX_1E4EACBBA0FBFEF` (`expiditeur_id`),
  ADD KEY `IDX_1E4EACBBC631C63F` (`destinateur_id`);

--
-- Index pour la table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UNIQ_IDENTIFIER_EMAIL` (`email`),
  ADD KEY `IDX_8D93D649184937D5` (`banques_id`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `agents`
--
ALTER TABLE `agents`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `annalyse`
--
ALTER TABLE `annalyse`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `banque`
--
ALTER TABLE `banque`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `borderaux`
--
ALTER TABLE `borderaux`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `demande_de_sang`
--
ALTER TABLE `demande_de_sang`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `donateur`
--
ALTER TABLE `donateur`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `dons`
--
ALTER TABLE `dons`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `messenger_messages`
--
ALTER TABLE `messenger_messages`
  MODIFY `id` bigint NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `patient`
--
ALTER TABLE `patient`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `phenotype`
--
ALTER TABLE `phenotype`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `reset_password_request`
--
ALTER TABLE `reset_password_request`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `stock_derive`
--
ALTER TABLE `stock_derive`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `transfert`
--
ALTER TABLE `transfert`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `user`
--
ALTER TABLE `user`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `agents`
--
ALTER TABLE `agents`
  ADD CONSTRAINT `FK_9596AB6E184937D5` FOREIGN KEY (`banques_id`) REFERENCES `banque` (`id`),
  ADD CONSTRAINT `FK_9596AB6E67B3B43D` FOREIGN KEY (`users_id`) REFERENCES `user` (`id`);

--
-- Contraintes pour la table `annalyse`
--
ALTER TABLE `annalyse`
  ADD CONSTRAINT `FK_275CDCA5184937D5` FOREIGN KEY (`banques_id`) REFERENCES `banque` (`id`),
  ADD CONSTRAINT `FK_275CDCA5749678EB` FOREIGN KEY (`enregistreur_id`) REFERENCES `agents` (`id`),
  ADD CONSTRAINT `FK_275CDCA57B3C9061` FOREIGN KEY (`don_id`) REFERENCES `dons` (`id`);

--
-- Contraintes pour la table `borderaux`
--
ALTER TABLE `borderaux`
  ADD CONSTRAINT `FK_A0A2C655749678EB` FOREIGN KEY (`enregistreur_id`) REFERENCES `agents` (`id`),
  ADD CONSTRAINT `FK_A0A2C655A0FBFEF` FOREIGN KEY (`expiditeur_id`) REFERENCES `banque` (`id`),
  ADD CONSTRAINT `FK_A0A2C655C631C63F` FOREIGN KEY (`destinateur_id`) REFERENCES `banque` (`id`);

--
-- Contraintes pour la table `demande_de_sang`
--
ALTER TABLE `demande_de_sang`
  ADD CONSTRAINT `FK_282FD663749678EB` FOREIGN KEY (`enregistreur_id`) REFERENCES `agents` (`id`),
  ADD CONSTRAINT `FK_282FD663CC0FBF92` FOREIGN KEY (`hopital_id`) REFERENCES `banque` (`id`),
  ADD CONSTRAINT `FK_282FD663CEC3FD2F` FOREIGN KEY (`patients_id`) REFERENCES `patient` (`id`);

--
-- Contraintes pour la table `donateur`
--
ALTER TABLE `donateur`
  ADD CONSTRAINT `FK_9CD3DE50184937D5` FOREIGN KEY (`banques_id`) REFERENCES `banque` (`id`),
  ADD CONSTRAINT `FK_9CD3DE501D398C02` FOREIGN KEY (`phenotypes_id`) REFERENCES `phenotype` (`id`),
  ADD CONSTRAINT `FK_9CD3DE50749678EB` FOREIGN KEY (`enregistreur_id`) REFERENCES `agents` (`id`);

--
-- Contraintes pour la table `dons`
--
ALTER TABLE `dons`
  ADD CONSTRAINT `FK_E4F955FA184937D5` FOREIGN KEY (`banques_id`) REFERENCES `banque` (`id`),
  ADD CONSTRAINT `FK_E4F955FA749678EB` FOREIGN KEY (`enregistreur_id`) REFERENCES `agents` (`id`),
  ADD CONSTRAINT `FK_E4F955FADE789267` FOREIGN KEY (`donateurs_id`) REFERENCES `donateur` (`id`);

--
-- Contraintes pour la table `patient`
--
ALTER TABLE `patient`
  ADD CONSTRAINT `FK_1ADAD7EB749678EB` FOREIGN KEY (`enregistreur_id`) REFERENCES `agents` (`id`),
  ADD CONSTRAINT `FK_1ADAD7EBCC0FBF92` FOREIGN KEY (`hopital_id`) REFERENCES `banque` (`id`);

--
-- Contraintes pour la table `phenotype`
--
ALTER TABLE `phenotype`
  ADD CONSTRAINT `FK_482C09B41B5FBCDB` FOREIGN KEY (`donneurs_id`) REFERENCES `donateur` (`id`);

--
-- Contraintes pour la table `reset_password_request`
--
ALTER TABLE `reset_password_request`
  ADD CONSTRAINT `FK_7CE748AA76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`);

--
-- Contraintes pour la table `stock_derive`
--
ALTER TABLE `stock_derive`
  ADD CONSTRAINT `FK_7BDE155B184937D5` FOREIGN KEY (`banques_id`) REFERENCES `banque` (`id`),
  ADD CONSTRAINT `FK_7BDE155B1D398C02` FOREIGN KEY (`phenotypes_id`) REFERENCES `phenotype` (`id`),
  ADD CONSTRAINT `FK_7BDE155B749678EB` FOREIGN KEY (`enregistreur_id`) REFERENCES `agents` (`id`),
  ADD CONSTRAINT `FK_7BDE155B7B3C9061` FOREIGN KEY (`don_id`) REFERENCES `dons` (`id`);

--
-- Contraintes pour la table `transfert`
--
ALTER TABLE `transfert`
  ADD CONSTRAINT `FK_1E4EACBB37F05903` FOREIGN KEY (`borderau_id`) REFERENCES `borderaux` (`id`),
  ADD CONSTRAINT `FK_1E4EACBB749678EB` FOREIGN KEY (`enregistreur_id`) REFERENCES `agents` (`id`),
  ADD CONSTRAINT `FK_1E4EACBB97CD0554` FOREIGN KEY (`porches_id`) REFERENCES `stock_derive` (`id`),
  ADD CONSTRAINT `FK_1E4EACBBA0FBFEF` FOREIGN KEY (`expiditeur_id`) REFERENCES `banque` (`id`),
  ADD CONSTRAINT `FK_1E4EACBBC631C63F` FOREIGN KEY (`destinateur_id`) REFERENCES `banque` (`id`);

--
-- Contraintes pour la table `user`
--
ALTER TABLE `user`
  ADD CONSTRAINT `FK_8D93D649184937D5` FOREIGN KEY (`banques_id`) REFERENCES `banque` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;