-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: pettisco
-- ------------------------------------------------------
-- Server version	10.4.32-MariaDB

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
-- Table structure for table `agendamentos`
--

DROP TABLE IF EXISTS `agendamentos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `agendamentos` (
  `id_agendamento` int(11) NOT NULL AUTO_INCREMENT,
  `id_user` int(11) DEFAULT NULL,
  `id_servico` int(11) DEFAULT NULL,
  `data` date DEFAULT NULL,
  `horario` time DEFAULT NULL,
  `status` tinyint(1) DEFAULT 1,
  `id_pet` int(11) DEFAULT NULL,
  PRIMARY KEY (`id_agendamento`),
  UNIQUE KEY `uk_agendamento_horario` (`data`,`horario`),
  KEY `fk_agendamento_user` (`id_user`),
  KEY `fk_agendamento_pet` (`id_pet`),
  KEY `fk_agendamento_servico` (`id_servico`),
  CONSTRAINT `fk_agendamento_pet` FOREIGN KEY (`id_pet`) REFERENCES `pet` (`id_pet`),
  CONSTRAINT `fk_agendamento_servico` FOREIGN KEY (`id_servico`) REFERENCES `servico` (`id_servico`),
  CONSTRAINT `fk_agendamento_user` FOREIGN KEY (`id_user`) REFERENCES `user` (`id_user`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `agendamentos`
--

LOCK TABLES `agendamentos` WRITE;
/*!40000 ALTER TABLE `agendamentos` DISABLE KEYS */;
/*!40000 ALTER TABLE `agendamentos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `especie`
--

DROP TABLE IF EXISTS `especie`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `especie` (
  `id_especie` int(11) NOT NULL AUTO_INCREMENT,
  `nome_especie` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id_especie`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `especie`
--

LOCK TABLES `especie` WRITE;
/*!40000 ALTER TABLE `especie` DISABLE KEYS */;
INSERT INTO `especie` VALUES (1,'Cachorro'),(2,'Gato'),(3,'Coelho'),(4,'Hamster'),(5,'Ave');
/*!40000 ALTER TABLE `especie` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `item_compra`
--

DROP TABLE IF EXISTS `item_compra`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `item_compra` (
  `id_item` int(11) NOT NULL AUTO_INCREMENT,
  `id_pedido` int(11) NOT NULL,
  `id_produto` int(11) NOT NULL,
  `quantidade` int(11) NOT NULL,
  `preco_unitario` decimal(10,2) NOT NULL,
  `subtotal` decimal(10,2) GENERATED ALWAYS AS (`quantidade` * `preco_unitario`) STORED,
  PRIMARY KEY (`id_item`),
  KEY `fk_item_pedido` (`id_pedido`),
  KEY `fk_item_produto` (`id_produto`),
  CONSTRAINT `fk_item_pedido` FOREIGN KEY (`id_pedido`) REFERENCES `pedido` (`id_pedido`),
  CONSTRAINT `fk_item_produto` FOREIGN KEY (`id_produto`) REFERENCES `produtos` (`id_produto`),
  CONSTRAINT `chk_item_quantidade` CHECK (`quantidade` > 0),
  CONSTRAINT `chk_item_preco` CHECK (`preco_unitario` >= 0)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `item_compra`
--

LOCK TABLES `item_compra` WRITE;
/*!40000 ALTER TABLE `item_compra` DISABLE KEYS */;
/*!40000 ALTER TABLE `item_compra` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pedido`
--

DROP TABLE IF EXISTS `pedido`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pedido` (
  `id_pedido` int(11) NOT NULL AUTO_INCREMENT,
  `id_user` int(11) DEFAULT NULL,
  `data_compra` date DEFAULT NULL,
  `status` tinyint(1) DEFAULT 1,
  `valor_total` decimal(9,2) DEFAULT NULL,
  PRIMARY KEY (`id_pedido`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pedido`
--

LOCK TABLES `pedido` WRITE;
/*!40000 ALTER TABLE `pedido` DISABLE KEYS */;
/*!40000 ALTER TABLE `pedido` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pet`
--

DROP TABLE IF EXISTS `pet`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pet` (
  `id_pet` int(11) NOT NULL AUTO_INCREMENT,
  `id_user` int(11) DEFAULT NULL,
  `nome` varchar(255) NOT NULL,
  `id_especie` int(11) DEFAULT NULL,
  `id_raca` int(11) DEFAULT NULL,
  `foto_pet_url` varchar(255) NOT NULL,
  `peso` decimal(5,2) DEFAULT NULL,
  PRIMARY KEY (`id_pet`),
  KEY `fk_pet_especie` (`id_especie`),
  KEY `id_raca` (`id_raca`),
  KEY `fk_pet_user` (`id_user`),
  CONSTRAINT `fk_pet_especie` FOREIGN KEY (`id_especie`) REFERENCES `especie` (`id_especie`),
  CONSTRAINT `fk_pet_user` FOREIGN KEY (`id_user`) REFERENCES `user` (`id_user`),
  CONSTRAINT `id_raca` FOREIGN KEY (`id_raca`) REFERENCES `raca` (`id_raca`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pet`
--

LOCK TABLES `pet` WRITE;
/*!40000 ALTER TABLE `pet` DISABLE KEYS */;
/*!40000 ALTER TABLE `pet` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `porte`
--

DROP TABLE IF EXISTS `porte`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `porte` (
  `id_porte` int(11) NOT NULL AUTO_INCREMENT,
  `nome_porte` varchar(50) NOT NULL,
  PRIMARY KEY (`id_porte`),
  UNIQUE KEY `nome_porte` (`nome_porte`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `porte`
--

LOCK TABLES `porte` WRITE;
/*!40000 ALTER TABLE `porte` DISABLE KEYS */;
INSERT INTO `porte` VALUES (3,'Grande'),(2,'M?dio'),(1,'Pequeno');
/*!40000 ALTER TABLE `porte` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `produtos`
--

DROP TABLE IF EXISTS `produtos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `produtos` (
  `id_produto` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(255) NOT NULL,
  `descricao` text DEFAULT NULL,
  `quantidade` int(11) NOT NULL DEFAULT 0,
  `preco` decimal(10,2) NOT NULL,
  `tipo` varchar(100) NOT NULL,
  `img_url` varchar(255) DEFAULT NULL,
  `ativo` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id_produto`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `produtos`
--

LOCK TABLES `produtos` WRITE;
/*!40000 ALTER TABLE `produtos` DISABLE KEYS */;
/*!40000 ALTER TABLE `produtos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `raca`
--

DROP TABLE IF EXISTS `raca`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `raca` (
  `id_raca` int(11) NOT NULL AUTO_INCREMENT,
  `nome_raca` varchar(255) NOT NULL,
  `id_especie` int(11) DEFAULT NULL,
  `id_porte` int(11) DEFAULT NULL,
  PRIMARY KEY (`id_raca`),
  KEY `id_especie` (`id_especie`),
  KEY `fk_raca_porte` (`id_porte`),
  CONSTRAINT `fk_raca_porte` FOREIGN KEY (`id_porte`) REFERENCES `porte` (`id_porte`),
  CONSTRAINT `id_especie` FOREIGN KEY (`id_especie`) REFERENCES `especie` (`id_especie`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `raca`
--

LOCK TABLES `raca` WRITE;
/*!40000 ALTER TABLE `raca` DISABLE KEYS */;
INSERT INTO `raca` VALUES (1,'Shih-tzu',1,1),(2,'Pinscher',1,1),(3,'Poodle',1,2),(4,'Labrador',1,3),(5,'Golden Retriever',1,3),(6,'Bulldog Franc?s',1,2),(7,'Persa',2,1),(8,'Siam?s',2,1),(9,'Maine Coon',2,3),(10,'Coelho An?o',3,1);
/*!40000 ALTER TABLE `raca` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `servico`
--

DROP TABLE IF EXISTS `servico`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `servico` (
  `id_servico` int(11) NOT NULL AUTO_INCREMENT,
  `nome_servico` varchar(255) NOT NULL,
  `descricao` text DEFAULT NULL,
  PRIMARY KEY (`id_servico`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `servico`
--

LOCK TABLES `servico` WRITE;
/*!40000 ALTER TABLE `servico` DISABLE KEYS */;
/*!40000 ALTER TABLE `servico` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user`
--

DROP TABLE IF EXISTS `user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user` (
  `id_user` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `email` varchar(255) NOT NULL,
  `senha` varchar(64) NOT NULL,
  `foto_url` varchar(255) DEFAULT NULL,
  `adm` tinyint(1) DEFAULT 0,
  `criado_em` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id_user`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user`
--

LOCK TABLES `user` WRITE;
/*!40000 ALTER TABLE `user` DISABLE KEYS */;
/*!40000 ALTER TABLE `user` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `valor_servico`
--

DROP TABLE IF EXISTS `valor_servico`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `valor_servico` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_servico` int(11) NOT NULL,
  `id_porte` int(11) NOT NULL,
  `valor` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_servico_porte` (`id_servico`,`id_porte`),
  KEY `fk_valor_porte` (`id_porte`),
  CONSTRAINT `fk_valor_porte` FOREIGN KEY (`id_porte`) REFERENCES `porte` (`id_porte`),
  CONSTRAINT `fk_valor_servico` FOREIGN KEY (`id_servico`) REFERENCES `servico` (`id_servico`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `valor_servico`
--

LOCK TABLES `valor_servico` WRITE;
/*!40000 ALTER TABLE `valor_servico` DISABLE KEYS */;
/*!40000 ALTER TABLE `valor_servico` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-11-09 14:40:17
