<?php

define('MENIT_KADALUWARSA_RESERVASI', 30);

function pastikanStrukturUser($conn) {
    $cek = $conn->query("SHOW TABLES LIKE 'users'");
    if ($cek->num_rows === 0) {
        buatTabelJikaPerlu($conn);
        isiDataAwal($conn);
    }
}

function buatTabelJikaPerlu($conn) {
    $conn->query("CREATE TABLE IF NOT EXISTS users (
        id_user INT AUTO_INCREMENT PRIMARY KEY,
        nama VARCHAR(100) NOT NULL,
        email VARCHAR(120) NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        no_telepon VARCHAR(20) NOT NULL,
        role VARCHAR(20) NOT NULL DEFAULT 'pelanggan',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    $cekKolomRating = $conn->query("SHOW COLUMNS FROM users LIKE 'rating_pelanggan'");
    if ($cekKolomRating->num_rows === 0) {
        $conn->query("ALTER TABLE users ADD COLUMN rating_pelanggan INT NOT NULL DEFAULT 5 AFTER role");
    }

    $cekKolomEmail = $conn->query("SHOW COLUMNS FROM users LIKE 'email'");
    $kolomEmail = $cekKolomEmail->fetch_assoc();
    if (($kolomEmail['Null'] ?? '') !== 'YES') {
        $conn->query("ALTER TABLE users MODIFY COLUMN email VARCHAR(120) NULL");
    }

    $conn->query("CREATE TABLE IF NOT EXISTS terapis (
        id_terapis INT AUTO_INCREMENT PRIMARY KEY,
        nama_terapis VARCHAR(100) NOT NULL,
        no_telp VARCHAR(20) NOT NULL,
        spesialisasi VARCHAR(120) NOT NULL,
        status VARCHAR(20) NOT NULL DEFAULT 'aktif',
        jenis_kelamin VARCHAR(20) NOT NULL DEFAULT 'Perempuan',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    $conn->query("CREATE TABLE IF NOT EXISTS layanan (
        id_layanan INT AUTO_INCREMENT PRIMARY KEY,
        nama_layanan VARCHAR(120) NOT NULL,
        kategori VARCHAR(100) NOT NULL DEFAULT 'Spa & Massage',
        media VARCHAR(255) NULL,
        harga INT NOT NULL,
        durasi INT NOT NULL,
        deskripsi TEXT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    $cekKolomMedia = $conn->query("SHOW COLUMNS FROM layanan LIKE 'media'");
    if ($cekKolomMedia->num_rows === 0) {
        $conn->query("ALTER TABLE layanan ADD COLUMN media VARCHAR(255) NULL AFTER nama_layanan");
    }

    $cekKolomKategori = $conn->query("SHOW COLUMNS FROM layanan LIKE 'kategori'");
    if ($cekKolomKategori->num_rows === 0) {
        $conn->query("ALTER TABLE layanan ADD COLUMN kategori VARCHAR(100) NOT NULL DEFAULT 'Spa & Massage' AFTER nama_layanan");
    }

    $conn->query("CREATE TABLE IF NOT EXISTS reservasi (
        id_reservasi INT AUTO_INCREMENT PRIMARY KEY,
        id_user INT NOT NULL,
        id_ruangan INT NULL,
        gender_terapis VARCHAR(20) NOT NULL DEFAULT 'Bebas',
        reservation_date DATETIME NOT NULL,
        reservation_type VARCHAR(20) NOT NULL DEFAULT 'online',
        status_reservation VARCHAR(40) NOT NULL DEFAULT 'Menunggu Pembayaran',
        total_price INT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (id_user) REFERENCES users(id_user) ON DELETE CASCADE,
        FOREIGN KEY (id_ruangan) REFERENCES ruangan(id_ruangan) ON DELETE SET NULL
    )");

    $cekKolomGender = $conn->query("SHOW COLUMNS FROM reservasi LIKE 'gender_terapis'");
    if ($cekKolomGender->num_rows === 0) {
        $conn->query("ALTER TABLE reservasi ADD COLUMN gender_terapis VARCHAR(20) NOT NULL DEFAULT 'Bebas'");
    }

    $conn->query("CREATE TABLE IF NOT EXISTS reservasi_detail (
        id_detail INT AUTO_INCREMENT PRIMARY KEY,
        id_reservasi INT NOT NULL,
        id_layanan INT NOT NULL,
        id_terapis INT NULL,
        qty INT NOT NULL DEFAULT 1,
        subtotal INT NOT NULL,
        FOREIGN KEY (id_reservasi) REFERENCES reservasi(id_reservasi) ON DELETE CASCADE,
        FOREIGN KEY (id_layanan) REFERENCES layanan(id_layanan) ON DELETE CASCADE,
        FOREIGN KEY (id_terapis) REFERENCES terapis(id_terapis) ON DELETE SET NULL
    )");

    $cekKolomTerapisDetail = $conn->query("SHOW COLUMNS FROM reservasi_detail LIKE 'id_terapis'");
    if ($cekKolomTerapisDetail->num_rows === 0) {
        $conn->query("ALTER TABLE reservasi_detail ADD COLUMN id_terapis INT NULL AFTER id_layanan");
        $conn->query("ALTER TABLE reservasi_detail ADD FOREIGN KEY (id_terapis) REFERENCES terapis(id_terapis) ON DELETE SET NULL");
    }

    $conn->query("CREATE TABLE IF NOT EXISTS payment (
        id_payment INT AUTO_INCREMENT PRIMARY KEY,
        id_reservasi INT NOT NULL,
        payment_method VARCHAR(50) NOT NULL,
        payment_proof VARCHAR(255) NOT NULL,
        payment_date DATETIME NOT NULL,
        status_payment VARCHAR(40) NOT NULL DEFAULT 'Menunggu Pembayaran',
        jenis_pembayaran VARCHAR(20) NOT NULL DEFAULT 'DP 50%',
        nominal_payment INT NOT NULL DEFAULT 0,
        verified_by VARCHAR(100) NULL,
        pelunasan_method VARCHAR(50) NULL,
        pelunasan_date DATETIME NULL,
        pelunasan_uang_bayar INT NOT NULL DEFAULT 0,
        pelunasan_kembalian INT NOT NULL DEFAULT 0,
        FOREIGN KEY (id_reservasi) REFERENCES reservasi(id_reservasi) ON DELETE CASCADE
    )");

    $cekKolomJenisPembayaran = $conn->query("SHOW COLUMNS FROM payment LIKE 'jenis_pembayaran'");
    if ($cekKolomJenisPembayaran->num_rows === 0) {
        $conn->query("ALTER TABLE payment ADD COLUMN jenis_pembayaran VARCHAR(20) NOT NULL DEFAULT 'DP 50%' AFTER status_payment");
    }

    $cekKolomNominalPembayaran = $conn->query("SHOW COLUMNS FROM payment LIKE 'nominal_payment'");
    if ($cekKolomNominalPembayaran->num_rows === 0) {
        $conn->query("ALTER TABLE payment ADD COLUMN nominal_payment INT NOT NULL DEFAULT 0 AFTER jenis_pembayaran");
        $conn->query("UPDATE payment p
                      JOIN reservasi r ON p.id_reservasi = r.id_reservasi
                      SET p.nominal_payment = CASE
                          WHEN r.reservation_type = 'online' THEN r.total_price * 0.5
                          ELSE r.total_price
                      END
                      WHERE p.nominal_payment = 0");
        $conn->query("UPDATE transaksi t
                      JOIN payment p ON t.id_reservasi = p.id_reservasi
                      JOIN reservasi r ON t.id_reservasi = r.id_reservasi
                      SET t.total_payment = p.nominal_payment,
                          t.uang_bayar = p.nominal_payment,
                          t.kembalian = 0
                      WHERE r.reservation_type = 'online'
                      AND p.status_payment NOT IN ('Lunas')");
    }

    $cekKolomMetodePelunasan = $conn->query("SHOW COLUMNS FROM payment LIKE 'pelunasan_method'");
    if ($cekKolomMetodePelunasan->num_rows === 0) {
        $conn->query("ALTER TABLE payment ADD COLUMN pelunasan_method VARCHAR(50) NULL AFTER verified_by");
    } else {
        $conn->query("ALTER TABLE payment MODIFY COLUMN pelunasan_method VARCHAR(50) NULL");
    }

    $cekKolomTanggalPelunasan = $conn->query("SHOW COLUMNS FROM payment LIKE 'pelunasan_date'");
    if ($cekKolomTanggalPelunasan->num_rows === 0) {
        $conn->query("ALTER TABLE payment ADD COLUMN pelunasan_date DATETIME NULL AFTER pelunasan_method");
    }

    $cekKolomUangPelunasan = $conn->query("SHOW COLUMNS FROM payment LIKE 'pelunasan_uang_bayar'");
    if ($cekKolomUangPelunasan->num_rows === 0) {
        $conn->query("ALTER TABLE payment ADD COLUMN pelunasan_uang_bayar INT NOT NULL DEFAULT 0 AFTER pelunasan_date");
    }

    $cekKolomKembalianPelunasan = $conn->query("SHOW COLUMNS FROM payment LIKE 'pelunasan_kembalian'");
    if ($cekKolomKembalianPelunasan->num_rows === 0) {
        $conn->query("ALTER TABLE payment ADD COLUMN pelunasan_kembalian INT NOT NULL DEFAULT 0 AFTER pelunasan_uang_bayar");
    }

    $conn->query("CREATE TABLE IF NOT EXISTS transaksi (
        id_transaksi INT AUTO_INCREMENT PRIMARY KEY,
        id_reservasi INT NOT NULL,
        transaction_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        total_payment INT NOT NULL,
        uang_bayar INT NOT NULL DEFAULT 0,
        kembalian INT NOT NULL DEFAULT 0,
        FOREIGN KEY (id_reservasi) REFERENCES reservasi(id_reservasi) ON DELETE CASCADE
    )");

    $cekKolomUangBayarTransaksi = $conn->query("SHOW COLUMNS FROM transaksi LIKE 'uang_bayar'");
    if ($cekKolomUangBayarTransaksi->num_rows === 0) {
        $conn->query("ALTER TABLE transaksi ADD COLUMN uang_bayar INT NOT NULL DEFAULT 0 AFTER total_payment");
        $conn->query("UPDATE transaksi SET uang_bayar = total_payment WHERE uang_bayar = 0");
    }

    $cekKolomKembalianTransaksi = $conn->query("SHOW COLUMNS FROM transaksi LIKE 'kembalian'");
    if ($cekKolomKembalianTransaksi->num_rows === 0) {
        $conn->query("ALTER TABLE transaksi ADD COLUMN kembalian INT NOT NULL DEFAULT 0 AFTER uang_bayar");
    }

    $conn->query("CREATE TABLE IF NOT EXISTS detail_transaksi (
        id_transaksi_detail INT AUTO_INCREMENT PRIMARY KEY,
        id_transaksi INT NOT NULL,
        id_layanan INT NOT NULL,
        qty INT NOT NULL DEFAULT 1,
        subtotal INT NOT NULL,
        FOREIGN KEY (id_transaksi) REFERENCES transaksi(id_transaksi) ON DELETE CASCADE,
        FOREIGN KEY (id_layanan) REFERENCES layanan(id_layanan) ON DELETE CASCADE
    )");

    $conn->query("CREATE TABLE IF NOT EXISTS ulasan (
        id_ulasan INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        reservasi_id INT NULL,
        id_layanan INT NOT NULL,
        rating INT NOT NULL,
        ulasan TEXT NOT NULL,
        balasan_admin TEXT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (reservasi_id) REFERENCES reservasi(id_reservasi) ON DELETE SET NULL,
        FOREIGN KEY (user_id) REFERENCES users(id_user) ON DELETE CASCADE,
        FOREIGN KEY (id_layanan) REFERENCES layanan(id_layanan) ON DELETE CASCADE
    )");

    $cekKolomReservasiUlasan = $conn->query("SHOW COLUMNS FROM ulasan LIKE 'reservasi_id'");
    if ($cekKolomReservasiUlasan->num_rows === 0) {
        $conn->query("ALTER TABLE ulasan ADD COLUMN reservasi_id INT NULL AFTER user_id");
    }

    $conn->query("CREATE TABLE IF NOT EXISTS pengaturan_halaman (
        kunci VARCHAR(50) PRIMARY KEY,
        nilai TEXT NOT NULL
    )");

    $conn->query("CREATE TABLE IF NOT EXISTS ruangan (
        id_ruangan INT AUTO_INCREMENT PRIMARY KEY,
        nama_ruangan VARCHAR(50) NOT NULL UNIQUE,
        status VARCHAR(20) NOT NULL DEFAULT 'aktif'
    )");

    $cekKolomRuangan = $conn->query("SHOW COLUMNS FROM reservasi LIKE 'id_ruangan'");
    if ($cekKolomRuangan->num_rows === 0) {
        $conn->query("ALTER TABLE reservasi ADD COLUMN id_ruangan INT NULL AFTER gender_terapis");
        $conn->query("ALTER TABLE reservasi ADD FOREIGN KEY (id_ruangan) REFERENCES ruangan(id_ruangan) ON DELETE SET NULL");
    }

    $conn->query("CREATE TABLE IF NOT EXISTS rekening (
        id_rekening INT AUTO_INCREMENT PRIMARY KEY,
        nama_bank VARCHAR(50) NOT NULL,
        nomor_rekening VARCHAR(50) NOT NULL,
        atas_nama VARCHAR(100) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
}

function isiDataAwal($conn) {
    seedLayanan($conn);
    seedTerapis($conn);
    seedAdmin($conn);
    seedUlasan($conn);
    seedPengaturan($conn);
    seedRuangan($conn);
    seedRekening($conn);
}

function seedLayanan($conn) {
    $cek = $conn->query("SELECT COUNT(*) AS total FROM layanan");
    $total = $cek->fetch_assoc()['total'] ?? 0;
    if ((int)$total > 0) {
        return;
    }

    $dataLayanan = [
        ['Pijat Belakang',                'Pijat',                    'Pijat fokus pada area punggung, pinggang, dan belakang betis kaki untuk membantu meredakan pegal dan melancarkan peredaran darah.',   45,  79000],
        ['Pijat Sehat',                   'Pijat',                    'Pijat relaksasi seluruh tubuh untuk mengurangi stres, mengatasi pegal, dan membuat tubuh lebih rileks.',                              90, 129000],
        ['Pijat Sehat',                   'Pijat',                    'Pijat seluruh tubuh dengan durasi lebih lama sehingga tubuh terasa lebih segar, nyaman, dan rileks maksimal.',                        120, 149000],
        ['Refleksi',                      'Refleksi',                 'Terapi pijat refleksi pada titik saraf kaki untuk membantu melancarkan sirkulasi darah dan mengurangi kelelahan.',                     60,  89000],
        ['Refleksi',                      'Refleksi',                 'Refleksi kaki dengan durasi lebih lama untuk memberikan efek relaksasi dan kenyamanan yang lebih maksimal.',                           90, 109000],
        ['Garam Rendam Kaki',             'Refleksi',                 'Perawatan rendam kaki menggunakan garam untuk membantu relaksasi and mengurangi pegal pada kaki.',                                     15,  12000],
        ['Extra Time 15 Menit',           'Tambahan',                 'Tambahan waktu treatment selama 15 menit.',                                                                                             15,  30000],
        ['Extra Time 30 Menit',           'Tambahan',                 'Tambahan waktu treatment selama 30 menit.',                                                                                             30,  40000],
        ['Aromaterapi Bakar',             'Tambahan',                 'Aromaterapi dengan aroma menenangkan untuk membantu menciptakan suasana rileks selama treatment.',                                       0,  15000],
        ['Dulang',                        'Our Signature',            'Paket kombinasi lulur, pijat tubuh, dan totok wajah untuk memberikan perawatan relaksasi dan kecantikan secara menyeluruh.',          150, 259000],
        ['Talam',                         'Our Signature',            'Kombinasi refleksi, pijat sehat, dan totok wajah untuk membantu tubuh lebih segar dan wajah lebih rileks.',                          150, 249000],
        ['Refleksi dan Pijat Sehat',      'Combo Paket',              'Kombinasi refleksi kaki dan pijat sehat seluruh tubuh untuk mengurangi rasa lelah dan pegal.',                                       120, 189000],
        ['Pijat Sehat dan Bekam 9 Titik', 'Combo Paket',              'Paket pijat sehat dengan terapi bekam 9 titik untuk membantu melancarkan peredaran darah dan mengurangi pegal.',                     120, 220000],
        ['Pijat Sehat dan Totok Wajah',   'Combo Paket',              'Kombinasi pijat tubuh dan totok wajah untuk relaksasi tubuh sekaligus menyegarkan wajah.',                                          120, 209000],
        ['Lulur+',                        'Lulur (+ Plus Pijat Sehat)', 'Perawatan lulur tubuh dan pijat sehat untuk membantu mengangkat sel kulit mati dan membuat tubuh rileks.',                         120, 209000],
        ['Lulur Boreh+',                  'Lulur (+ Plus Pijat Sehat)', 'Lulur boreh tradisional dipadukan dengan pijat sehat untuk membantu tubuh terasa hangat dan segar.',                              120, 220000],
        ['Lulur Boreh Bali+',             'Lulur (+ Plus Pijat Sehat)', 'Perawatan lulur khas Bali dengan pijat sehat untuk memberikan sensasi relaksasi dan perawatan tubuh premium.',                    120, 230000],
        ['Totok Wajah',                   'Spesial Treatment',        'Treatment wajah dengan teknik penekanan titik tertentu untuk membantu wajah terasa segar and rileks.',                                 15,  69000],
        ['Kerok Badan',                   'Spesial Treatment',        'Treatment kerokan untuk membantu mengurangi masuk angin dan membuat tubuh terasa lebih ringan.',                                       30,  30000],
        ['Bekam Kering',                  'Spesial Treatment',        'Terapi bekam tanpa sayatan untuk membantu melancarkan peredaran darah dan mengurangi pegal.',                                          30,  49000],
        ['Bekam Basah',                   'Spesial Treatment',        'Terapi bekam dengan metode pengeluaran darah kotor untuk membantu detoksifikasi tubuh.',                                               45, 119000],
        ['Tambahan 1 Titik Bekam Basah',  'Tambahan Bekam',           'Tambahan satu titik area bekam basah sesuai kebutuhan pelanggan.',                                                                      0,  10000],
        ['Tambahan 1 Titik Bekam Kering', 'Tambahan Bekam',           'Tambahan satu titik area bekam kering sesuai kebutuhan pelanggan.',                                                                     0,   5000],
    ];

    $stmt = $conn->prepare("INSERT INTO layanan (nama_layanan, kategori, media, deskripsi, durasi, harga) VALUES (?, ?, ?, ?, ?, ?)");
    foreach ($dataLayanan as $layanan) {
        [$nama, $kategori, $deskripsi, $durasi, $harga] = $layanan;
        $gambar = resolveDefaultImage($nama, $durasi);
        $stmt->bind_param("ssssii", $nama, $kategori, $gambar, $deskripsi, $durasi, $harga);
        $stmt->execute();
    }
    $stmt->close();
}

function resolveDefaultImage($namaLayanan, $durasi) {
    $peta = [
        'Pijat Belakang'                => ['45' => 'PijatBelakang.jpeg'],
        'Pijat Sehat'                   => ['90' => 'PijatSehat.jpeg', '120' => 'PijatSehat(Premium).jpeg'],
        'Refleksi'                      => ['60' => 'Refleksi.jpeg', '90' => 'Refleksi(Premium).jpeg'],
        'Garam Rendam Kaki'             => ['15' => 'GaramRendamKaki.jpeg'],
        'Extra Time 15 Menit'           => ['15' => 'ExtraTime15Menit.png'],
        'Extra Time 30 Menit'           => ['30' => 'ExtraTime30Menit.png'],
        'Aromaterapi Bakar'             => ['0'  => 'AromaterapiBakar.png'],
        'Dulang'                        => ['150' => 'Dulang_150Menit.jpeg'],
        'Talam'                         => ['150' => 'Talam_150Menit.png'],
        'Refleksi dan Pijat Sehat'      => ['120' => 'RefleksidanPijatSehat.jpeg'],
        'Pijat Sehat dan Bekam 9 Titik' => ['120' => 'PijatSehatdanBekam9Titik.jpeg'],
        'Pijat Sehat dan Totok Wajah'   => ['120' => 'PijatSehatdanTotokWajah.jpeg'],
        'Lulur+'                        => ['120' => 'Lulur+.jpeg'],
        'Lulur Boreh+'                  => ['120' => 'LulurBoreh.jpeg'],
        'Lulur Boreh Bali+'             => ['120' => 'LulurBorehBali.jpeg'],
        'Totok Wajah'                   => ['15' => 'TotokWajah.jpeg'],
        'Kerok Badan'                   => ['30' => 'KerokBadan.jpeg'],
        'Bekam Kering'                  => ['30' => 'BekamKering.jpeg'],
        'Bekam Basah'                   => ['45' => 'BekamBasah.jpeg'],
        'Tambahan 1 Titik Bekam Basah'  => ['0'  => 'Tambahan1TitikBekamBasah.png'],
        'Tambahan 1 Titik Bekam Kering' => ['0'  => 'Tambahan1TitikBekamKering.png'],
    ];

    if (isset($peta[$namaLayanan][(string)$durasi])) {
        return $peta[$namaLayanan][(string)$durasi];
    }
    if (isset($peta[$namaLayanan])) {
        return reset($peta[$namaLayanan]);
    }

    return 'https://images.unsplash.com/photo-1540555700478-4be289fbecef?auto=format&fit=crop&w=900&q=80';
}

function seedTerapis($conn) {
    $dataTerapis = [
        ['Maya Putri',    '081234567890', 'Pijat Sehat, Lulur, dan Dulang',              'Perempuan'],
        ['Nadia Safira',  '081234567891', 'Totok Wajah, Pijat Sehat, dan Talam',         'Perempuan'],
        ['Rani Amelia',   '081234567892', 'Refleksi, Bekam Basah, dan Bekam Kering',     'Perempuan'],
        ['Budi Santoso',  '081234567893', 'Bekam Kering, Refleksi, dan Kerok Badan',     'Pria'],
        ['Dewi Lestari',  '081234567894', 'Lulur, Dulang, dan Garam Rendam Kaki',        'Perempuan'],
    ];

    $stmtInsert = $conn->prepare("INSERT INTO terapis (nama_terapis, no_telp, spesialisasi, jenis_kelamin) VALUES (?, ?, ?, ?)");
    $stmtCek    = $conn->prepare("SELECT COUNT(*) AS total FROM terapis WHERE nama_terapis = ?");

    foreach ($dataTerapis as [$nama, $telp, $spesialis, $gender]) {
        $stmtCek->bind_param("s", $nama);
        $stmtCek->execute();
        $ada = (int) $stmtCek->get_result()->fetch_assoc()['total'];

        if ($ada === 0) {
            $stmtInsert->bind_param("ssss", $nama, $telp, $spesialis, $gender);
            $stmtInsert->execute();
        }
    }

    $stmtInsert->close();
    $stmtCek->close();
}

function seedAdmin($conn) {
    $emailAdmin = 'admin@spadmin.com';
    $stmt = $conn->prepare("SELECT COUNT(*) AS total FROM users WHERE email = ?");
    $stmt->bind_param("s", $emailAdmin);
    $stmt->execute();
    $sudahAda = (int) $stmt->get_result()->fetch_assoc()['total'];
    $stmt->close();

    if ($sudahAda > 0) return;

    $nama  = 'Diah(Admin)';
    $pass  = password_hash('admin123', PASSWORD_DEFAULT);
    $telp  = '08123456789';
    $role  = 'admin';
    $stmtInsert = $conn->prepare("INSERT INTO users (nama, email, password, no_telepon, role) VALUES (?, ?, ?, ?, ?)");
    $stmtInsert->bind_param("sssss", $nama, $emailAdmin, $pass, $telp, $role);
    $stmtInsert->execute();
    $stmtInsert->close();
}

function cariPelangganByEmail($conn, $email) {
    $stmt = $conn->prepare("SELECT id_user AS id, nama, email, password, no_telepon AS telepon, role, created_at FROM users WHERE email = ? LIMIT 1");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    return $user;
}

function cariPelangganById($conn, $id) {
    $stmt = $conn->prepare("SELECT id_user AS id, nama, email, password, no_telepon AS telepon, role, created_at FROM users WHERE id_user = ? LIMIT 1");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    return $user;
}

function emailPelangganSudahAda($conn, $email) {
    $stmt = $conn->prepare("SELECT id_user FROM users WHERE email = ? LIMIT 1");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $ada = $stmt->get_result()->num_rows > 0;
    $stmt->close();
    return $ada;
}

function buatPelanggan($conn, $nama, $email, $telepon, $password) {
    $passwordHash = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $conn->prepare("INSERT INTO users (nama, email, password, no_telepon, role) VALUES (?, ?, ?, ?, 'pelanggan')");
    $stmt->bind_param("ssss", $nama, $email, $passwordHash, $telepon);
    $berhasil = $stmt->execute();
    $stmt->close();
    return $berhasil;
}

function ambilRingkasanPelanggan($conn, $userId) {
    $stmt = $conn->prepare("SELECT
        COUNT(*) AS total_reservasi,
        SUM(status_reservation = 'Menunggu Validasi') AS menunggu,
        SUM(status_reservation = 'Diterima') AS diterima,
        SUM(status_reservation = 'Selesai') AS selesai,
        SUM(status_reservation = 'Hangus') AS hangus
        FROM reservasi WHERE id_user = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $ringkasan = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    return $ringkasan;
}

function seedPengaturan($conn) {
    $sudahAda = $conn->query("SELECT COUNT(*) AS total FROM pengaturan_halaman")->fetch_assoc()['total'] ?? 0;
    if ((int)$sudahAda > 0) return;
    $defaultSettings = [
        ['featured_section_eyebrow', 'Our Best Value'],
        ['featured_section_title', 'Combo Packages'],
        ['featured_section_subtitle', 'Dapatkan pengalaman spa terlengkap dengan harga terbaik melalui paket kombinasi eksklusif kami.'],
        ['featured_section_category', 'Combo Paket'],
        ['interval_reservasi', '30'],
        ['sesi_pagi_mulai', '09:00'],
        ['sesi_pagi_selesai', '11:30'],
        ['sesi_siang_mulai', '12:00'],
        ['sesi_siang_selesai', '16:30'],
        ['sesi_sore_mulai', '17:00'],
        ['sesi_sore_selesai', '20:00']
    ];
    $stmt = $conn->prepare("INSERT INTO pengaturan_halaman (kunci, nilai) VALUES (?, ?)");
    foreach ($defaultSettings as $setting) {
        $stmt->bind_param("ss", $setting[0], $setting[1]);
        $stmt->execute();
    }
    $stmt->close();
}

function seedRuangan($conn) {
    $defaultRooms = ['Room 1', 'Room 2', 'Room 3', 'Room 4', 'Room 5', 'Room 6', 'Room 7', 'Room 8', 'Room 9', 'Room 10'];
    $stmt = $conn->prepare("INSERT IGNORE INTO ruangan (nama_ruangan, status) VALUES (?, 'aktif')");
    foreach ($defaultRooms as $room) {
        $stmt->bind_param("s", $room);
        $stmt->execute();
    }
    $stmt->close();
}

function ambilPengaturan($conn, $kunci, $default = '') {
    $stmt = $conn->prepare("SELECT nilai FROM pengaturan_halaman WHERE kunci = ? LIMIT 1");
    if ($stmt) {
        $stmt->bind_param("s", $kunci);
        $stmt->execute();
        $res = $stmt->get_result();
        $row = $res->fetch_assoc();
        $stmt->close();
        if ($row) {
            return $row['nilai'];
        }
    }
    return $default;
}

function generasiSlotJam($mulai, $selesai, $interval) {
    $mulaiTS = strtotime($mulai);
    $selesaiTS = strtotime($selesai);
    if (!$mulaiTS || !$selesaiTS || $mulaiTS > $selesaiTS || $interval <= 0) {
        return '';
    }
    $slots = [];
    $curr = $mulaiTS;
    while ($curr <= $selesaiTS) {
        $slots[] = date('H:i', $curr);
        $curr += $interval * 60;
    }
    return implode(',', $slots);
}

function validasiPasswordPelanggan($password, $label = 'Password') {
    if (strlen($password) < 8) {
        return $label . " minimal 8 karakter.";
    }

    if (strlen($password) > 255) {
        return $label . " maksimal 255 karakter.";
    }

    if (!preg_match('/[A-Z]/', $password)) {
        return $label . " harus memiliki minimal 1 huruf besar (A-Z).";
    }

    if (!preg_match('/[^A-Za-z0-9]/', $password)) {
        return $label . " harus memiliki minimal 1 karakter khusus.";
    }

    return null;
}

function ambilLayanan($conn, $keyword = '', $kategori = '', $durasi = '', $sort = '') {
    $sql    = "SELECT id_layanan AS id, nama_layanan, kategori, media, harga, durasi, deskripsi, created_at FROM layanan WHERE 1=1";
    $params = [];
    $types  = "";

    if ($keyword !== '') {
        $sql   .= " AND (nama_layanan LIKE ? OR deskripsi LIKE ? OR kategori LIKE ?)";
        $like   = "%$keyword%";
        $params = array_merge($params, [$like, $like, $like]);
        $types .= "sss";
    }

    if ($kategori !== '') {
        $sql   .= " AND kategori = ?";
        $params[] = $kategori;
        $types   .= "s";
    }

    if ($durasi === 'singkat') {
        $sql .= " AND durasi > 0 AND durasi <= 60";
    } elseif ($durasi === 'sedang') {
        $sql .= " AND durasi > 60 AND durasi <= 90";
    } elseif ($durasi === 'panjang') {
        $sql .= " AND durasi > 90";
    }

    $orderBy = match($sort) {
        'harga-terendah'   => "harga ASC, id_layanan ASC",
        'harga-tertinggi'  => "harga DESC, id_layanan ASC",
        'durasi-terpendek' => "durasi ASC, id_layanan ASC",
        'durasi-terpanjang'=> "durasi DESC, id_layanan ASC",
        default            => "id_layanan DESC",
    };
    $sql .= " ORDER BY $orderBy";

    $stmt = $conn->prepare($sql);
    if ($params) {
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    $hasil = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    return $hasil;
}

function ambilKategoriLayanan($conn) {
    $hasil = $conn->query("SELECT DISTINCT kategori FROM layanan ORDER BY kategori ASC");
    return $hasil->fetch_all(MYSQLI_ASSOC);
}

function ambilLayananById($conn, $id) {
    $stmt = $conn->prepare("SELECT id_layanan AS id, nama_layanan, kategori, media, harga, durasi, deskripsi, created_at FROM layanan WHERE id_layanan = ? LIMIT 1");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $layanan = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    return $layanan;
}

function ambilTerapis($conn) {
    $hasil = $conn->query("SELECT id_terapis AS id, nama_terapis, no_telp, spesialisasi, status, jenis_kelamin, created_at FROM terapis ORDER BY nama_terapis ASC");
    return $hasil->fetch_all(MYSQLI_ASSOC);
}

function ambilTerapisById($conn, $id) {
    $stmt = $conn->prepare("SELECT id_terapis AS id, nama_terapis, no_telp, spesialisasi, status, jenis_kelamin, created_at FROM terapis WHERE id_terapis = ? LIMIT 1");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $terapis = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    return $terapis;
}

function buildQueryReservasi() {
    return "SELECT
        r.id_reservasi AS id,
        r.id_user AS user_id,
        (SELECT MIN(rd5.id_terapis) FROM reservasi_detail rd5 WHERE rd5.id_reservasi = r.id_reservasi) AS terapis_id,
        DATE(r.reservation_date) AS tanggal,
        TIME_FORMAT(TIME(r.reservation_date), '%H:%i') AS jam,
        '' AS catatan,
        r.status_reservation AS status_reservasi,
        COALESCE((SELECT p.status_payment FROM payment p WHERE p.id_reservasi = r.id_reservasi ORDER BY p.id_payment DESC LIMIT 1), 'Belum Upload') AS status_pembayaran,
        (SELECT p.payment_proof FROM payment p WHERE p.id_reservasi = r.id_reservasi ORDER BY p.id_payment DESC LIMIT 1) AS nama_file,
        r.total_price AS harga,
        r.total_price AS total_price,
        r.created_at,
        GROUP_CONCAT(DISTINCT l.nama_layanan ORDER BY rd.id_detail ASC SEPARATOR ', ') AS nama_layanan,
        (SELECT MIN(l2.media) FROM reservasi_detail rd2 JOIN layanan l2 ON l2.id_layanan = rd2.id_layanan WHERE rd2.id_reservasi = r.id_reservasi) AS media,
        (SELECT SUM(l3.durasi) FROM reservasi_detail rd3 JOIN layanan l3 ON l3.id_layanan = rd3.id_layanan WHERE rd3.id_reservasi = r.id_reservasi) AS durasi,
        (SELECT MIN(rd4.id_layanan) FROM reservasi_detail rd4 WHERE rd4.id_reservasi = r.id_reservasi) AS layanan_id,
        (SELECT GROUP_CONCAT(t2.nama_terapis SEPARATOR ', ') FROM reservasi_detail rd2 JOIN terapis t2 ON rd2.id_terapis = t2.id_terapis WHERE rd2.id_reservasi = r.id_reservasi) AS nama_terapis
        FROM reservasi r
        JOIN reservasi_detail rd ON rd.id_reservasi = r.id_reservasi
        JOIN layanan l ON l.id_layanan = rd.id_layanan";
}

function ambilReservasiPelanggan($conn, $userId) {
    $sql  = buildQueryReservasi() . " WHERE r.id_user = ? GROUP BY r.id_reservasi ORDER BY r.reservation_date DESC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $data = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    return $data;
}

function ambilReservasiById($conn, $id, $userId) {
    $sql  = buildQueryReservasi() . " WHERE r.id_reservasi = ? AND r.id_user = ? GROUP BY r.id_reservasi LIMIT 1";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $id, $userId);
    $stmt->execute();
    $data = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    return $data;
}

function checkSpecializationMatch($therapistSpec, $serviceName, $serviceCategory) {
    $therapistSpec = strtolower($therapistSpec);
    $serviceName = strtolower($serviceName);
    $serviceCategory = strtolower($serviceCategory);

    // Add-ons / Tambahan / Extra Time do not require specific specializations
    if ($serviceCategory === 'tambahan' || stripos($serviceName, 'extra time') !== false || stripos($serviceName, 'aromaterapi') !== false) {
        return true;
    }

    // Direct check
    if (stripos($therapistSpec, $serviceName) !== false || stripos($serviceName, $therapistSpec) !== false) {
        return true;
    }

    // Specific Bekam handling to avoid mismatch
    if (stripos($serviceName, 'bekam') !== false) {
        if (stripos($serviceName, 'kering') !== false && stripos($therapistSpec, 'bekam kering') !== false) {
            return true;
        }
        if (stripos($serviceName, 'basah') !== false && stripos($therapistSpec, 'bekam basah') !== false) {
            return true;
        }
        // Fallback for generic bekam if service is generic
        if (stripos($therapistSpec, 'bekam') !== false && stripos($serviceName, 'kering') === false && stripos($serviceName, 'basah') === false) {
            return true;
        }
        return false;
    }

    // Split service name into keywords
    $cleanService = str_replace(['dan', 'and', '+', '(', ')', ',', '&'], ' ', $serviceName);
    $keywords = array_filter(array_map('trim', explode(' ', $cleanService)));
    
    foreach ($keywords as $kw) {
        if (strlen($kw) < 4) continue;
        if (stripos($therapistSpec, $kw) !== false) {
            return true;
        }
    }

    // Category fallback (for categories that represent skills e.g., Pijat, Refleksi, Lulur)
    if (!in_array($serviceCategory, ['spesial treatment', 'combo paket', 'our signature'])) {
        if (stripos($therapistSpec, $serviceCategory) !== false) {
            return true;
        }
    }

    return false;
}

function simpanReservasi($conn, $userId, $layananIds, $genderTerapis, $tanggal, $jam, $catatan) {
    if (!is_array($layananIds) || empty($layananIds)) return 0;

    $items = [];
    $totalPrice = 0;
    $categories = [];
    $totalDuration = 0;
    foreach ($layananIds as $lid) {
        $layanan = ambilLayananById($conn, $lid);
        if ($layanan) {
            $totalPrice += (int)$layanan['harga'];
            $categories[] = $layanan['kategori'];
            $totalDuration += (int)$layanan['durasi'];
            $items[] = ['id' => $lid, 'harga' => (int)$layanan['harga']];
        }
    }

    if (empty($items)) return 0;

    $datetime = $tanggal . ' ' . $jam . ':00';
    $startTS = strtotime($datetime);
    if (!$startTS) return 0;
    $endTS = $startTS + ($totalDuration * 60);
    $endDateTime = date('Y-m-d H:i:s', $endTS);

    if (!slotBeradaDalamJamOperasional($conn, $jam, $totalDuration)) {
        throw new Exception("Jam kedatangan tidak tersedia karena durasi layanan melewati jam operasional spa.");
    }

    // Ambil daftar terapis sibuk pada jadwal tersebut
    $occupiedTherapists = getOccupiedTherapistsForSlot($conn, $datetime, $endDateTime);

    // Dapatkan semua terapis aktif
    $allTherapists = [];
    $resTerapis = $conn->query("SELECT * FROM terapis WHERE status = 'aktif'");
    if ($resTerapis) {
        while ($t = $resTerapis->fetch_assoc()) {
            $allTherapists[] = $t;
        }
    }

    $assignedTherapistsMap = [];
    $assignedInThisBooking = [];

    foreach ($items as $item) {
        $layanan = ambilLayananById($conn, $item['id']);
        $cat = $layanan['kategori'] ?? '';

        $matching = [];
        foreach ($allTherapists as $t) {
            $catMatch = checkSpecializationMatch($t['spesialisasi'], $layanan['nama_layanan'], $cat);
            if (!$catMatch) continue;

            $genderMatch = false;
            if ($genderTerapis === 'Bebas') {
                $genderMatch = true;
            } elseif ($genderTerapis === 'Perempuan' && $t['jenis_kelamin'] === 'Perempuan') {
                $genderMatch = true;
            } elseif (($genderTerapis === 'Laki-Laki' || $genderTerapis === 'Pria') && ($t['jenis_kelamin'] === 'Pria' || $t['jenis_kelamin'] === 'Laki-Laki')) {
                $genderMatch = true;
            }

            if ($genderMatch) {
                $matching[] = $t;
            }
        }

        $foundT = null;
        // Prioritas 1: terapis kosong dan belum dipasang di bookingan ini
        foreach ($matching as $t) {
            if (!in_array((int)$t['id_terapis'], $occupiedTherapists) && !in_array((int)$t['id_terapis'], $assignedInThisBooking)) {
                $foundT = $t;
                break;
            }
        }
        // Prioritas 2: fallback asal terapis kosong (boleh double jika terapis terbatas)
        if (!$foundT) {
            foreach ($matching as $t) {
                if (!in_array((int)$t['id_terapis'], $occupiedTherapists)) {
                    $foundT = $t;
                    break;
                }
            }
        }

        if (!$foundT) {
            throw new Exception("Maaf, tidak ada terapis dengan keahlian '" . $cat . "' yang tersedia pada jam tersebut. Silakan pilih waktu lain.");
        }

        $assignedTherapistsMap[$item['id']] = (int)$foundT['id_terapis'];
        $assignedInThisBooking[] = (int)$foundT['id_terapis'];
    }

    $rooms = [];
    $resRooms = $conn->query("SELECT * FROM ruangan WHERE status = 'aktif'");
    if ($resRooms) {
        while ($r = $resRooms->fetch_assoc()) {
            $rooms[] = $r;
        }
    }

    $occupiedRooms = [];
    $stmtR = $conn->prepare("
        SELECT DISTINCT r.id_ruangan 
        FROM reservasi r
        WHERE r.id_ruangan IS NOT NULL
          AND r.status_reservation IN ('Menunggu Pembayaran', 'Menunggu Validasi', 'Diterima', 'Dikonfirmasi')
          AND r.reservation_date < ?
          AND DATE_ADD(r.reservation_date, INTERVAL (
              SELECT SUM(l2.durasi) 
              FROM reservasi_detail rd2 
              JOIN layanan l2 ON l2.id_layanan = rd2.id_layanan 
              WHERE rd2.id_reservasi = r.id_reservasi
          ) MINUTE) > ?
    ");
    $stmtR->bind_param("ss", $endDateTime, $datetime);
    $stmtR->execute();
    $resR = $stmtR->get_result();
    while ($row = $resR->fetch_assoc()) {
        $occupiedRooms[] = (int)$row['id_ruangan'];
    }
    $stmtR->close();

    $availableRooms = [];
    foreach ($rooms as $r) {
        if (!in_array((int)$r['id_ruangan'], $occupiedRooms)) {
            $availableRooms[] = $r;
        }
    }

    if (empty($availableRooms)) {
        throw new Exception("Maaf, seluruh ruangan kami penuh pada jam tersebut. Silakan pilih waktu lain.");
    }

    $assignedRoom = $availableRooms[0];

    $conn->begin_transaction();
    try {
        $stmt = $conn->prepare("INSERT INTO reservasi (id_user, id_ruangan, gender_terapis, reservation_date, reservation_type, status_reservation, total_price) VALUES (?, ?, ?, ?, 'online', 'Menunggu Pembayaran', ?)");
        $stmt->bind_param("iissi", $userId, $assignedRoom['id_ruangan'], $genderTerapis, $datetime, $totalPrice);
        $stmt->execute();
        $reservasiId = $conn->insert_id;
        $stmt->close();

        if ($reservasiId <= 0) {
            $conn->rollback();
            return 0;
        }

        $stmtDetail = $conn->prepare("INSERT INTO reservasi_detail (id_reservasi, id_layanan, id_terapis, qty, subtotal) VALUES (?, ?, ?, 1, ?)");
        foreach ($items as $item) {
            $tId = $assignedTherapistsMap[$item['id']];
            $stmtDetail->bind_param("iiii", $reservasiId, $item['id'], $tId, $item['harga']);
            $stmtDetail->execute();
        }
        $stmtDetail->close();

        $conn->commit();
        return $reservasiId;
    } catch (Exception $e) {
        $conn->rollback();
        return 0;
    }
}

function getOccupiedTherapistsForSlot($conn, $datetime, $endDateTime) {
    // 1. Dapatkan semua terapis aktif
    $allTherapists = [];
    $resT = $conn->query("SELECT * FROM terapis WHERE status = 'aktif'");
    if ($resT) {
        while ($row = $resT->fetch_assoc()) {
            $allTherapists[] = $row;
        }
    }

    // 2. Ambil daftar detail reservasi aktif yang overlap pada jadwal tersebut
    $activeDetails = [];
    $stmtOverlap = $conn->prepare("
        SELECT rd.id_detail, rd.id_layanan, rd.id_terapis, l.kategori, l.nama_layanan
        FROM reservasi_detail rd
        JOIN reservasi r ON rd.id_reservasi = r.id_reservasi
        JOIN layanan l ON rd.id_layanan = l.id_layanan
        WHERE r.status_reservation IN ('Menunggu Pembayaran', 'Menunggu Validasi', 'Diterima', 'Dikonfirmasi')
          AND r.reservation_date < ?
          AND DATE_ADD(r.reservation_date, INTERVAL (
              SELECT SUM(l2.durasi) 
              FROM reservasi_detail rd2 
              JOIN layanan l2 ON l2.id_layanan = rd2.id_layanan 
              WHERE rd2.id_reservasi = r.id_reservasi
          ) MINUTE) > ?
    ");
    if (!$stmtOverlap) {
        return [];
    }
    $stmtOverlap->bind_param("ss", $endDateTime, $datetime);
    $stmtOverlap->execute();
    $resOverlap = $stmtOverlap->get_result();
    while ($row = $resOverlap->fetch_assoc()) {
        $activeDetails[] = $row;
    }
    $stmtOverlap->close();

    $occupiedTherapists = [];
    $unassignedDetails = [];
    foreach ($activeDetails as $detail) {
        if ($detail['id_terapis'] !== null) {
            $occupiedTherapists[] = (int)$detail['id_terapis'];
        } else {
            $unassignedDetails[] = $detail;
        }
    }

    // 3. Simulasikan penugasan terapis untuk detail reservasi yang belum ditugaskan (id_terapis IS NULL)
    foreach ($unassignedDetails as $detail) {
        $cat = $detail['kategori'] ?? '';
        $foundSimT = null;
        foreach ($allTherapists as $t) {
            $tId = (int)$t['id_terapis'];
            if (in_array($tId, $occupiedTherapists)) continue;

            $catMatch = checkSpecializationMatch($t['spesialisasi'], $detail['nama_layanan'], $cat);

            if ($catMatch) {
                $foundSimT = $tId;
                break;
            }
        }

        if ($foundSimT !== null) {
            $occupiedTherapists[] = $foundSimT;
        }
    }

    return $occupiedTherapists;
}

function cekKetersediaanSlot($conn, $layananIds, $genderTerapis, $tanggal, $jam) {
    if (!is_array($layananIds) || empty($layananIds)) return true;

    $totalDuration = 0;
    $services = [];
    foreach ($layananIds as $lid) {
        $layanan = ambilLayananById($conn, $lid);
        if ($layanan) {
            $totalDuration += (int)$layanan['durasi'];
            $services[] = $layanan;
        }
    }
    if (empty($services)) return true;

    if (!slotBeradaDalamJamOperasional($conn, $jam, $totalDuration)) {
        return false;
    }

    $datetime = $tanggal . ' ' . $jam . ':00';
    $startTS = strtotime($datetime);
    if (!$startTS) return false;
    $endTS = $startTS + ($totalDuration * 60);
    $endDateTime = date('Y-m-d H:i:s', $endTS);

    $allTherapists = [];
    $resTerapis = $conn->query("SELECT * FROM terapis WHERE status = 'aktif'");
    if ($resTerapis) {
        while ($t = $resTerapis->fetch_assoc()) {
            $allTherapists[] = $t;
        }
    }

    $occupiedTherapists = getOccupiedTherapistsForSlot($conn, $datetime, $endDateTime);

    // Untuk setiap layanan, harus ada minimal satu terapis yang sedia dan cocok spesialisasinya
    foreach ($services as $svc) {
        $cat = $svc['kategori'] ?? '';
        $anyAvailable = false;
        foreach ($allTherapists as $t) {
            if (in_array((int)$t['id_terapis'], $occupiedTherapists)) continue;

            // Cek gender matching
            $genderMatch = false;
            if ($genderTerapis === 'Bebas') {
                $genderMatch = true;
            } elseif ($genderTerapis === 'Perempuan' && $t['jenis_kelamin'] === 'Perempuan') {
                $genderMatch = true;
            } elseif (($genderTerapis === 'Laki-Laki' || $genderTerapis === 'Pria') && ($t['jenis_kelamin'] === 'Pria' || $t['jenis_kelamin'] === 'Laki-Laki')) {
                $genderMatch = true;
            }
            if (!$genderMatch) continue;

            // Cek spesialisasi
            if (checkSpecializationMatch($t['spesialisasi'], $svc['nama_layanan'], $cat)) {
                $anyAvailable = true;
                break;
            }
        }
        if (!$anyAvailable) {
            return false; // Ada satu layanan yang tidak punya terapis sedia
        }
    }

    // Pengecekan ruangan
    $rooms = [];
    $resRooms = $conn->query("SELECT * FROM ruangan WHERE status = 'aktif'");
    if ($resRooms) {
        while ($r = $resRooms->fetch_assoc()) {
            $rooms[] = $r;
        }
    }

    $occupiedRooms = [];
    $stmtR = $conn->prepare("
        SELECT DISTINCT r.id_ruangan 
        FROM reservasi r
        WHERE r.id_ruangan IS NOT NULL
          AND r.status_reservation IN ('Menunggu Pembayaran', 'Menunggu Validasi', 'Diterima', 'Dikonfirmasi')
          AND r.reservation_date < ?
          AND DATE_ADD(r.reservation_date, INTERVAL (
              SELECT SUM(l2.durasi) 
              FROM reservasi_detail rd2 
              JOIN layanan l2 ON l2.id_layanan = rd2.id_layanan 
              WHERE rd2.id_reservasi = r.id_reservasi
          ) MINUTE) > ?
    ");
    if ($stmtR) {
        $stmtR->bind_param("ss", $endDateTime, $datetime);
        $stmtR->execute();
        $resR = $stmtR->get_result();
        while ($row = $resR->fetch_assoc()) {
            $occupiedRooms[] = (int)$row['id_ruangan'];
        }
        $stmtR->close();
    }

    $availableRooms = [];
    foreach ($rooms as $r) {
        if (!in_array((int)$r['id_ruangan'], $occupiedRooms)) {
            $availableRooms[] = $r;
        }
    }
    if (empty($availableRooms)) return false;

    return true;
}

function slotBeradaDalamJamOperasional($conn, $jam, $durasiMenit) {
    $durasiMenit = (int)$durasiMenit;
    if ($durasiMenit <= 0) return false;

    $jamMulai = strtotime($jam);
    if (!$jamMulai) return false;

    $batasTutup = ambilBatasTutupSpa($conn);
    $jamTutup = strtotime($batasTutup);
    if (!$jamTutup) return false;

    return ($jamMulai + ($durasiMenit * 60)) <= $jamTutup;
}

function ambilBatasTutupSpa($conn) {
    $batas = [
        ambilPengaturan($conn, 'sesi_pagi_selesai', '11:30'),
        ambilPengaturan($conn, 'sesi_siang_selesai', '16:30'),
        ambilPengaturan($conn, 'sesi_sore_selesai', '20:00'),
    ];

    usort($batas, function($a, $b) {
        return strtotime($b) <=> strtotime($a);
    });

    return $batas[0] ?? '20:00';
}

function ambilDetailReservasi($conn, $reservasiId) {
    $stmt = $conn->prepare("SELECT rd.id_detail, rd.id_reservasi, rd.id_layanan, rd.id_terapis, rd.qty, rd.subtotal,
                                   l.nama_layanan, l.kategori, l.durasi, l.media,
                                   t.nama_terapis
                            FROM reservasi_detail rd
                            JOIN layanan l ON l.id_layanan = rd.id_layanan
                            LEFT JOIN terapis t ON rd.id_terapis = t.id_terapis
                            WHERE rd.id_reservasi = ?");
    $stmt->bind_param("i", $reservasiId);
    $stmt->execute();
    $data = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    return $data;
}

function ambilRekeningById($conn, $rekeningId) {
    $stmt = $conn->prepare("SELECT id_rekening, nama_bank, nomor_rekening, atas_nama FROM rekening WHERE id_rekening = ? LIMIT 1");
    $stmt->bind_param("i", $rekeningId);
    $stmt->execute();
    $data = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    return $data;
}

function simpanBuktiPembayaran($conn, $reservasiId, $namaFile, $metodePembayaran = 'Transfer', $jenisPembayaran = 'DP 50%', $nominalPembayaran = 0) {
    $status = 'Menunggu Validasi';
    $stmt   = $conn->prepare("INSERT INTO payment (id_reservasi, payment_method, payment_proof, payment_date, status_payment, jenis_pembayaran, nominal_payment) VALUES (?, ?, ?, NOW(), ?, ?, ?)");
    $stmt->bind_param("issssi", $reservasiId, $metodePembayaran, $namaFile, $status, $jenisPembayaran, $nominalPembayaran);
    $berhasil = $stmt->execute();
    $stmt->close();

    if ($berhasil) {
        $stmt2 = $conn->prepare("UPDATE reservasi SET status_reservation = ? WHERE id_reservasi = ?");
        $stmt2->bind_param("si", $status, $reservasiId);
        $stmt2->execute();
        $stmt2->close();
    }

    return $berhasil;
}

function updateProfilPelanggan($conn, $userId, $nama, $telepon) {
    $stmt = $conn->prepare("UPDATE users SET nama = ?, no_telepon = ? WHERE id_user = ?");
    $stmt->bind_param("ssi", $nama, $telepon, $userId);
    $berhasil = $stmt->execute();
    $stmt->close();
    return $berhasil;
}

function updatePasswordPelanggan($conn, $userId, $passwordBaru) {
    $hash = password_hash($passwordBaru, PASSWORD_DEFAULT);
    $stmt = $conn->prepare("UPDATE users SET password = ? WHERE id_user = ?");
    $stmt->bind_param("si", $hash, $userId);
    $berhasil = $stmt->execute();
    $stmt->close();
    return $berhasil;
}

function ulasanSudahAda($conn, $reservasiId) {
    $stmtRes = $conn->prepare("SELECT r.id_user, rd.id_layanan FROM reservasi r JOIN reservasi_detail rd ON rd.id_reservasi = r.id_reservasi WHERE r.id_reservasi = ? LIMIT 1");
    $stmtRes->bind_param("i", $reservasiId);
    $stmtRes->execute();
    $res = $stmtRes->get_result()->fetch_assoc();
    $stmtRes->close();

    if (!$res) return true;

    $stmt = $conn->prepare("SELECT id_ulasan FROM ulasan WHERE reservasi_id = ? OR (reservasi_id IS NULL AND user_id = ? AND id_layanan = ?) LIMIT 1");
    $stmt->bind_param("iii", $reservasiId, $res['id_user'], $res['id_layanan']);
    $stmt->execute();
    $ada = $stmt->get_result()->num_rows > 0;
    $stmt->close();
    return $ada;
}

function simpanUlasan($conn, $userId, $layananId, $reservasiId, $rating, $isiUlasan) {
    $stmt = $conn->prepare("INSERT INTO ulasan (user_id, reservasi_id, id_layanan, rating, ulasan) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("iiiis", $userId, $reservasiId, $layananId, $rating, $isiUlasan);
    $berhasil = $stmt->execute();
    $stmt->close();
    return $berhasil;
}

function ambilUlasanLayanan($conn, $layananId) {
    $stmt = $conn->prepare("SELECT u.id_ulasan AS id, u.user_id, u.id_layanan, u.rating, u.ulasan AS isi_ulasan, u.balasan_admin, u.created_at, us.nama
        FROM ulasan u
        JOIN users us ON us.id_user = u.user_id
        WHERE u.id_layanan = ?
        ORDER BY u.created_at DESC");
    $stmt->bind_param("i", $layananId);
    $stmt->execute();
    $data = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    return $data;
}

function ambilUlasanPelanggan($conn, $userId) {
    $stmt = $conn->prepare("SELECT u.id_ulasan AS id, u.id_layanan, u.rating, u.ulasan AS isi_ulasan, u.balasan_admin, u.created_at,
            COALESCE(
                (
                    SELECT GROUP_CONCAT(DISTINCT l2.nama_layanan ORDER BY rd2.id_detail ASC SEPARATOR ', ')
                    FROM reservasi_detail rd2
                    JOIN layanan l2 ON l2.id_layanan = rd2.id_layanan
                    WHERE rd2.id_reservasi = u.reservasi_id
                ),
                (
                    SELECT GROUP_CONCAT(DISTINCT l3.nama_layanan ORDER BY rd3.id_detail ASC SEPARATOR ', ')
                    FROM reservasi_detail rd3
                    JOIN layanan l3 ON l3.id_layanan = rd3.id_layanan
                    WHERE rd3.id_reservasi = (
                        SELECT r3.id_reservasi
                        FROM reservasi r3
                        JOIN reservasi_detail rd_match ON rd_match.id_reservasi = r3.id_reservasi
                        WHERE r3.id_user = u.user_id
                          AND r3.status_reservation = 'Selesai'
                          AND rd_match.id_layanan = u.id_layanan
                        ORDER BY r3.reservation_date DESC
                        LIMIT 1
                      )
                ),
                l.nama_layanan
            ) AS nama_layanan
        FROM ulasan u
        JOIN layanan l ON l.id_layanan = u.id_layanan
        WHERE u.user_id = ?
        ORDER BY u.created_at DESC");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $data = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    return $data;
}

function ambilSemuaUlasan($conn, $limit = 6) {
    $limit = max(1, (int)$limit);
    $stmt = $conn->prepare("SELECT u.id_ulasan AS id, u.rating, u.ulasan AS isi_ulasan, u.created_at, us.nama AS nama_user, l.nama_layanan
        FROM ulasan u
        JOIN users us ON us.id_user = u.user_id
        JOIN layanan l ON l.id_layanan = u.id_layanan
        ORDER BY u.created_at DESC, u.id_ulasan DESC
        LIMIT ?");
    $stmt->bind_param("i", $limit);
    $stmt->execute();
    $data = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    return $data;
}

function updateMediaLayanan($conn, $idLayanan, $namaFile) {
    $stmt = $conn->prepare("UPDATE layanan SET media = ? WHERE id_layanan = ?");
    $stmt->bind_param("si", $namaFile, $idLayanan);
    $berhasil = $stmt->execute();
    $stmt->close();
    return $berhasil;
}

function seedUlasan($conn) {
    $sudahAda = $conn->query("SELECT COUNT(*) AS total FROM ulasan")->fetch_assoc()['total'] ?? 0;
    if ((int)$sudahAda > 0) return;

    $usersSeed = [
        ['nama' => 'Rian Hidayat', 'email' => 'rian.hidayat@gmail.com', 'password' => 'pelanggan123', 'telp' => '081234567890'],
        ['nama' => 'Siti Aminah', 'email' => 'siti.aminah@gmail.com', 'password' => 'pelanggan123', 'telp' => '081234567891'],
        ['nama' => 'Agus Setiawan', 'email' => 'agus.setiawan@gmail.com', 'password' => 'pelanggan123', 'telp' => '081234567892'],
        ['nama' => 'Dian Prasetyo', 'email' => 'dian.prasetyo@gmail.com', 'password' => 'pelanggan123', 'telp' => '081234567893'],
        ['nama' => 'Dewi Lestari', 'email' => 'dewi.lestari@gmail.com', 'password' => 'pelanggan123', 'telp' => '081234567894']
    ];

    $userIds = [];
    foreach ($usersSeed as $us) {
        $stmt = $conn->prepare("SELECT id_user FROM users WHERE email = ?");
        $stmt->bind_param("s", $us['email']);
        $stmt->execute();
        $res = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if ($res) {
            $userIds[$us['nama']] = $res['id_user'];
        } else {
            $passHash = password_hash($us['password'], PASSWORD_DEFAULT);
            $stmtInsert = $conn->prepare("INSERT INTO users (nama, email, password, no_telepon, role) VALUES (?, ?, ?, ?, 'pelanggan')");
            $stmtInsert->bind_param("ssss", $us['nama'], $us['email'], $passHash, $us['telp']);
            $stmtInsert->execute();
            $userIds[$us['nama']] = $stmtInsert->insert_id;
            $stmtInsert->close();
        }
    }

    $layananMap = [];
    $resLayanan = $conn->query("SELECT id_layanan, nama_layanan FROM layanan");
    while ($row = $resLayanan->fetch_assoc()) {
        $layananMap[$row['nama_layanan']] = $row['id_layanan'];
    }

    $reviewsToSeed = [
        [
            'nama_user' => 'Rian Hidayat',
            'nama_layanan' => 'Refleksi dan Pijat Sehat',
            'rating' => 5,
            'ulasan' => 'Pijatan Hot Stone-nya pas banget di badan. Pegel-pegel abis kerja seharian langsung ilang dalam sekali sesi.'
        ],
        [
            'nama_user' => 'Siti Aminah',
            'nama_layanan' => 'Pijat Sehat dan Bekam 9 Titik',
            'rating' => 5,
            'ulasan' => 'Gampang banget booking lewat webnya, ga ribet. Terapisnya ramah, sopan, kamarnya juga wangi and bersih banget.'
        ],
        [
            'nama_user' => 'Agus Setiawan',
            'nama_layanan' => 'Pijat Sehat dan Totok Wajah',
            'rating' => 4,
            'ulasan' => 'Teknik deep tissue terapisnya mantap banget sih. Pas pulang kerasa enteng and seger banget badannya.'
        ],
        [
            'nama_user' => 'Dian Prasetyo',
            'nama_layanan' => 'Pijat Sehat',
            'rating' => 5,
            'ulasan' => 'Pijatannya teratur and ga bikin sakit. Ditambah wangi aromaterapi lavendernya bikin tenang, nyaris ketiduran tadi.'
        ],
        [
            'nama_user' => 'Dewi Lestari',
            'nama_layanan' => 'Pijat Sehat',
            'rating' => 5,
            'ulasan' => 'Kualitas layanannya oke punya buat spa di Bandung. Suasananya tenang and privasinya bener-bener terjaga.'
        ]
    ];

    foreach ($reviewsToSeed as $rev) {
        $userId = $userIds[$rev['nama_user']] ?? null;
        $layananId = null;
        if (isset($layananMap[$rev['nama_layanan']])) {
            $layananId = $layananMap[$rev['nama_layanan']];
        } else {
            foreach ($layananMap as $namaL => $idL) {
                if (stripos($namaL, $rev['nama_layanan']) !== false || stripos($rev['nama_layanan'], $namaL) !== false) {
                    $layananId = $idL;
                    break;
                }
            }
        }

        if (!$layananId && !empty($layananMap)) {
            $layananId = reset($layananMap);
        }

        if ($userId && $layananId) {
            $stmt = $conn->prepare("INSERT INTO ulasan (user_id, id_layanan, rating, ulasan) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("iiis", $userId, $layananId, $rev['rating'], $rev['ulasan']);
            $stmt->execute();
            $stmt->close();
        }
    }
}

function hanguskanReservasiKadaluwarsa($conn) {
    $conn->query("UPDATE reservasi r
                  LEFT JOIN payment p ON p.id_reservasi = r.id_reservasi
                  SET r.status_reservation = 'Hangus'
                  WHERE r.status_reservation = 'Menunggu Pembayaran'
                  AND (
                      p.id_payment IS NULL
                      OR (
                          p.status_payment IN ('Menunggu Pembayaran', 'pending', 'Belum Upload')
                          AND (p.payment_proof IS NULL OR p.payment_proof = '')
                      )
                  )
                  AND (
                      r.created_at <= DATE_SUB(NOW(), INTERVAL " . MENIT_KADALUWARSA_RESERVASI . " MINUTE)
                      OR r.reservation_date < NOW()
                  )");
}

function seedRekening($conn) {
    $sudahAda = $conn->query("SELECT COUNT(*) AS total FROM rekening")->fetch_assoc()['total'] ?? 0;
    if ((int)$sudahAda > 0) return;

    $conn->query("INSERT INTO rekening (nama_bank, nomor_rekening, atas_nama) VALUES 
        ('BCA', '1234567890', 'A.N. SPADMIN SPA'),
        ('MANDIRI', '9876543210', 'A.N. SPADMIN SPA')");
}
?>
