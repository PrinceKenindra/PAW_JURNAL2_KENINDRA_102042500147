<?php
require 'koneksi.php';
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$stmt = $conn->prepare("SELECT materi.*, mata_kuliah.nama as nama_mk, mata_kuliah.kode FROM materi JOIN mata_kuliah ON materi.mata_kuliah_id = mata_kuliah.id WHERE materi.id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$data = $stmt->get_result()->fetch_assoc();

if (!$data) {
    die("Data materi tidak ditemukan.");
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Detail Materi</title>
    <style>body { font-family: Arial, sans-serif; margin: 40px; }</style>
</head>
<body>
    <h1>Materi</h1>
    <h2><?= htmlspecialchars($data['judul']) ?></h2>
    <p><strong>Mata kuliah:</strong> <?= htmlspecialchars($data['kode'] . ' - ' . $data['nama_mk']) ?></p>
    <p><strong>Isi materi:</strong><br><?= nl2br(htmlspecialchars($data['isi'])) ?></p>
    <p><strong>Tautan referensi:</strong><br><a href="<?= htmlspecialchars($data['tautan']) ?>" target="_blank"><?= htmlspecialchars($data['tautan']) ?></a></p>
    <p>
        <a href="edit_materi.php?id=<?= $data['id'] ?>">Edit data</a> | 
        <a href="daftar_materi.php">Kembali</a>
    </p>
</body>
</html>