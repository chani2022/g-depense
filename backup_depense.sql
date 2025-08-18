-- MariaDB dump 10.19  Distrib 10.10.2-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: depense
-- ------------------------------------------------------
-- Server version	10.10.2-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `capital`
--

DROP TABLE IF EXISTS `capital`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `capital` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `compte_salaire_id` int(11) NOT NULL,
  `montant` double DEFAULT NULL,
  `ajout` double DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_307CBAA6EE6C183F` (`compte_salaire_id`),
  CONSTRAINT `FK_307CBAA6EE6C183F` FOREIGN KEY (`compte_salaire_id`) REFERENCES `compte_salaire` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `capital`
--

LOCK TABLES `capital` WRITE;
/*!40000 ALTER TABLE `capital` DISABLE KEYS */;
INSERT INTO `capital` VALUES
(1,1,1500.75,NULL),
(2,1,NULL,15.25),
(3,3,200.75,NULL);
/*!40000 ALTER TABLE `capital` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `category`
--

DROP TABLE IF EXISTS `category`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `category` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nom` varchar(255) NOT NULL,
  `owner_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_64C19C17E3C61F9` (`owner_id`),
  CONSTRAINT `FK_64C19C17E3C61F9` FOREIGN KEY (`owner_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `category`
--

LOCK TABLES `category` WRITE;
/*!40000 ALTER TABLE `category` DISABLE KEYS */;
INSERT INTO `category` VALUES
(1,'riz',1),
(2,'alreadyExist',1),
(3,'jean',2);
/*!40000 ALTER TABLE `category` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `compte_salaire`
--

DROP TABLE IF EXISTS `compte_salaire`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `compte_salaire` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `owner_id` int(11) NOT NULL,
  `date_debut_compte` datetime NOT NULL,
  `date_fin_compte` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_83AC81477E3C61F9` (`owner_id`),
  CONSTRAINT `FK_83AC81477E3C61F9` FOREIGN KEY (`owner_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `compte_salaire`
--

LOCK TABLES `compte_salaire` WRITE;
/*!40000 ALTER TABLE `compte_salaire` DISABLE KEYS */;
INSERT INTO `compte_salaire` VALUES
(1,1,'2024-01-01 00:00:00','2024-01-15 00:00:00'),
(2,1,'2025-08-07 06:04:28','2025-08-21 06:04:28'),
(3,2,'2024-02-16 00:00:00','2024-03-01 00:00:00'),
(4,1,'2024-01-16 00:00:00','2024-01-30 00:00:00'),
(5,1,'2024-02-01 00:00:00','2024-02-15 00:00:00');
/*!40000 ALTER TABLE `compte_salaire` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `depense`
--

DROP TABLE IF EXISTS `depense`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `depense` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `compte_salaire_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `unite_id` int(11) NOT NULL,
  `nom_depense` varchar(255) NOT NULL,
  `prix` double NOT NULL,
  `vital` tinyint(1) NOT NULL DEFAULT 0,
  `quantite` double NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_34059757EE6C183F` (`compte_salaire_id`),
  KEY `IDX_3405975712469DE2` (`category_id`),
  CONSTRAINT `FK_3405975712469DE2` FOREIGN KEY (`category_id`) REFERENCES `category` (`id`),
  CONSTRAINT `FK_34059757EE6C183F` FOREIGN KEY (`compte_salaire_id`) REFERENCES `compte_salaire` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `depense`
--

LOCK TABLES `depense` WRITE;
/*!40000 ALTER TABLE `depense` DISABLE KEYS */;
INSERT INTO `depense` VALUES
(1,1,1,1,'depense 1',15.25,0,0),
(2,2,2,1,'depense 2',25.25,0,0),
(3,3,3,2,'',100.25,0,0);
/*!40000 ALTER TABLE `depense` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `doctrine_migration_versions`
--

DROP TABLE IF EXISTS `doctrine_migration_versions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `doctrine_migration_versions` (
  `version` varchar(191) NOT NULL,
  `executed_at` datetime DEFAULT NULL,
  `execution_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`version`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `doctrine_migration_versions`
--

LOCK TABLES `doctrine_migration_versions` WRITE;
/*!40000 ALTER TABLE `doctrine_migration_versions` DISABLE KEYS */;
INSERT INTO `doctrine_migration_versions` VALUES
('DoctrineMigrations\\Version20250627165637','2025-08-13 15:02:08',360),
('DoctrineMigrations\\Version20250702121629','2025-08-13 15:02:09',286),
('DoctrineMigrations\\Version20250714103918','2025-08-13 15:02:09',505),
('DoctrineMigrations\\Version20250721085124','2025-08-13 15:02:10',534),
('DoctrineMigrations\\Version20250722100804','2025-08-13 15:02:10',160),
('DoctrineMigrations\\Version20250722111039','2025-08-13 15:02:10',927),
('DoctrineMigrations\\Version20250722111848','2025-08-13 15:02:11',260),
('DoctrineMigrations\\Version20250722140618','2025-08-13 15:02:12',885),
('DoctrineMigrations\\Version20250731123529','2025-08-13 15:02:12',561),
('DoctrineMigrations\\Version20250801122340','2025-08-13 15:02:13',1002),
('DoctrineMigrations\\Version20250813073012','2025-08-13 15:02:14',251),
('DoctrineMigrations\\Version20250813141454','2025-08-13 15:02:14',259),
('DoctrineMigrations\\Version20250813145802','2025-08-13 15:02:15',961),
('DoctrineMigrations\\Version20250813151323','2025-08-13 15:13:45',391),
('DoctrineMigrations\\Version20250814062853','2025-08-14 06:29:06',604),
('DoctrineMigrations\\Version20250814063237','2025-08-14 06:32:57',324),
('DoctrineMigrations\\Version20250814071355','2025-08-14 07:14:04',316);
/*!40000 ALTER TABLE `doctrine_migration_versions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `messenger_messages`
--

DROP TABLE IF EXISTS `messenger_messages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `messenger_messages` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `body` longtext NOT NULL,
  `headers` longtext NOT NULL,
  `queue_name` varchar(190) NOT NULL,
  `created_at` datetime NOT NULL,
  `available_at` datetime NOT NULL,
  `delivered_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_75EA56E0FB7336F0` (`queue_name`),
  KEY `IDX_75EA56E0E3BD61CE` (`available_at`),
  KEY `IDX_75EA56E016BA31DB` (`delivered_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `messenger_messages`
--

LOCK TABLES `messenger_messages` WRITE;
/*!40000 ALTER TABLE `messenger_messages` DISABLE KEYS */;
/*!40000 ALTER TABLE `messenger_messages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `unite`
--

DROP TABLE IF EXISTS `unite`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `unite` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `owner_id` int(11) NOT NULL,
  `unite` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_1D64C1187E3C61F9` (`owner_id`),
  CONSTRAINT `FK_1D64C1187E3C61F9` FOREIGN KEY (`owner_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `unite`
--

LOCK TABLES `unite` WRITE;
/*!40000 ALTER TABLE `unite` DISABLE KEYS */;
/*!40000 ALTER TABLE `unite` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user`
--

DROP TABLE IF EXISTS `user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(180) NOT NULL,
  `roles` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL COMMENT '(DC2Type:json)' CHECK (json_valid(`roles`)),
  `password` varchar(255) NOT NULL,
  `nom` varchar(255) DEFAULT NULL,
  `prenom` varchar(255) DEFAULT NULL,
  `image_name` varchar(255) DEFAULT NULL,
  `image_size` int(11) DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)',
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_8D93D649F85E0677` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user`
--

LOCK TABLES `user` WRITE;
/*!40000 ALTER TABLE `user` DISABLE KEYS */;
INSERT INTO `user` VALUES
(1,'username','[]','$2y$13$t6TO/GRU.QYrNoOzfzbry.JQShYJHdJ/VKspEuoTTI90Ln99ZSDXO',NULL,NULL,NULL,NULL,NULL),
(2,'username 1','[]','$2y$13$t6TO/GRU.QYrNoOzfzbry.JQShYJHdJ/VKspEuoTTI90Ln99ZSDXO',NULL,NULL,NULL,NULL,NULL),
(3,'admin','[\"ROLE_ADMIN\"]','$2y$13$t6TO/GRU.QYrNoOzfzbry.JQShYJHdJ/VKspEuoTTI90Ln99ZSDXO',NULL,NULL,NULL,NULL,NULL);
/*!40000 ALTER TABLE `user` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-08-18 11:09:50
