-- MySQL dump 10.13  Distrib 8.0.30, for Win64 (x86_64)
--
-- Host: localhost    Database: ngonser_db
-- ------------------------------------------------------
-- Server version	8.0.30

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `backup_log`
--

DROP TABLE IF EXISTS `backup_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `backup_log` (
  `id` int NOT NULL AUTO_INCREMENT,
  `filename` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tipe` enum('manual','otomatis') COLLATE utf8mb4_unicode_ci DEFAULT 'manual',
  `ukuran_kb` int DEFAULT NULL,
  `created_by` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `backup_log`
--

LOCK TABLES `backup_log` WRITE;
/*!40000 ALTER TABLE `backup_log` DISABLE KEYS */;
INSERT INTO `backup_log` VALUES (1,'backup_ngonser_db_2026-06-06_07-18-30.sql','manual',29,'Administrator','2026-06-06 14:18:30'),(3,'backup_2026-06-03_12-14.sql','otomatis',20,'Task Scheduler','2026-06-03 12:14:00'),(6,'backup_ngonser_db_2026-06-06_07-46-34.sql','manual',29,'Administrator','2026-06-06 14:46:35');
/*!40000 ALTER TABLE `backup_log` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jenis_bayar`
--

DROP TABLE IF EXISTS `jenis_bayar`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `jenis_bayar` (
  `id` int NOT NULL DEFAULT '0',
  `kode_transaksi` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_harga` decimal(14,2) NOT NULL,
  `metode_bayar` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jenis_bayar`
--

LOCK TABLES `jenis_bayar` WRITE;
/*!40000 ALTER TABLE `jenis_bayar` DISABLE KEYS */;
INSERT INTO `jenis_bayar` VALUES (1,'TRX-20250101-001',5000000.00,'transfer_bank'),(2,'TRX-20250101-002',2250000.00,'gopay'),(3,'TRX-20250102-001',1500000.00,'ovo'),(4,'TRX-20250102-002',2400000.00,'transfer_bank'),(5,'TRX-20250103-001',1400000.00,'dana'),(6,'TRX-20250103-002',900000.00,'gopay'),(7,'TRX-20250104-001',3000000.00,'transfer_bank'),(8,'TRX-20260603-00057411',12500000.00,'gopay'),(9,'TRX-20260603-00055292',2500000.00,'gopay'),(10,'TRX-20260603-00057707',5000000.00,'gopay'),(11,'TRX-20260603-00051360',7500000.00,'transfer_bank');
/*!40000 ALTER TABLE `jenis_bayar` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `konser`
--

DROP TABLE IF EXISTS `konser`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `konser` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nama_konser` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `artis` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `venue` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `kota` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tanggal_konser` date NOT NULL,
  `jam_mulai` time NOT NULL,
  `deskripsi` text COLLATE utf8mb4_unicode_ci,
  `poster` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT 'default.jpg',
  `status` enum('upcoming','ongoing','selesai','batal') COLLATE utf8mb4_unicode_ci DEFAULT 'upcoming',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `konser`
--

LOCK TABLES `konser` WRITE;
/*!40000 ALTER TABLE `konser` DISABLE KEYS */;
INSERT INTO `konser` VALUES (1,'Konser Spektakuler 2025','Dewa 19','Stadion Gelora Bung Karno','Jakarta','2025-08-15','19:00:00','Konser reuni Dewa 19 yang spektakuler!','default.jpg','upcoming','2026-06-06 11:04:16'),(2,'Soundrenaline 2025','Various Artist','Garuda Wisnu Kencana','Bali','2025-09-20','15:00:00','Festival musik terbesar di Asia Tenggara.','default.jpg','upcoming','2026-06-06 11:04:16'),(3,'Synchronize Fest','Various Artist','Gambir Expo','Jakarta','2025-10-03','14:00:00','Festival musik indie terbesar di Indonesia.','default.jpg','upcoming','2026-06-06 11:04:16'),(4,'Noah World Tour','NOAH','Istora Senayan','Jakarta','2025-07-10','20:00:00','Tur dunia NOAH kembali hadir di Jakarta.','default.jpg','selesai','2026-06-06 11:04:16'),(5,'Java Jazz Festival','Various Artist','Jakarta International Expo','Jakarta','2025-03-01','12:00:00','Pertemuan jazz internasional tahunan.','default.jpg','selesai','2026-06-06 11:04:16');
/*!40000 ALTER TABLE `konser` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `konser_jakarta`
--

DROP TABLE IF EXISTS `konser_jakarta`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `konser_jakarta` (
  `id` int NOT NULL DEFAULT '0',
  `nama_konser` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `artis` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `venue` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `kota` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tanggal_konser` date NOT NULL,
  `jam_mulai` time NOT NULL,
  `deskripsi` text COLLATE utf8mb4_unicode_ci,
  `poster` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT 'default.jpg',
  `status` enum('upcoming','ongoing','selesai','batal') COLLATE utf8mb4_unicode_ci DEFAULT 'upcoming',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `konser_jakarta`
--

LOCK TABLES `konser_jakarta` WRITE;
/*!40000 ALTER TABLE `konser_jakarta` DISABLE KEYS */;
INSERT INTO `konser_jakarta` VALUES (1,'Konser Spektakuler 2025','Dewa 19','Stadion Gelora Bung Karno','Jakarta','2025-08-15','19:00:00','Konser reuni Dewa 19 yang spektakuler!','default.jpg','upcoming','2026-06-06 11:04:16'),(3,'Synchronize Fest','Various Artist','Gambir Expo','Jakarta','2025-10-03','14:00:00','Festival musik indie terbesar di Indonesia.','default.jpg','upcoming','2026-06-06 11:04:16'),(4,'Noah World Tour','NOAH','Istora Senayan','Jakarta','2025-07-10','20:00:00','Tur dunia NOAH kembali hadir di Jakarta.','default.jpg','selesai','2026-06-06 11:04:16'),(5,'Java Jazz Festival','Various Artist','Jakarta International Expo','Jakarta','2025-03-01','12:00:00','Pertemuan jazz internasional tahunan.','default.jpg','selesai','2026-06-06 11:04:16');
/*!40000 ALTER TABLE `konser_jakarta` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `log_transaksi`
--

DROP TABLE IF EXISTS `log_transaksi`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `log_transaksi` (
  `id` int NOT NULL AUTO_INCREMENT,
  `transaksi_id` int DEFAULT NULL,
  `kode_transaksi` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_id` int DEFAULT NULL,
  `aksi` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `keterangan` text COLLATE utf8mb4_unicode_ci,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `log_transaksi`
--

LOCK TABLES `log_transaksi` WRITE;
/*!40000 ALTER TABLE `log_transaksi` DISABLE KEYS */;
INSERT INTO `log_transaksi` VALUES (1,8,'TRX-20260603-00057411',5,'BOOKING_CREATED','Booking 5 tiket, total Rp 12500000.00','2026-06-03 19:08:54'),(2,9,'TRX-20260603-00055292',5,'BOOKING_CREATED','Booking 1 tiket, total Rp 2500000.00','2026-06-03 19:09:02'),(3,10,'TRX-20260603-00057707',5,'BOOKING_CREATED','Booking 2 tiket, total Rp 5000000.00','2026-06-03 19:09:14'),(4,10,'TRX-20260603-00057707',5,'PAYMENT_CONFIRMED','Stok tiket ID=1 berkurang 2 unit','2026-06-03 19:09:18'),(5,11,'TRX-20260603-00051360',5,'BOOKING_CREATED','Booking 3 tiket, total Rp 7500000.00','2026-06-03 19:10:54'),(6,4,'TRX-20250102-002',3,'PAYMENT_CONFIRMED','Stok tiket ID=5 berkurang 2 unit','2026-06-05 21:26:09'),(7,9,'TRX-20260603-00055292',5,'PAYMENT_CONFIRMED','Stok tiket ID=1 berkurang 1 unit','2026-06-05 21:58:11');
/*!40000 ALTER TABLE `log_transaksi` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tiket`
--

DROP TABLE IF EXISTS `tiket`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tiket` (
  `id` int NOT NULL AUTO_INCREMENT,
  `konser_id` int NOT NULL,
  `kategori` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `harga` decimal(12,2) NOT NULL,
  `kuota` int NOT NULL DEFAULT '0',
  `terjual` int NOT NULL DEFAULT '0',
  `keterangan` text COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`id`),
  KEY `konser_id` (`konser_id`),
  CONSTRAINT `tiket_ibfk_1` FOREIGN KEY (`konser_id`) REFERENCES `konser` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tiket`
--

LOCK TABLES `tiket` WRITE;
/*!40000 ALTER TABLE `tiket` DISABLE KEYS */;
INSERT INTO `tiket` VALUES (1,1,'VVIP',2500000.00,100,13,NULL),(2,1,'VIP',1500000.00,300,45,NULL),(3,1,'Festival',750000.00,1000,200,NULL),(4,2,'VIP Lounge',3000000.00,50,5,NULL),(5,2,'CAT 1',1200000.00,500,122,NULL),(6,2,'Festival',600000.00,2000,500,NULL),(7,3,'Presale A',350000.00,800,300,NULL),(8,3,'Presale B',450000.00,600,150,NULL),(9,4,'VIP',2000000.00,200,200,NULL),(10,4,'Regular',800000.00,800,800,NULL),(11,5,'Premium',1800000.00,150,150,NULL),(12,5,'Standard',900000.00,500,500,NULL);
/*!40000 ALTER TABLE `tiket` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `transaksi`
--

DROP TABLE IF EXISTS `transaksi`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `transaksi` (
  `id` int NOT NULL AUTO_INCREMENT,
  `kode_transaksi` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` int NOT NULL,
  `tiket_id` int NOT NULL,
  `jumlah_tiket` int NOT NULL DEFAULT '1',
  `total_harga` decimal(14,2) NOT NULL,
  `status` enum('pending','paid','cancelled','refunded') COLLATE utf8mb4_unicode_ci DEFAULT 'pending',
  `metode_bayar` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `catatan` text COLLATE utf8mb4_unicode_ci,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `kode_transaksi` (`kode_transaksi`),
  KEY `user_id` (`user_id`),
  KEY `tiket_id` (`tiket_id`),
  CONSTRAINT `transaksi_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `transaksi_ibfk_2` FOREIGN KEY (`tiket_id`) REFERENCES `tiket` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `transaksi`
--

LOCK TABLES `transaksi` WRITE;
/*!40000 ALTER TABLE `transaksi` DISABLE KEYS */;
INSERT INTO `transaksi` VALUES (1,'TRX-20250101-001',2,1,2,5000000.00,'paid','transfer_bank',NULL,'2026-06-06 11:04:16','2026-06-06 11:04:16'),(2,'TRX-20250101-002',2,3,3,2250000.00,'paid','gopay',NULL,'2026-06-06 11:04:16','2026-06-06 11:04:16'),(3,'TRX-20250102-001',3,2,1,1500000.00,'paid','ovo',NULL,'2026-06-06 11:04:16','2026-06-06 11:04:16'),(4,'TRX-20250102-002',3,5,2,2400000.00,'paid','transfer_bank',NULL,'2026-06-06 11:04:16','2026-06-06 11:04:16'),(5,'TRX-20250103-001',4,7,4,1400000.00,'paid','dana',NULL,'2026-06-06 11:04:16','2026-06-06 11:04:16'),(6,'TRX-20250103-002',2,8,2,900000.00,'cancelled','gopay',NULL,'2026-06-06 11:04:16','2026-06-06 11:04:16'),(7,'TRX-20250104-001',4,4,1,3000000.00,'paid','transfer_bank',NULL,'2026-06-06 11:04:16','2026-06-06 11:04:16'),(8,'TRX-20260603-00057411',5,1,5,12500000.00,'pending','gopay',NULL,'2026-06-06 11:04:16','2026-06-06 11:04:16'),(9,'TRX-20260603-00055292',5,1,1,2500000.00,'paid','gopay',NULL,'2026-06-06 11:04:16','2026-06-06 11:04:16'),(10,'TRX-20260603-00057707',5,1,2,5000000.00,'refunded','gopay',NULL,'2026-06-06 11:04:16','2026-06-06 11:04:16'),(11,'TRX-20260603-00051360',5,1,3,7500000.00,'pending','transfer_bank',NULL,'2026-06-06 11:04:16','2026-06-06 11:04:16');
/*!40000 ALTER TABLE `transaksi` ENABLE KEYS */;
UNLOCK TABLES;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_unicode_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `trg_before_insert_transaksi` BEFORE INSERT ON `transaksi` FOR EACH ROW BEGIN
    DECLARE v_sisa INT;
    SELECT (kuota - terjual) INTO v_sisa FROM tiket WHERE id = NEW.tiket_id;
    IF v_sisa < NEW.jumlah_tiket THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Kuota tiket tidak mencukupi!';
    END IF;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_unicode_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `trg_after_insert_transaksi` AFTER INSERT ON `transaksi` FOR EACH ROW BEGIN
    INSERT INTO log_transaksi (transaksi_id, kode_transaksi, user_id, aksi, keterangan)
    VALUES (NEW.id, NEW.kode_transaksi, NEW.user_id, 'BOOKING_CREATED',
            CONCAT('Booking ', NEW.jumlah_tiket, ' tiket, total Rp ', NEW.total_harga));
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_unicode_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `trg_after_update_transaksi` AFTER UPDATE ON `transaksi` FOR EACH ROW BEGIN
    IF NEW.status = 'paid' AND OLD.status != 'paid' THEN
        UPDATE tiket
        SET terjual = terjual + NEW.jumlah_tiket
        WHERE id = NEW.tiket_id;

        INSERT INTO log_transaksi (transaksi_id, kode_transaksi, user_id, aksi, keterangan)
        VALUES (NEW.id, NEW.kode_transaksi, NEW.user_id, 'PAYMENT_CONFIRMED',
                CONCAT('Stok tiket ID=', NEW.tiket_id, ' berkurang ', NEW.jumlah_tiket, ' unit'));
    END IF;

    IF NEW.status = 'cancelled' AND OLD.status = 'paid' THEN
        UPDATE tiket
        SET terjual = terjual - NEW.jumlah_tiket
        WHERE id = NEW.tiket_id;

        INSERT INTO log_transaksi (transaksi_id, kode_transaksi, user_id, aksi, keterangan)
        VALUES (NEW.id, NEW.kode_transaksi, NEW.user_id, 'CANCELLED_REFUND',
                CONCAT('Stok tiket ID=', NEW.tiket_id, ' dikembalikan ', NEW.jumlah_tiket, ' unit'));
    END IF;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nama` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` enum('admin','user') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'user',
  `phone` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'Administrator','admin@ngonser.id','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','admin','081234567890','2026-06-06 11:04:16','2026-06-06 11:04:16'),(2,'Andi Pratama','andi@mail.com','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','user','082111222333','2026-06-06 11:04:16','2026-06-06 11:04:16'),(3,'Budi Santoso','budi@mail.com','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','user','083222333444','2026-06-06 11:04:16','2026-06-06 11:04:16'),(4,'Citra Dewi','citra@mail.com','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','user','085333444555','2026-06-06 11:04:16','2026-06-06 11:04:16'),(5,'Muhamad Raffael Ramadhani','raffael@gmail.com','$2y$10$DU4VxRqY1/t/qEO5txPlOeyGKG35r3M8VcGAKfqeS/QKIK8Wyv7Au','user','087262782828','2026-06-06 11:04:16','2026-06-06 11:04:16');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Temporary view structure for view `v_pembeli_aktif`
--

DROP TABLE IF EXISTS `v_pembeli_aktif`;
/*!50001 DROP VIEW IF EXISTS `v_pembeli_aktif`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `v_pembeli_aktif` AS SELECT 
 1 AS `user_id`,
 1 AS `periode`*/;
SET character_set_client = @saved_cs_client;

--
-- Temporary view structure for view `v_riwayat_transaksi`
--

DROP TABLE IF EXISTS `v_riwayat_transaksi`;
/*!50001 DROP VIEW IF EXISTS `v_riwayat_transaksi`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `v_riwayat_transaksi` AS SELECT 
 1 AS `id`,
 1 AS `kode_transaksi`,
 1 AS `nama_pembeli`,
 1 AS `email`,
 1 AS `nama_konser`,
 1 AS `artis`,
 1 AS `tanggal_konser`,
 1 AS `kategori_tiket`,
 1 AS `jumlah_tiket`,
 1 AS `total_harga`,
 1 AS `status`,
 1 AS `metode_bayar`,
 1 AS `waktu_pesan`*/;
SET character_set_client = @saved_cs_client;

--
-- Temporary view structure for view `v_statistik_konser`
--

DROP TABLE IF EXISTS `v_statistik_konser`;
/*!50001 DROP VIEW IF EXISTS `v_statistik_konser`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `v_statistik_konser` AS SELECT 
 1 AS `konser_id`,
 1 AS `nama_konser`,
 1 AS `artis`,
 1 AS `total_transaksi`,
 1 AS `tiket_terjual`,
 1 AS `total_pendapatan`*/;
SET character_set_client = @saved_cs_client;

--
-- Temporary view structure for view `v_tiket_semua_transaksi`
--

DROP TABLE IF EXISTS `v_tiket_semua_transaksi`;
/*!50001 DROP VIEW IF EXISTS `v_tiket_semua_transaksi`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `v_tiket_semua_transaksi` AS SELECT 
 1 AS `tiket_id`,
 1 AS `kategori`,
 1 AS `harga`,
 1 AS `kode_transaksi`,
 1 AS `status`,
 1 AS `jumlah_tiket`*/;
SET character_set_client = @saved_cs_client;

--
-- Temporary view structure for view `v_tiket_tersedia`
--

DROP TABLE IF EXISTS `v_tiket_tersedia`;
/*!50001 DROP VIEW IF EXISTS `v_tiket_tersedia`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `v_tiket_tersedia` AS SELECT 
 1 AS `konser_id`,
 1 AS `nama_konser`,
 1 AS `artis`,
 1 AS `tanggal_konser`,
 1 AS `status_konser`,
 1 AS `tiket_id`,
 1 AS `kategori`,
 1 AS `harga`,
 1 AS `sisa_tiket`*/;
SET character_set_client = @saved_cs_client;

--
-- Final view structure for view `v_pembeli_aktif`
--

/*!50001 DROP VIEW IF EXISTS `v_pembeli_aktif`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_unicode_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `v_pembeli_aktif` AS select `transaksi`.`user_id` AS `user_id`,'Q1-2025' AS `periode` from `transaksi` where ((`transaksi`.`created_at` >= '2025-01-01') and (`transaksi`.`created_at` < '2025-04-01') and (`transaksi`.`status` = 'paid')) union select `transaksi`.`user_id` AS `user_id`,'All-Time' AS `periode` from `transaksi` where (`transaksi`.`status` = 'paid') */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `v_riwayat_transaksi`
--

/*!50001 DROP VIEW IF EXISTS `v_riwayat_transaksi`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_unicode_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `v_riwayat_transaksi` AS select `tr`.`id` AS `id`,`tr`.`kode_transaksi` AS `kode_transaksi`,`u`.`nama` AS `nama_pembeli`,`u`.`email` AS `email`,`k`.`nama_konser` AS `nama_konser`,`k`.`artis` AS `artis`,`k`.`tanggal_konser` AS `tanggal_konser`,`t`.`kategori` AS `kategori_tiket`,`tr`.`jumlah_tiket` AS `jumlah_tiket`,`tr`.`total_harga` AS `total_harga`,`tr`.`status` AS `status`,`tr`.`metode_bayar` AS `metode_bayar`,`tr`.`created_at` AS `waktu_pesan` from (((`transaksi` `tr` join `users` `u` on((`tr`.`user_id` = `u`.`id`))) join `tiket` `t` on((`tr`.`tiket_id` = `t`.`id`))) join `konser` `k` on((`t`.`konser_id` = `k`.`id`))) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `v_statistik_konser`
--

/*!50001 DROP VIEW IF EXISTS `v_statistik_konser`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_unicode_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `v_statistik_konser` AS select `k`.`id` AS `konser_id`,`k`.`nama_konser` AS `nama_konser`,`k`.`artis` AS `artis`,count(`tr`.`id`) AS `total_transaksi`,sum((case when (`tr`.`status` = 'paid') then `tr`.`jumlah_tiket` else 0 end)) AS `tiket_terjual`,sum((case when (`tr`.`status` = 'paid') then `tr`.`total_harga` else 0 end)) AS `total_pendapatan` from ((`konser` `k` left join `tiket` `t` on((`k`.`id` = `t`.`konser_id`))) left join `transaksi` `tr` on((`t`.`id` = `tr`.`tiket_id`))) group by `k`.`id`,`k`.`nama_konser`,`k`.`artis` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `v_tiket_semua_transaksi`
--

/*!50001 DROP VIEW IF EXISTS `v_tiket_semua_transaksi`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_unicode_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `v_tiket_semua_transaksi` AS select `t`.`id` AS `tiket_id`,`t`.`kategori` AS `kategori`,`t`.`harga` AS `harga`,`tr`.`kode_transaksi` AS `kode_transaksi`,`tr`.`status` AS `status`,`tr`.`jumlah_tiket` AS `jumlah_tiket` from (`tiket` `t` left join `transaksi` `tr` on((`tr`.`tiket_id` = `t`.`id`))) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `v_tiket_tersedia`
--

/*!50001 DROP VIEW IF EXISTS `v_tiket_tersedia`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_unicode_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `v_tiket_tersedia` AS select `k`.`id` AS `konser_id`,`k`.`nama_konser` AS `nama_konser`,`k`.`artis` AS `artis`,`k`.`tanggal_konser` AS `tanggal_konser`,`k`.`status` AS `status_konser`,`t`.`id` AS `tiket_id`,`t`.`kategori` AS `kategori`,`t`.`harga` AS `harga`,(`t`.`kuota` - `t`.`terjual`) AS `sisa_tiket` from (`konser` `k` join `tiket` `t` on((`k`.`id` = `t`.`konser_id`))) where ((`t`.`kuota` - `t`.`terjual`) > 0) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-06-06 14:48:30
