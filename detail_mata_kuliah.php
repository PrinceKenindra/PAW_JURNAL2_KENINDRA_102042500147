<?php
require 'koneksi.php';

$id = $_GET['id'] ?? null;
if (!$id) die("ID tidak ditemukan.");

// Mengambil data mata kuliah
$stmt = $conn->prepare('SELECT * FROM mata_kuliah WHERE id = ?');
$stmt->bind_param("i", $id);
$stmt->execute();
$data = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$data) die("Data mata kuliah tidak ditemukan.");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <title>Detail Mata Kuliah</title>
</head>
<body>
    <h2>Detail Mata Kuliah</h2>
    <table border="1" cellpadding="10" cellspacing="0">
        <tr>
            <th>Kode Mata Kuliah</th>
            <td><?= htmlspecialchars($data['kode']) ?></td>
        </tr>
        <tr>
            <th>Nama Mata Kuliah</th>
            <td><?= htmlspecialchars($data['nama']) ?></td>
        </tr>
        <tr>
            <th>Dosen Pengampu</th>
            <td><?= htmlspecialchars($data['dosen']) ?></td>
        </tr>
        <tr>
            <th>Semester</th>
            <td><?= htmlspecialchars($data['semester']) ?></td>
        </tr>
    </table>
    <br>
    <a href="edit_mata_kuliah.php?id=<?= $data['id'] ?>"><button>Edit data</button></a>
    <a href="daftar_mata_kuliah.php"><button>Kembali</button></a>
</body>
</html>