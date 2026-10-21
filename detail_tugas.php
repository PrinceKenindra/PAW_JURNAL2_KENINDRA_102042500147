<?php
require 'koneksi.php';

$id = $_GET['id'] ?? null;
if (!$id) die("ID tidak ditemukan.");

$stmt = $conn->prepare('
    SELECT t.*, mk.nama AS nama_mk 
    FROM tugas t 
    JOIN mata_kuliah mk ON t.mata_kuliah_id = mk.id 
    WHERE t.id = ?
');
$stmt->bind_param("i", $id);
$stmt->execute();
$data = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$data) die("Data tugas tidak ditemukan.");
?>
<!DOCTYPE html>
<html lang="id">
<head><title>Detail Tugas</title></head>
<body>
    <h2>Detail Tugas</h2>
    <table border="1" cellpadding="10" cellspacing="0">
        <tr><th>Mata kuliah</th><td><?= htmlspecialchars($data['nama_mk']) ?></td></tr>
        <tr><th>Judul tugas</th><td><?= htmlspecialchars($data['judul']) ?></td></tr>
        <tr><th>Instruksi</th><td><?= nl2br(htmlspecialchars($data['instruksi'])) ?></td></tr>
        <tr><th>Tenggat</th><td><?= htmlspecialchars($data['tenggat']) ?></td></tr>
        <tr><th>Status</th><td><?= htmlspecialchars($data['status']) ?></td></tr>
    </table>
    <br>
    <a href="edit_tugas.php?id=<?= $data['id'] ?>"><button>Edit data</button></a>
    <a href="daftar_tugas.php"><button>Kembali</button></a>
</body>
</html>