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

INSERT INTO users (nama, email, password, role, phone) VALUES
('Administrator',  'admin@ngonser.id',  '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', '081234567890'),
('Andi Pratama',   'andi@mail.com',     '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'user',  '082111222333'),
('Budi Santoso',   'budi@mail.com',     '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'user',  '083222333444'),
('Citra Dewi',     'citra@mail.com',    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'user',  '085333444555');

INSERT INTO konser (nama_konser, artis, venue, kota, tanggal_konser, jam_mulai, deskripsi, status) VALUES
('Konser Spektakuler 2025',  'Dewa 19',        'Stadion Gelora Bung Karno',  'Jakarta',   '2025-08-15', '19:00:00', 'Konser reuni Dewa 19 yang spektakuler!', 'upcoming'),
('Soundrenaline 2025',       'Various Artist', 'Garuda Wisnu Kencana',       'Bali',      '2025-09-20', '15:00:00', 'Festival musik terbesar di Asia Tenggara.', 'upcoming'),
('Synchronize Fest',         'Various Artist', 'Gambir Expo',                'Jakarta',   '2025-10-03', '14:00:00', 'Festival musik indie terbesar di Indonesia.', 'upcoming'),
('Noah World Tour',          'NOAH',           'Istora Senayan',              'Jakarta',   '2025-07-10', '20:00:00', 'Tur dunia NOAH kembali hadir di Jakarta.', 'selesai'),
('Java Jazz Festival',       'Various Artist', 'Jakarta International Expo', 'Jakarta',   '2025-03-01', '12:00:00', 'Pertemuan jazz internasional tahunan.', 'selesai');

INSERT INTO tiket (konser_id, kategori, harga, kuota, terjual) VALUES
(1, 'VVIP',          2500000, 100,  10),
(1, 'VIP',           1500000, 300,  45),
(1, 'Festival',       750000, 1000, 200),
(2, 'VIP Lounge',    3000000, 50,   5),
(2, 'CAT 1',         1200000, 500,  120),
(2, 'Festival',       600000, 2000, 500),
(3, 'Presale A',      350000, 800,  300),
(3, 'Presale B',      450000, 600,  150),
(4, 'VIP',           2000000, 200,  200),
(4, 'Regular',        800000, 800,  800),
(5, 'Premium',       1800000, 150,  150),
(5, 'Standard',       900000, 500,  500);

INSERT INTO transaksi (kode_transaksi, user_id, tiket_id, jumlah_tiket, total_harga, status, metode_bayar) VALUES
('TRX-20250101-001', 2, 1, 2, 5000000,  'paid',      'transfer_bank'),
('TRX-20250101-002', 2, 3, 3, 2250000,  'paid',      'gopay'),
('TRX-20250102-001', 3, 2, 1, 1500000,  'paid',      'ovo'),
('TRX-20250102-002', 3, 5, 2, 2400000,  'pending',   'transfer_bank'),
('TRX-20250103-001', 4, 7, 4, 1400000,  'paid',      'dana'),
('TRX-20250103-002', 2, 8, 2, 900000,   'cancelled', 'gopay'),
('TRX-20250104-001', 4, 4, 1, 3000000,  'paid',      'transfer_bank');

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
    COUNT(tr.id)                            AS total_transaksi,
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
