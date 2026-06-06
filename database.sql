-- SQL Database Schema for SPAdmin Spa & Massage
-- Database Name: spadmin_rpl
-- Generated based on PHP Model classes and configurations

CREATE DATABASE IF NOT EXISTS `spadmin_rpl` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `spadmin_rpl`;

-- Disable foreign key checks temporarily to avoid constraint errors during recreation
SET FOREIGN_KEY_CHECKS = 0;

-- --------------------------------------------------------
-- Table Structure for `users`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
    `id_user` INT AUTO_INCREMENT PRIMARY KEY,
    `nama` VARCHAR(100) NOT NULL,
    `email` VARCHAR(120) NULL UNIQUE,
    `password` VARCHAR(255) NOT NULL,
    `no_telepon` VARCHAR(20) NOT NULL,
    `role` VARCHAR(20) NOT NULL DEFAULT 'pelanggan',
    `rating_pelanggan` INT NOT NULL DEFAULT 5,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- Table Structure for `terapis`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `terapis`;
CREATE TABLE `terapis` (
    `id_terapis` INT AUTO_INCREMENT PRIMARY KEY,
    `nama_terapis` VARCHAR(100) NOT NULL,
    `no_telp` VARCHAR(20) NOT NULL,
    `spesialisasi` VARCHAR(120) NOT NULL,
    `status` VARCHAR(20) NOT NULL DEFAULT 'aktif',
    `jenis_kelamin` VARCHAR(20) NOT NULL DEFAULT 'Perempuan',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- Table Structure for `layanan`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `layanan`;
CREATE TABLE `layanan` (
    `id_layanan` INT AUTO_INCREMENT PRIMARY KEY,
    `nama_layanan` VARCHAR(120) NOT NULL,
    `kategori` VARCHAR(100) NOT NULL DEFAULT 'Spa & Massage',
    `media` VARCHAR(255) NULL,
    `harga` INT NOT NULL,
    `durasi` INT NOT NULL,
    `deskripsi` TEXT NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- Table Structure for `ruangan`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `ruangan`;
CREATE TABLE `ruangan` (
    `id_ruangan` INT AUTO_INCREMENT PRIMARY KEY,
    `nama_ruangan` VARCHAR(50) NOT NULL UNIQUE,
    `status` VARCHAR(20) NOT NULL DEFAULT 'aktif'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- Table Structure for `reservasi`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `reservasi`;
CREATE TABLE `reservasi` (
    `id_reservasi` INT AUTO_INCREMENT PRIMARY KEY,
    `id_user` INT NOT NULL,
    `id_ruangan` INT NULL,
    `gender_terapis` VARCHAR(20) NOT NULL DEFAULT 'Bebas',
    `reservation_date` DATETIME NOT NULL,
    `reservation_type` VARCHAR(20) NOT NULL DEFAULT 'online',
    `status_reservation` VARCHAR(40) NOT NULL DEFAULT 'Menunggu Pembayaran',
    `total_price` INT NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`) ON DELETE CASCADE,
    FOREIGN KEY (`id_ruangan`) REFERENCES `ruangan` (`id_ruangan`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- Table Structure for `reservasi_detail`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `reservasi_detail`;
CREATE TABLE `reservasi_detail` (
    `id_detail` INT AUTO_INCREMENT PRIMARY KEY,
    `id_reservasi` INT NOT NULL,
    `id_layanan` INT NOT NULL,
    `id_terapis` INT NULL,
    `qty` INT NOT NULL DEFAULT 1,
    `subtotal` INT NOT NULL,
    FOREIGN KEY (`id_reservasi`) REFERENCES `reservasi` (`id_reservasi`) ON DELETE CASCADE,
    FOREIGN KEY (`id_layanan`) REFERENCES `layanan` (`id_layanan`) ON DELETE CASCADE,
    FOREIGN KEY (`id_terapis`) REFERENCES `terapis` (`id_terapis`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- Table Structure for `payment`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `payment`;
CREATE TABLE `payment` (
    `id_payment` INT AUTO_INCREMENT PRIMARY KEY,
    `id_reservasi` INT NOT NULL,
    `payment_method` VARCHAR(50) NOT NULL,
    `payment_proof` VARCHAR(255) NOT NULL,
    `payment_date` DATETIME NOT NULL,
    `status_payment` VARCHAR(40) NOT NULL DEFAULT 'Menunggu Pembayaran',
    `jenis_pembayaran` VARCHAR(20) NOT NULL DEFAULT 'DP 50%',
    `nominal_payment` INT NOT NULL DEFAULT 0,
    `verified_by` VARCHAR(100) NULL,
    `pelunasan_method` VARCHAR(50) NULL,
    `pelunasan_date` DATETIME NULL,
    `pelunasan_uang_bayar` INT NOT NULL DEFAULT 0,
    `pelunasan_kembalian` INT NOT NULL DEFAULT 0,
    FOREIGN KEY (`id_reservasi`) REFERENCES `reservasi` (`id_reservasi`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- Table Structure for `transaksi`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `transaksi`;
CREATE TABLE `transaksi` (
    `id_transaksi` INT AUTO_INCREMENT PRIMARY KEY,
    `id_reservasi` INT NOT NULL,
    `transaction_date` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `total_payment` INT NOT NULL,
    `uang_bayar` INT NOT NULL DEFAULT 0,
    `kembalian` INT NOT NULL DEFAULT 0,
    FOREIGN KEY (`id_reservasi`) REFERENCES `reservasi` (`id_reservasi`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- Table Structure for `detail_transaksi`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `detail_transaksi`;
CREATE TABLE `detail_transaksi` (
    `id_transaksi_detail` INT AUTO_INCREMENT PRIMARY KEY,
    `id_transaksi` INT NOT NULL,
    `id_layanan` INT NOT NULL,
    `qty` INT NOT NULL DEFAULT 1,
    `subtotal` INT NOT NULL,
    FOREIGN KEY (`id_transaksi`) REFERENCES `transaksi` (`id_transaksi`) ON DELETE CASCADE,
    FOREIGN KEY (`id_layanan`) REFERENCES `layanan` (`id_layanan`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- Table Structure for `ulasan`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `ulasan`;
CREATE TABLE `ulasan` (
    `id_ulasan` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT NOT NULL,
    `reservasi_id` INT NULL,
    `id_layanan` INT NOT NULL,
    `rating` INT NOT NULL,
    `ulasan` TEXT NOT NULL,
    `balasan_admin` TEXT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`user_id`) REFERENCES `users` (`id_user`) ON DELETE CASCADE,
    FOREIGN KEY (`reservasi_id`) REFERENCES `reservasi` (`id_reservasi`) ON DELETE SET NULL,
    FOREIGN KEY (`id_layanan`) REFERENCES `layanan` (`id_layanan`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- Table Structure for `pengaturan_halaman`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `pengaturan_halaman`;
CREATE TABLE `pengaturan_halaman` (
    `kunci` VARCHAR(50) PRIMARY KEY,
    `nilai` TEXT NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- Table Structure for `rekening`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `rekening`;
CREATE TABLE `rekening` (
    `id_rekening` INT AUTO_INCREMENT PRIMARY KEY,
    `nama_bank` VARCHAR(50) NOT NULL,
    `nomor_rekening` VARCHAR(50) NOT NULL,
    `atas_nama` VARCHAR(100) NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Enable foreign key checks back
SET FOREIGN_KEY_CHECKS = 1;

-- ========================================================
-- SEED DATA (INITIAL DATA SEEDING)
-- ========================================================

-- 1. Seeding `users` (Password for all is 'pelanggan123' / 'admin123' pre-hashed with bcrypt)
INSERT INTO `users` (`id_user`, `nama`, `email`, `password`, `no_telepon`, `role`, `rating_pelanggan`) VALUES
(1, 'Diah(Admin)', 'admin@spadmin.com', '$2y$10$N.5tPRiwYeNUYw0ORKCCGOGQMGLmnulOAuITMz9LU9KqjDt71/eea', '08123456789', 'admin', 5),
(2, 'Rian Hidayat', 'rian.hidayat@gmail.com', '$2y$10$o8vH0wO4v.Xn81rF6V0Qxe9KqH1t7yTz.C5D2T0Q5h8fK3Q5h7G1G', '081234567890', 'pelanggan', 5),
(3, 'Siti Aminah', 'siti.aminah@gmail.com', '$2y$10$o8vH0wO4v.Xn81rF6V0Qxe9KqH1t7yTz.C5D2T0Q5h8fK3Q5h7G1G', '081234567891', 'pelanggan', 5),
(4, 'Agus Setiawan', 'agus.setiawan@gmail.com', '$2y$10$o8vH0wO4v.Xn81rF6V0Qxe9KqH1t7yTz.C5D2T0Q5h8fK3Q5h7G1G', '081234567892', 'pelanggan', 5),
(5, 'Dian Prasetyo', 'dian.prasetyo@gmail.com', '$2y$10$o8vH0wO4v.Xn81rF6V0Qxe9KqH1t7yTz.C5D2T0Q5h8fK3Q5h7G1G', '081234567893', 'pelanggan', 5),
(6, 'Dewi Lestari', 'dewi.lestari@gmail.com', '$2y$10$o8vH0wO4v.Xn81rF6V0Qxe9KqH1t7yTz.C5D2T0Q5h8fK3Q5h7G1G', '081234567894', 'pelanggan', 5);

-- 2. Seeding `terapis`
INSERT INTO `terapis` (`id_terapis`, `nama_terapis`, `no_telp`, `spesialisasi`, `status`, `jenis_kelamin`) VALUES
(1, 'Maya Putri', '081234567890', 'Pijat Sehat, Lulur, dan Dulang', 'aktif', 'Perempuan'),
(2, 'Nadia Safira', '081234567891', 'Totok Wajah, Pijat Sehat, dan Talam', 'aktif', 'Perempuan'),
(3, 'Rani Amelia', '081234567892', 'Refleksi, Bekam Basah, dan Bekam Kering', 'aktif', 'Perempuan'),
(4, 'Budi Santoso', '081234567893', 'Bekam Kering, Refleksi, dan Kerok Badan', 'aktif', 'Pria'),
(5, 'Dewi Lestari', '081234567894', 'Lulur, Dulang, dan Garam Rendam Kaki', 'aktif', 'Perempuan');

-- 3. Seeding `layanan`
INSERT INTO `layanan` (`id_layanan`, `nama_layanan`, `kategori`, `media`, `harga`, `durasi`, `deskripsi`) VALUES
(1, 'Pijat Belakang', 'Pijat', 'PijatBelakang.jpeg', 79000, 45, 'Pijat fokus pada area punggung, pinggang, dan belakang betis kaki untuk membantu meredakan pegal dan melancarkan peredaran darah.'),
(2, 'Pijat Sehat', 'Pijat', 'PijatSehat.jpeg', 129000, 90, 'Pijat relaksasi seluruh tubuh untuk mengurangi stres, mengatasi pegal, dan membuat tubuh lebih rileks.'),
(3, 'Pijat Sehat', 'Pijat', 'PijatSehat(Premium).jpeg', 149000, 120, 'Pijat seluruh tubuh dengan durasi lebih lama sehingga tubuh terasa lebih segar, nyaman, dan rileks maksimal.'),
(4, 'Refleksi', 'Refleksi', 'Refleksi.jpeg', 89000, 60, 'Terapi pijat refleksi pada titik saraf kaki untuk membantu melancarkan sirkulasi darah dan mengurangi kelelahan.'),
(5, 'Refleksi', 'Refleksi', 'Refleksi(Premium).jpeg', 109000, 90, 'Refleksi kaki dengan durasi lebih lama untuk memberikan efek relaksasi dan kenyamanan yang lebih maksimal.'),
(6, 'Garam Rendam Kaki', 'Refleksi', 'GaramRendamKaki.jpeg', 12000, 15, 'Perawatan rendam kaki menggunakan garam untuk membantu relaksasi and mengurangi pegal pada kaki.'),
(7, 'Extra Time 15 Menit', 'Tambahan', 'ExtraTime15Menit.png', 30000, 15, 'Tambahan waktu treatment selama 15 menit.'),
(8, 'Extra Time 30 Menit', 'Tambahan', 'ExtraTime30Menit.png', 40000, 30, 'Tambahan waktu treatment selama 30 menit.'),
(9, 'Aromaterapi Bakar', 'Tambahan', 'AromaterapiBakar.png', 15000, 0, 'Aromaterapi dengan aroma menenangkan untuk membantu menciptakan suasana rileks selama treatment.'),
(10, 'Dulang', 'Our Signature', 'Dulang_150Menit.jpeg', 259000, 150, 'Paket kombinasi lulur, pijat tubuh, dan totok wajah untuk memberikan perawatan relaksasi dan kecantikan secara menyeluruh.'),
(11, 'Talam', 'Our Signature', 'Talam_150Menit.png', 249000, 150, 'Kombinasi refleksi, pijat sehat, dan totok wajah untuk membantu tubuh lebih segar and wajah lebih rileks.'),
(12, 'Refleksi dan Pijat Sehat', 'Combo Paket', 'RefleksidanPijatSehat.jpeg', 189000, 120, 'Kombinasi refleksi kaki dan pijat sehat seluruh tubuh untuk mengurangi rasa lelah dan pegal.'),
(13, 'Pijat Sehat dan Bekam 9 Titik', 'Combo Paket', 'PijatSehatdanBekam9Titik.jpeg', 220000, 120, 'Paket pijat sehat dengan terapi bekam 9 titik untuk membantu melancarkan peredaran darah dan mengurangi pegal.'),
(14, 'Pijat Sehat dan Totok Wajah', 'Combo Paket', 'PijatSehatdanTotokWajah.jpeg', 209000, 120, 'Kombinasi pijat tubuh dan totok wajah untuk relaksasi tubuh sekaligus menyegarkan wajah.'),
(15, 'Lulur+', 'Lulur (+ Plus Pijat Sehat)', 'Lulur+.jpeg', 209000, 120, 'Perawatan lulur tubuh dan pijat sehat untuk membantu mengangkat sel kulit mati dan membuat tubuh rileks.'),
(16, 'Lulur Boreh+', 'Lulur (+ Plus Pijat Sehat)', 'LulurBoreh.jpeg', 220000, 120, 'Lulur boreh tradisional dipadukan dengan pijat sehat untuk membantu tubuh terasa hangat dan segar.'),
(17, 'Lulur Boreh Bali+', 'Lulur (+ Plus Pijat Sehat)', 'LulurBorehBali.jpeg', 230000, 120, 'Perawatan lulur khas Bali dengan pijat sehat untuk memberikan sensasi relaksasi dan perawatan tubuh premium.'),
(18, 'Totok Wajah', 'Spesial Treatment', 'TotokWajah.jpeg', 69000, 15, 'Treatment wajah dengan teknik penekanan titik tertentu untuk membantu wajah terasa segar and rileks.'),
(19, 'Kerok Badan', 'Spesial Treatment', 'KerokBadan.jpeg', 30000, 30, 'Treatment kerokan untuk membantu mengurangi masuk angin dan membuat tubuh terasa lebih ringan.'),
(20, 'Bekam Kering', 'Spesial Treatment', 'BekamKering.jpeg', 49000, 30, 'Terapi bekam tanpa sayatan untuk membantu melancarkan peredaran darah dan mengurangi pegal.'),
(21, 'Bekam Basah', 'Spesial Treatment', 'BekamBasah.jpeg', 119000, 45, 'Terapi bekam dengan metode pengeluaran darah kotor untuk membantu detoksifikasi tubuh.'),
(22, 'Tambahan 1 Titik Bekam Basah', 'Tambahan Bekam', 'Tambahan1TitikBekamBasah.png', 10000, 0, 'Tambahan satu titik area bekam basah sesuai kebutuhan pelanggan.'),
(23, 'Tambahan 1 Titik Bekam Kering', 'Tambahan Bekam', 'Tambahan1TitikBekamKering.png', 5000, 0, 'Tambahan satu titik area bekam kering sesuai kebutuhan pelanggan.');

-- 4. Seeding `ruangan`
INSERT INTO `ruangan` (`id_ruangan`, `nama_ruangan`, `status`) VALUES
(1, 'Room 1', 'aktif'),
(2, 'Room 2', 'aktif'),
(3, 'Room 3', 'aktif'),
(4, 'Room 4', 'aktif'),
(5, 'Room 5', 'aktif'),
(6, 'Room 6', 'aktif'),
(7, 'Room 7', 'aktif'),
(8, 'Room 8', 'aktif'),
(9, 'Room 9', 'aktif'),
(10, 'Room 10', 'aktif');

-- 5. Seeding `ulasan`
INSERT INTO `ulasan` (`id_ulasan`, `user_id`, `id_layanan`, `rating`, `ulasan`, `balasan_admin`) VALUES
(1, 2, 12, 5, 'Pijatan Hot Stone-nya pas banget di badan. Pegel-pegel abis kerja seharian langsung ilang dalam sekali sesi.', NULL),
(2, 3, 13, 5, 'Gampang banget booking lewat webnya, ga ribet. Terapisnya ramah, sopan, kamarnya juga wangi and bersih banget.', NULL),
(3, 4, 14, 4, 'Teknik deep tissue terapisnya mantap banget sih. Pas pulang kerasa enteng and seger banget badannya.', NULL),
(4, 5, 2, 5, 'Pijatannya teratur and ga bikin sakit. Ditambah wangi aromaterapi lavendernya bikin tenang, nyaris ketiduran tadi.', NULL),
(5, 6, 2, 5, 'Kualitas layanannya oke punya buat spa di Bandung. Suasananya tenang and privasinya bener-bener terjaga.', NULL);

-- 6. Seeding `pengaturan_halaman`
INSERT INTO `pengaturan_halaman` (`kunci`, `nilai`) VALUES
('featured_section_eyebrow', 'Our Best Value'),
('featured_section_title', 'Combo Packages'),
('featured_section_subtitle', 'Dapatkan pengalaman spa terlengkap dengan harga terbaik melalui paket kombinasi eksklusif kami.'),
('featured_section_category', 'Combo Paket'),
('interval_reservasi', '30'),
('sesi_pagi_mulai', '09:00'),
('sesi_pagi_selesai', '11:30'),
('sesi_siang_mulai', '12:00'),
('sesi_siang_selesai', '16:30'),
('sesi_sore_mulai', '17:00'),
('sesi_sore_selesai', '20:00');

-- 7. Seeding `rekening`
INSERT INTO `rekening` (`id_rekening`, `nama_bank`, `nomor_rekening`, `atas_nama`) VALUES 
(1, 'BCA', '1234567890', 'A.N. SPADMIN SPA'),
(2, 'MANDIRI', '9876543210', 'A.N. SPADMIN SPA');
