<?php
require 'koneksi.php';

// Menghitung jumlah data menggunakan COUNT dari database
$mk_count = $conn->query("SELECT COUNT(*) as total FROM mata_kuliah")->fetch_assoc()['total'];
$mat_count = $conn->query("SELECT COUNT(*) as total FROM materi")->fetch_assoc()['total'];
$tug_count = $conn->query("SELECT COUNT(*) as total FROM tugas")->fetch_assoc()['total'];

// Mengambil daftar mata kuliah untuk ringkasan beranda
$result_mk = $conn->query("SELECT kode, nama FROM mata_kuliah ORDER BY nama");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Beranda - LMS Sederhana</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; background: #f9f9f9; color: #333; }
        .card { background: #fff; padding: 20px; margin-bottom: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        nav a { margin-right: 15px; text-decoration: none; color: #0066cc; font-weight: bold; }
        ul { padding-left: 20px; }
    </style>
</head>
<body>
    <h1>Learning Management System (LMS)</h1>
    <nav>
        <a href="index.php">Beranda</a>
        <a href="daftar_mata_kuliah.php">Mata Kuliah (<?= $mk_count ?>)</a>
        <a href="daftar_materi.php">Materi (<?= $mat_count ?>)</a>
        <a href="daftar_tugas.php">Tugas (<?= $tug_count ?>)</a>
    </nav>
    <hr>

    <div class="card">
        <h2>Ringkasan LMS</h2>
        <p>Kelola mata kuliah, materi pembelajaran, dan tugas dengan mudah.</p>
        <ul>
            <li>Total Mata Kuliah: <strong><?= $mk_count ?></strong></li>
            <li>Total Materi: <strong><?= $mat_count ?></strong></li>
            <li>Total Tugas: <strong><?= $tug_count ?></strong></li>
        </ul>
    </div>

    <div class="card">
        <h3>Mata Kuliah Tersedia</h3>
        <?php if ($result_mk->num_rows > 0): ?>
            <ul>
                <?php while($row = $result_mk->fetch_assoc()): ?>
                    <li><?= htmlspecialchars($row['kode'] . ' - ' . $row['nama']) ?></li>
                <?php endwhile; ?>
            </ul>
        <?php else: ?>
            <p>Belum ada mata kuliah.</p>
        <?php endif; ?>
    </div>
</body>
</html>