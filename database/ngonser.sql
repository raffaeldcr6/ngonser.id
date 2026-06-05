SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+07:00";

DROP DATABASE IF EXISTS ngonser_db;
CREATE DATABASE ngonser_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE ngonser_db;

CREATE TABLE users (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    nama        VARCHAR(100)        NOT NULL,
    email       VARCHAR(100) UNIQUE NOT NULL,
    password    VARCHAR(255)        NOT NULL,
    role        ENUM('admin','user') NOT NULL DEFAULT 'user',
    phone       VARCHAR(20),
    created_at  DATETIME            DEFAULT CURRENT_TIMESTAMP,
    updated_at  DATETIME            DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE konser (
    id              INT AUTO_INCREMENT PRIMARY KEY,
    nama_konser     VARCHAR(150)    NOT NULL,
    artis           VARCHAR(100)    NOT NULL,
    venue           VARCHAR(200)    NOT NULL,
    kota            VARCHAR(100)    NOT NULL,
    tanggal_konser  DATE            NOT NULL,
    jam_mulai       TIME            NOT NULL,
    deskripsi       TEXT,
    poster          VARCHAR(255)    DEFAULT 'default.jpg',
    status          ENUM('upcoming','ongoing','selesai','batal') DEFAULT 'upcoming',
    created_at      DATETIME        DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE konser_jakarta (
    id              INT NOT NULL DEFAULT '0',
    nama_konser     VARCHAR(150)    NOT NULL,
    artis           VARCHAR(100)    NOT NULL,
    venue           VARCHAR(200)    NOT NULL,
    kota            VARCHAR(100)    NOT NULL,
    tanggal_konser  DATE            NOT NULL,
    jam_mulai       TIME            NOT NULL,
    deskripsi       TEXT,
    poster          VARCHAR(255)    DEFAULT 'default.jpg',
    status          ENUM('upcoming','ongoing','selesai','batal') DEFAULT 'upcoming',
    created_at      DATETIME        DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE tiket (
    id              INT AUTO_INCREMENT PRIMARY KEY,
    konser_id       INT             NOT NULL,
    kategori        VARCHAR(100)    NOT NULL,
    harga           DECIMAL(12,2)   NOT NULL,
    kuota           INT             NOT NULL DEFAULT 0,
    terjual         INT             NOT NULL DEFAULT 0,
    keterangan      TEXT,
    FOREIGN KEY (konser_id) REFERENCES konser(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE transaksi (
    id              INT AUTO_INCREMENT PRIMARY KEY,
    kode_transaksi  VARCHAR(30) UNIQUE NOT NULL,
    user_id         INT             NOT NULL,
    tiket_id        INT             NOT NULL,
    jumlah_tiket    INT             NOT NULL DEFAULT 1,
    total_harga     DECIMAL(14,2)   NOT NULL,
    status          ENUM('pending','paid','cancelled','refunded') DEFAULT 'pending',
    metode_bayar    VARCHAR(50),
    catatan         TEXT,
    created_at      DATETIME        DEFAULT CURRENT_TIMESTAMP,
    updated_at      DATETIME        DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id)   REFERENCES users(id)  ON DELETE CASCADE,
    FOREIGN KEY (tiket_id)  REFERENCES tiket(id)  ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE jenis_bayar (
    id              INT NOT NULL DEFAULT '0',
    kode_transaksi  VARCHAR(30)     NOT NULL,
    total_harga     DECIMAL(14,2)   NOT NULL,
    metode_bayar    VARCHAR(50)
) ENGINE=InnoDB;

CREATE TABLE log_transaksi (
    id              INT AUTO_INCREMENT PRIMARY KEY,
    transaksi_id    INT,
    kode_transaksi  VARCHAR(30),
    user_id         INT,
    aksi            VARCHAR(100),
    keterangan      TEXT,
    created_at      DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE backup_log (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    filename    VARCHAR(255) NOT NULL,
    tipe        ENUM('manual','otomatis') DEFAULT 'manual',
    ukuran_kb   INT,
    created_by  VARCHAR(100),
    created_at  DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

INSERT INTO users (id, nama, email, password, role, phone) VALUES
(1, 'Administrator', 'admin@ngonser.id', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', '081234567890'),
(2, 'Andi Pratama', 'andi@mail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'user', '082111222333'),
(3, 'Budi Santoso', 'budi@mail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'user', '083222333444'),
(4, 'Citra Dewi', 'citra@mail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'user', '085333444555'),
(5, 'Muhamad Raffael Ramadhani', 'raffael@gmail.com', '$2y$10$DU4VxRqY1/t/qEO5txPlOeyGKG35r3M8VcGAKfqeS/QKIK8Wyv7Au', 'user', '087262782828');

INSERT INTO konser (id, nama_konser, artis, venue, kota, tanggal_konser, jam_mulai, deskripsi, status) VALUES
(1, 'Konser Spektakuler 2025', 'Dewa 19', 'Stadion Gelora Bung Karno', 'Jakarta', '2025-08-15', '19:00:00', 'Konser reuni Dewa 19 yang spektakuler!', 'upcoming'),
(2, 'Soundrenaline 2025', 'Various Artist', 'Garuda Wisnu Kencana', 'Bali', '2025-09-20', '15:00:00', 'Festival musik terbesar di Asia Tenggara.', 'upcoming'),
(3, 'Synchronize Fest', 'Various Artist', 'Gambir Expo', 'Jakarta', '2025-10-03', '14:00:00', 'Festival musik indie terbesar di Indonesia.', 'upcoming'),
(4, 'Noah World Tour', 'NOAH', 'Istora Senayan', 'Jakarta', '2025-07-10', '20:00:00', 'Tur dunia NOAH kembali hadir di Jakarta.', 'selesai'),
(5, 'Java Jazz Festival', 'Various Artist', 'Jakarta International Expo', 'Jakarta', '2025-03-01', '12:00:00', 'Pertemuan jazz internasional tahunan.', 'selesai');

INSERT INTO konser_jakarta (id, nama_konser, artis, venue, kota, tanggal_konser, jam_mulai, deskripsi, status) VALUES
(1, 'Konser Spektakuler 2025', 'Dewa 19', 'Stadion Gelora Bung Karno', 'Jakarta', '2025-08-15', '19:00:00', 'Konser reuni Dewa 19 yang spektakuler!', 'upcoming'),
(3, 'Synchronize Fest', 'Various Artist', 'Gambir Expo', 'Jakarta', '2025-10-03', '14:00:00', 'Festival musik indie terbesar di Indonesia.', 'upcoming'),
(4, 'Noah World Tour', 'NOAH', 'Istora Senayan', 'Jakarta', '2025-07-10', '20:00:00', 'Tur dunia NOAH kembali hadir di Jakarta.', 'selesai'),
(5, 'Java Jazz Festival', 'Various Artist', 'Jakarta International Expo', 'Jakarta', '2025-03-01', '12:00:00', 'Pertemuan jazz internasional tahunan.', 'selesai');

INSERT INTO tiket (id, konser_id, kategori, harga, kuota, terjual, keterangan) VALUES
(1, 1, 'VVIP', 2500000.00, 100, 13, NULL),
(2, 1, 'VIP', 1500000.00, 300, 45, NULL),
(3, 1, 'Festival', 750000.00, 1000, 200, NULL),
(4, 2, 'VIP Lounge', 3000000.00, 50, 5, NULL),
(5, 2, 'CAT 1', 1200000.00, 500, 122, NULL),
(6, 2, 'Festival', 600000.00, 2000, 500, NULL),
(7, 3, 'Presale A', 350000.00, 800, 300, NULL),
(8, 3, 'Presale B', 450000.00, 600, 150, NULL),
(9, 4, 'VIP', 2000000.00, 200, 200, NULL),
(10, 4, 'Regular', 800000.00, 800, 800, NULL),
(11, 5, 'Premium', 1800000.00, 150, 150, NULL),
(12, 5, 'Standard', 900000.00, 500, 500, NULL);

INSERT INTO transaksi (id, kode_transaksi, user_id, tiket_id, jumlah_tiket, total_harga, status, metode_bayar, catatan) VALUES
(1, 'TRX-20250101-001', 2, 1, 2, 5000000.00, 'paid', 'transfer_bank', NULL),
(2, 'TRX-20250101-002', 2, 3, 3, 2250000.00, 'paid', 'gopay', NULL),
(3, 'TRX-20250102-001', 3, 2, 1, 1500000.00, 'paid', 'ovo', NULL),
(4, 'TRX-20250102-002', 3, 5, 2, 2400000.00, 'paid', 'transfer_bank', NULL),
(5, 'TRX-20250103-001', 4, 7, 4, 1400000.00, 'paid', 'dana', NULL),
(6, 'TRX-20250103-002', 2, 8, 2, 900000.00, 'cancelled', 'gopay', NULL),
(7, 'TRX-20250104-001', 4, 4, 1, 3000000.00, 'paid', 'transfer_bank', NULL),
(8, 'TRX-20260603-00057411', 5, 1, 5, 12500000.00, 'pending', 'gopay', NULL),
(9, 'TRX-20260603-00055292', 5, 1, 1, 2500000.00, 'paid', 'gopay', NULL),
(10, 'TRX-20260603-00057707', 5, 1, 2, 5000000.00, 'refunded', 'gopay', NULL),
(11, 'TRX-20260603-00051360', 5, 1, 3, 7500000.00, 'pending', 'transfer_bank', NULL);

INSERT INTO jenis_bayar (id, kode_transaksi, total_harga, metode_bayar) VALUES
(1, 'TRX-20250101-001', 5000000.00, 'transfer_bank'),
(2, 'TRX-20250101-002', 2250000.00, 'gopay'),
(3, 'TRX-20250102-001', 1500000.00, 'ovo'),
(4, 'TRX-20250102-002', 2400000.00, 'transfer_bank'),
(5, 'TRX-20250103-001', 1400000.00, 'dana'),
(6, 'TRX-20250103-002', 900000.00, 'gopay'),
(7, 'TRX-20250104-001', 3000000.00, 'transfer_bank'),
(8, 'TRX-20260603-00057411', 12500000.00, 'gopay'),
(9, 'TRX-20260603-00055292', 2500000.00, 'gopay'),
(10, 'TRX-20260603-00057707', 5000000.00, 'gopay'),
(11, 'TRX-20260603-00051360', 7500000.00, 'transfer_bank');

INSERT INTO log_transaksi (id, transaksi_id, kode_transaksi, user_id, aksi, keterangan, created_at) VALUES
(1, 8, 'TRX-20260603-00057411', 5, 'BOOKING_CREATED', 'Booking 5 tiket, total Rp 12500000.00', '2026-06-03 19:08:54'),
(2, 9, 'TRX-20260603-00055292', 5, 'BOOKING_CREATED', 'Booking 1 tiket, total Rp 2500000.00', '2026-06-03 19:09:02'),
(3, 10, 'TRX-20260603-00057707', 5, 'BOOKING_CREATED', 'Booking 2 tiket, total Rp 5000000.00', '2026-06-03 19:09:14'),
(4, 10, 'TRX-20260603-00057707', 5, 'PAYMENT_CONFIRMED', 'Stok tiket ID=1 berkurang 2 unit', '2026-06-03 19:09:18'),
(5, 11, 'TRX-20260603-00051360', 5, 'BOOKING_CREATED', 'Booking 3 tiket, total Rp 7500000.00', '2026-06-03 19:10:54'),
(6, 4, 'TRX-20250102-002', 3, 'PAYMENT_CONFIRMED', 'Stok tiket ID=5 berkurang 2 unit', '2026-06-05 21:26:09'),
(7, 9, 'TRX-20260603-00055292', 5, 'PAYMENT_CONFIRMED', 'Stok tiket ID=1 berkurang 1 unit', '2026-06-05 21:58:11');

CREATE VIEW v_tiket_tersedia AS
SELECT
    k.id            AS konser_id,
    k.nama_konser,
    k.artis,
    k.tanggal_konser,
    k.status        AS status_konser,
    t.id            AS tiket_id,
    t.kategori,
    t.harga,
    (t.kuota - t.terjual) AS sisa_tiket
FROM konser k
INNER JOIN tiket t ON k.id = t.konser_id
WHERE (t.kuota - t.terjual) > 0;

CREATE VIEW v_riwayat_transaksi AS
SELECT
    tr.id,
    tr.kode_transaksi,
    u.nama          AS nama_pembeli,
    u.email,
    k.nama_konser,
    k.artis,
    k.tanggal_konser,
    t.kategori      AS kategori_tiket,
    tr.jumlah_tiket,
    tr.total_harga,
    tr.status,
    tr.metode_bayar,
    tr.created_at   AS waktu_pesan
FROM transaksi tr
INNER JOIN users  u ON tr.user_id  = u.id
INNER JOIN tiket  t ON tr.tiket_id = t.id
INNER JOIN konser k ON t.konser_id  = k.id;

CREATE VIEW v_statistik_konser AS
SELECT
    k.id            AS konser_id,
    k.nama_konser,
    k.artis,
    COUNT(tr.id)                                                    AS total_transaksi,
    SUM(CASE WHEN tr.status='paid' THEN tr.jumlah_tiket ELSE 0 END) AS tiket_terjual,
    SUM(CASE WHEN tr.status='paid' THEN tr.total_harga  ELSE 0 END) AS total_pendapatan
FROM konser k
LEFT JOIN tiket     t  ON k.id        = t.konser_id
LEFT JOIN transaksi tr ON t.id        = tr.tiket_id
GROUP BY k.id, k.nama_konser, k.artis;

CREATE VIEW v_tiket_semua_transaksi AS
SELECT
    t.id            AS tiket_id,
    t.kategori,
    t.harga,
    tr.kode_transaksi,
    tr.status,
    tr.jumlah_tiket
FROM transaksi tr
RIGHT JOIN tiket t ON tr.tiket_id = t.id;

CREATE VIEW v_pembeli_aktif AS
SELECT user_id, 'Q1-2025' AS periode FROM transaksi WHERE created_at >= '2025-01-01' AND created_at < '2025-04-01' AND status='paid'
UNION
SELECT user_id, 'All-Time' AS periode FROM transaksi WHERE status='paid';

DELIMITER //

DROP FUNCTION IF EXISTS fn_format_rupiah //
CREATE FUNCTION fn_format_rupiah(harga DECIMAL(14,2))
RETURNS VARCHAR(30)
DETERMINISTIC
BEGIN
    RETURN CONCAT('Rp ', FORMAT(harga, 0, 'id_ID'));
END //

DROP FUNCTION IF EXISTS fn_status_tiket //
CREATE FUNCTION fn_status_tiket(kuota INT, terjual INT)
RETURNS VARCHAR(20)
DETERMINISTIC
BEGIN
    DECLARE sisa INT;
    SET sisa = kuota - terjual;
    IF sisa <= 0 THEN
        RETURN 'HABIS';
    ELSEIF sisa <= 10 THEN
        RETURN 'HAMPIR HABIS';
    ELSE
        RETURN 'TERSEDIA';
    END IF;
END //

DROP FUNCTION IF EXISTS fn_generate_kode_trx //
CREATE FUNCTION fn_generate_kode_trx(user_id INT)
RETURNS VARCHAR(30)
DETERMINISTIC
BEGIN
    RETURN CONCAT('TRX-', DATE_FORMAT(NOW(), '%Y%m%d'), '-', LPAD(user_id, 4, '0'), LPAD(FLOOR(RAND()*9999),4,'0'));
END //

DELIMITER ;

DELIMITER //

DROP PROCEDURE IF EXISTS sp_tambah_tiket //
CREATE PROCEDURE sp_tambah_tiket(
    IN p_konser_id  INT,
    IN p_kategori   VARCHAR(100),
    IN p_harga      DECIMAL(12,2),
    IN p_kuota      INT,
    IN p_keterangan TEXT,
    OUT p_result    VARCHAR(100)
)
BEGIN
    IF p_kuota <= 0 THEN
        SET p_result = 'ERROR: Kuota harus lebih dari 0';
    ELSE
        INSERT INTO tiket (konser_id, kategori, harga, kuota, keterangan)
        VALUES (p_konser_id, p_kategori, p_harga, p_kuota, p_keterangan);
        SET p_result = CONCAT('SUCCESS: Tiket ID=', LAST_INSERT_ID(), ' berhasil ditambahkan');
    END IF;
END //

DROP PROCEDURE IF EXISTS sp_lihat_tiket //
CREATE PROCEDURE sp_lihat_tiket(IN p_konser_id INT)
BEGIN
    SELECT
        t.id, t.kategori,
        fn_format_rupiah(t.harga)   AS harga_format,
        t.kuota, t.terjual,
        (t.kuota - t.terjual)       AS sisa,
        fn_status_tiket(t.kuota, t.terjual) AS ketersediaan
    FROM tiket t
    WHERE t.konser_id = p_konser_id
    ORDER BY t.harga DESC;
END //

DROP PROCEDURE IF EXISTS sp_update_tiket //
CREATE PROCEDURE sp_update_tiket(
    IN p_id         INT,
    IN p_kategori   VARCHAR(100),
    IN p_harga      DECIMAL(12,2),
    IN p_kuota      INT,
    OUT p_result    VARCHAR(100)
)
BEGIN
    IF NOT EXISTS (SELECT 1 FROM tiket WHERE id = p_id) THEN
        SET p_result = 'ERROR: Tiket tidak ditemukan';
    ELSE
        UPDATE tiket SET kategori=p_kategori, harga=p_harga, kuota=p_kuota WHERE id=p_id;
        SET p_result = 'SUCCESS: Tiket berhasil diperbarui';
    END IF;
END //

DROP PROCEDURE IF EXISTS sp_hapus_tiket //
CREATE PROCEDURE sp_hapus_tiket(IN p_id INT, OUT p_result VARCHAR(100))
BEGIN
    IF EXISTS (SELECT 1 FROM transaksi WHERE tiket_id = p_id AND status IN ('pending','paid')) THEN
        SET p_result = 'ERROR: Tiket memiliki transaksi aktif, tidak dapat dihapus';
    ELSE
        DELETE FROM tiket WHERE id = p_id;
        SET p_result = 'SUCCESS: Tiket berhasil dihapus';
    END IF;
END //

DROP PROCEDURE IF EXISTS sp_checkout_tiket //
CREATE PROCEDURE sp_checkout_tiket(
    IN  p_user_id       INT,
    IN  p_tiket_id      INT,
    IN  p_jumlah        INT,
    IN  p_metode        VARCHAR(50),
    OUT p_result        VARCHAR(200),
    OUT p_kode_trx      VARCHAR(30)
)
BEGIN
    DECLARE v_harga     DECIMAL(12,2);
    DECLARE v_sisa      INT;
    DECLARE v_total     DECIMAL(14,2);
    DECLARE v_kode      VARCHAR(30);
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        SET p_result   = 'ERROR: Terjadi kesalahan sistem, transaksi dibatalkan';
        SET p_kode_trx = NULL;
    END;

    START TRANSACTION;

    SELECT harga, (kuota - terjual) INTO v_harga, v_sisa
    FROM tiket WHERE id = p_tiket_id FOR UPDATE;

    IF v_sisa < p_jumlah THEN
        ROLLBACK;
        SET p_result   = 'ERROR: Tiket tidak mencukupi';
        SET p_kode_trx = NULL;
    ELSE
        SET v_total = v_harga * p_jumlah;
        SET v_kode  = fn_generate_kode_trx(p_user_id);

        INSERT INTO transaksi (kode_transaksi, user_id, tiket_id, jumlah_tiket, total_harga, status, metode_bayar)
        VALUES (v_kode, p_user_id, p_tiket_id, p_jumlah, v_total, 'pending', p_metode);

        COMMIT;
        SET p_result   = 'SUCCESS: Booking berhasil, silakan selesaikan pembayaran';
        SET p_kode_trx = v_kode;
    END IF;
END //

DROP PROCEDURE IF EXISTS sp_konfirmasi_bayar //
CREATE PROCEDURE sp_konfirmasi_bayar(
    IN  p_kode_trx  VARCHAR(30),
    OUT p_result    VARCHAR(200)
)
BEGIN
    DECLARE v_trx_id    INT;
    DECLARE v_status    VARCHAR(20);
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        SET p_result = 'ERROR: Gagal konfirmasi pembayaran';
    END;

    START TRANSACTION;
    SELECT id, status INTO v_trx_id, v_status
    FROM transaksi WHERE kode_transaksi = p_kode_trx FOR UPDATE;

    IF v_trx_id IS NULL THEN
        ROLLBACK;
        SET p_result = 'ERROR: Kode transaksi tidak ditemukan';
    ELSEIF v_status != 'pending' THEN
        ROLLBACK;
        SET p_result = CONCAT('ERROR: Transaksi sudah berstatus ', v_status);
    ELSE
        UPDATE transaksi SET status='paid', updated_at=NOW() WHERE id=v_trx_id;
        COMMIT;
        SET p_result = 'SUCCESS: Pembayaran berhasil dikonfirmasi';
    END IF;
END //

DELIMITER ;

DELIMITER //

DROP TRIGGER IF EXISTS trg_after_insert_transaksi //
CREATE TRIGGER trg_after_insert_transaksi
AFTER INSERT ON transaksi
FOR EACH ROW
BEGIN
    INSERT INTO log_transaksi (transaksi_id, kode_transaksi, user_id, aksi, keterangan)
    VALUES (NEW.id, NEW.kode_transaksi, NEW.user_id, 'BOOKING_CREATED',
            CONCAT('Booking ', NEW.jumlah_tiket, ' tiket, total Rp ', NEW.total_harga));
END //

DROP TRIGGER IF EXISTS trg_after_update_transaksi //
CREATE TRIGGER trg_after_update_transaksi
AFTER UPDATE ON transaksi
FOR EACH ROW
BEGIN
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
END //

DROP TRIGGER IF EXISTS trg_before_insert_transaksi //
CREATE TRIGGER trg_before_insert_transaksi
BEFORE INSERT ON transaksi
FOR EACH ROW
BEGIN
    DECLARE v_sisa INT;
    SELECT (kuota - terjual) INTO v_sisa FROM tiket WHERE id = NEW.tiket_id;
    IF v_sisa < NEW.jumlah_tiket THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Kuota tiket tidak mencukupi!';
    END IF;
END //

DELIMITER ;

CREATE USER IF NOT EXISTS 'adm_backup'@'localhost' IDENTIFIED BY 'admin123';
GRANT SELECT, LOCK TABLES, SHOW VIEW, EVENT, TRIGGER ON ngonser_db.* TO 'adm_backup'@'localhost';
FLUSH PRIVILEGES;

COMMIT;
