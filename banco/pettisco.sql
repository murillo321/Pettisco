-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Linux (x86_64)
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
  KEY `fk_agendamento_servico` (`id_servico`),
  KEY `fk_agendamento_pet` (`id_pet`),
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
-- Table structure for table `categoria`
--

DROP TABLE IF EXISTS `categoria`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `categoria` (
  `id_categoria` int(11) NOT NULL AUTO_INCREMENT,
  `identificador` varchar(120) NOT NULL,
  `nome_categoria` varchar(120) NOT NULL,
  `descricao` text DEFAULT NULL,
  `id_especie` int(11) DEFAULT NULL,
  `categoria_pai_id` int(11) DEFAULT NULL,
  `ordem_exibicao` int(11) NOT NULL DEFAULT 0,
  `ativo` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id_categoria`),
  UNIQUE KEY `uk_categoria_identificador` (`identificador`),
  KEY `fk_categoria_especie` (`id_especie`),
  KEY `fk_categoria_pai` (`categoria_pai_id`),
  CONSTRAINT `fk_categoria_especie` FOREIGN KEY (`id_especie`) REFERENCES `especie` (`id_especie`),
  CONSTRAINT `fk_categoria_pai` FOREIGN KEY (`categoria_pai_id`) REFERENCES `categoria` (`id_categoria`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `categoria`
--

LOCK TABLES `categoria` WRITE;
/*!40000 ALTER TABLE `categoria` DISABLE KEYS */;
INSERT INTO `categoria` VALUES (1,'racoes-cachorro','Rações para Cachorros','Alimentos secos e úmidos para cães',1,NULL,1,1),(2,'brinquedos-cachorro','Brinquedos para Cachorros','Bolas, mordedores e brinquedos interativos',1,NULL,2,1),(3,'higiene-cachorro','Higiene para Cachorros','Shampoos, tapetes higiênicos e cuidados',1,NULL,3,1),(4,'acessorios-cachorro','Acessórios para Cachorros','Coleiras, guias e acessórios',1,NULL,4,1),(5,'racoes-gato','Rações para Gatos','Alimentos secos e úmidos para felinos',2,NULL,5,1),(6,'brinquedos-gato','Brinquedos para Gatos','Arranhadores e brinquedos interativos',2,NULL,6,1),(7,'higiene-gato','Higiene para Gatos','Areias sanitárias e produtos de higiene',2,NULL,7,1),(8,'acessorios-gato','Acessórios para Gatos','Camas, caixas de transporte e coleiras',2,NULL,8,1),(9,'alimentacao-coelho','Alimentação para Coelhos','Rações, fenos e alimentos para coelhos',3,NULL,9,1),(10,'acessorios-coelho','Acessórios para Coelhos','Comedouros e brinquedos para coelhos',3,NULL,10,1),(11,'alimentacao-hamster','Alimentação para Hamsters','Rações e misturas de sementes',4,NULL,11,1),(12,'acessorios-hamster','Acessórios para Hamsters','Rodas, túneis e brinquedos',4,NULL,12,1),(13,'alimentacao-ave','Alimentação para Aves','Rações e sementes para pássaros',5,NULL,13,1),(14,'brinquedos-ave','Brinquedos para Aves','Balanços e brinquedos para pássaros',5,NULL,14,1),(15,'acessorios-ave','Acessórios para Aves','Poleiros, comedouros e acessórios',5,NULL,15,1);
/*!40000 ALTER TABLE `categoria` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `convite_adm`
--

DROP TABLE IF EXISTS `convite_adm`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `convite_adm` (
  `id_convite` int(11) NOT NULL AUTO_INCREMENT,
  `codigo_hash` char(64) NOT NULL,
  `cpf_convidado` varchar(14) NOT NULL,
  `id_adm_criador` int(11) NOT NULL,
  `id_adm_cadastrado` int(11) DEFAULT NULL,
  `criado_em` timestamp NOT NULL DEFAULT current_timestamp(),
  `expira_em` datetime NOT NULL,
  `usado_em` datetime DEFAULT NULL,
  `revogado_em` datetime DEFAULT NULL,
  PRIMARY KEY (`id_convite`),
  UNIQUE KEY `uk_convite_codigo` (`codigo_hash`),
  KEY `fk_convite_criador` (`id_adm_criador`),
  KEY `fk_convite_cadastrado` (`id_adm_cadastrado`),
  CONSTRAINT `fk_convite_cadastrado` FOREIGN KEY (`id_adm_cadastrado`) REFERENCES `user` (`id_user`),
  CONSTRAINT `fk_convite_criador` FOREIGN KEY (`id_adm_criador`) REFERENCES `user` (`id_user`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `convite_adm`
--

LOCK TABLES `convite_adm` WRITE;
/*!40000 ALTER TABLE `convite_adm` DISABLE KEYS */;
INSERT INTO `convite_adm` VALUES (1,'3abc879c','860.313.520-79',3,4,'2026-11-22 15:14:55','2026-12-31 23:59:59','2026-11-22 12:22:43',NULL);
/*!40000 ALTER TABLE `convite_adm` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `especie`
--

DROP TABLE IF EXISTS `especie`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `especie` (
  `id_especie` int(11) NOT NULL AUTO_INCREMENT,
  `nome_especie` varchar(255) NOT NULL,
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
  `id_itens_comprados` text DEFAULT NULL,
  PRIMARY KEY (`id_pedido`),
  KEY `fk_pedido_user` (`id_user`),
  CONSTRAINT `fk_pedido_user` FOREIGN KEY (`id_user`) REFERENCES `user` (`id_user`)
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
  KEY `fk_pet_user` (`id_user`),
  KEY `fk_pet_especie` (`id_especie`),
  KEY `fk_pet_raca` (`id_raca`),
  CONSTRAINT `fk_pet_especie` FOREIGN KEY (`id_especie`) REFERENCES `especie` (`id_especie`),
  CONSTRAINT `fk_pet_raca` FOREIGN KEY (`id_raca`) REFERENCES `raca` (`id_raca`),
  CONSTRAINT `fk_pet_user` FOREIGN KEY (`id_user`) REFERENCES `user` (`id_user`)
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
  UNIQUE KEY `uk_nome_porte` (`nome_porte`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `porte`
--

LOCK TABLES `porte` WRITE;
/*!40000 ALTER TABLE `porte` DISABLE KEYS */;
INSERT INTO `porte` VALUES (3,'Grande'),(2,'Médio'),(1,'Pequeno');
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
  `preco_venda` decimal(10,2) NOT NULL,
  `preco_custo` decimal(10,2) NOT NULL,
  `tipo` varchar(100) NOT NULL,
  `id_categoria` int(11) DEFAULT NULL,
  `img_url` varchar(255) DEFAULT NULL,
  `ativo` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id_produto`),
  KEY `fk_produto_categoria` (`id_categoria`),
  CONSTRAINT `fk_produto_categoria` FOREIGN KEY (`id_categoria`) REFERENCES `categoria` (`id_categoria`),
  CONSTRAINT `chk_produto_quantidade` CHECK (`quantidade` >= 0),
  CONSTRAINT `chk_produto_preco` CHECK (`preco_venda` >= 0)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `produtos`
--

LOCK TABLES `produtos` WRITE;
/*!40000 ALTER TABLE `produtos` DISABLE KEYS */;
INSERT INTO `produtos` VALUES (1,'Ração Premium Cães Adultos 10kg','Ração completa para cães adultos de todos os portes.',45,189.90,125.00,'Ração',1,NULL,1),(2,'Ração Filhotes Frango 3kg','Alimento balanceado para cães filhotes.',30,79.90,48.00,'Ração',1,NULL,1),(3,'Bola de Borracha Resistente','Bola de borracha para brincadeiras e exercícios.',80,24.90,9.50,'Brinquedo',2,NULL,1),(4,'Mordedor de Corda Colorido','Brinquedo de corda para cães de pequeno e médio porte.',60,19.90,7.00,'Brinquedo',2,NULL,1),(5,'Shampoo Neutro para Cães 500ml','Shampoo suave para limpeza e higiene de cães.',35,29.90,12.50,'Higiene',3,NULL,1),(6,'Coleira Ajustável Azul','Coleira confortável com regulagem de tamanho.',50,39.90,15.00,'Acessório',4,NULL,1),(7,'Ração Premium Gatos Castrados 3kg','Ração balanceada para gatos adultos castrados.',40,119.90,78.00,'Ração',5,NULL,1),(8,'Sachê de Frango para Gatos 85g','Alimento úmido sabor frango para gatos adultos.',120,5.90,2.80,'Alimento Úmido',5,NULL,1),(9,'Arranhador de Papelão','Arranhador compacto para gatos.',25,49.90,21.00,'Brinquedo',6,NULL,1),(10,'Varinha com Penas para Gatos','Brinquedo interativo para estimular a atividade física.',70,17.90,5.50,'Brinquedo',6,NULL,1),(11,'Areia Sanitária 4kg','Areia absorvente para caixas sanitárias de gatos.',55,32.90,17.00,'Higiene',7,NULL,1),(12,'Cama Redonda para Gatos','Cama macia e confortável para descanso.',20,89.90,43.00,'Acessório',8,NULL,1),(13,'Ração para Coelhos 1kg','Alimento extrusado para coelhos domésticos.',30,34.90,18.00,'Ração',9,NULL,1),(14,'Feno Natural para Coelhos 500g','Feno natural para alimentação de coelhos.',35,22.90,10.00,'Alimento',9,NULL,1),(15,'Comedouro Cerâmico para Coelhos','Comedouro resistente e fácil de higienizar.',18,35.90,14.00,'Acessório',10,NULL,1),(16,'Mix de Sementes para Hamsters 500g','Mistura de grãos e sementes para hamsters.',40,18.90,8.00,'Alimento',11,NULL,1),(17,'Roda de Exercícios para Hamsters','Roda para estimular exercícios físicos.',25,45.90,19.00,'Acessório',12,NULL,1),(18,'Mistura de Sementes para Aves 1kg','Mistura de sementes para aves domésticas.',45,24.90,11.00,'Alimento',13,NULL,1),(19,'Balanço de Madeira para Aves','Brinquedo suspenso para aves de pequeno porte.',22,29.90,12.00,'Brinquedo',14,NULL,1),(20,'Poleiro Natural para Aves','Poleiro de madeira para gaiolas.',30,19.90,7.50,'Acessório',15,NULL,1);
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
  KEY `fk_raca_especie` (`id_especie`),
  KEY `fk_raca_porte` (`id_porte`),
  CONSTRAINT `fk_raca_especie` FOREIGN KEY (`id_especie`) REFERENCES `especie` (`id_especie`),
  CONSTRAINT `fk_raca_porte` FOREIGN KEY (`id_porte`) REFERENCES `porte` (`id_porte`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `raca`
--

LOCK TABLES `raca` WRITE;
/*!40000 ALTER TABLE `raca` DISABLE KEYS */;
INSERT INTO `raca` VALUES (1,'Shih-tzu',1,1),(2,'Pinscher',1,1),(3,'Poodle',1,2),(4,'Labrador',1,3),(5,'Golden Retriever',1,3),(6,'Bulldog Francês',1,2),(7,'Persa',2,1),(8,'Siamês',2,1),(9,'Maine Coon',2,3),(10,'Coelho Anão',3,1);
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
  `cpf` varchar(14) NOT NULL,
  `senha` varchar(255) NOT NULL,
  `foto_url` varchar(255) DEFAULT NULL,
  `adm` tinyint(1) NOT NULL DEFAULT 0,
  `criado_em` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id_user`),
  UNIQUE KEY `cpf` (`cpf`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user`
--

LOCK TABLES `user` WRITE;
/*!40000 ALTER TABLE `user` DISABLE KEYS */;
INSERT INTO `user` VALUES (1,'Caio Barile','adm@adm.adm','12345678909','$2y$10$LTjWeueJYoWtrtB3OghDh.HvffKfS84KVr6B5o6x1Q2B61nADdRAa',NULL,0,'2026-11-20 20:17:20'),(2,'Murillo Barboza de Oliveira','murillo.oliveira212@gmail.com','453.973.164-58','$2y$10$7MMYR9wLKm0/elPiRwarmOwXs3gdYCVpVOo9yB3PYvanyAUsssmKi',NULL,0,'2026-11-21 15:01:41'),(3,'Adm Supremo','admsupremo@adm.com','676.676.676-67','$2y$10$FMVa4/0ELDUdCzHjZ6SzQu24Vmp8Xrx2QgP1/UMeXUYcZkADcsLk6',NULL,1,'2026-11-21 19:04:21'),(4,'Miguel Campos','miguel@gmail.com','860.313.520-79','$2y$10$yAq36O3jnqDAeMnIj0vkue7GcM0/qz6Ltmq9IBwTgpFlnOVC66j8u',NULL,1,'2026-11-22 15:22:43'),(5,'Gabriel Viado','gabriel@gmail.com','525.751.902-07','$2y$10$jRT8hBnm.V5oCfwCb4ltce..fp2JIIuGcGv0s.5nngu1RjvVFgg46',NULL,0,'2026-11-22 15:27:02');
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

-- Dump completed on 2026-11-22 21:31:03
