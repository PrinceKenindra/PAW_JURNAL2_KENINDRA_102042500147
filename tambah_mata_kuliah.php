<?php
require 'koneksi.php';
$error = '';
$kode = $nama = $dosen = $semester = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $kode = trim($_POST['kode']);
    $nama = trim($_POST['nama']);
    $dosen = trim($_POST['dosen']);
    $semester = (int)$_POST['semester'];

    // Validasi isian
    if (!preg_match('/^[A-Z0-9\-]{3,20}$/', $kode)) {
        $error = "Kode harus 3-20 karakter (huruf kapital, angka, tanda -).";
    } elseif (strlen($nama) < 3 || strlen($nama) > 100) {
        $error = "Nama mata kuliah harus 3-100 karakter.";
    } elseif (strlen($dosen) < 3 || strlen($dosen) > 100) {
        $error = "Nama dosen harus 3-100 karakter.";
    } elseif ($semester < 1 || $semester > 14) {
        $error = "Semester harus berada di antara 1 dan 14.";
    } else {
        // Cek keunikan kode
        $stmt = $conn->prepare("SELECT id FROM mata_kuliah WHERE kode = ?");
        $stmt->bind_param("s", $kode);
        $stmt->execute();
        if ($stmt->get_result()->num_rows > 0) {
            $error = "ERROR J02: Kode mata kuliah sudah digunakan.";
        } else {
            $stmt = $conn->prepare("INSERT INTO mata_kuliah (kode, nama, dosen, semester) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("sssi", $kode, $nama, $dosen, $semester);
            if ($stmt->execute()) {
                $_SESSION['pesan'] = "Mata kuliah berhasil ditambahkan.";
                header("Location: daftar_mata_kuliah.php");
                exit;
            } else {
                $error = "Gagal menyimpan data ke database.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tambah Mata Kuliah</title>
    <style>body { font-family: Arial, sans-serif; margin: 40px; } .error { color: red; }</style>
</head>
<body>
    <h1>Tambah Mata Kuliah</h1>
    <?php if ($error): ?><p class="error"><?= $error ?></p><?php endif; ?>
    <form method="POST" action="">
        <label>Kode Mata Kuliah:</label><br>
        <input type="text" name="kode" value="<?= htmlspecialchars($kode) ?>" required><br><br>
        <label>Nama Mata Kuliah:</label><br>
        <input type="text" name="nama" value="<?= htmlspecialchars($nama) ?>" required><br><br>
        <label>Dosen Pengampu:</label><br>
        <input type="text" name="dosen" value="<?= htmlspecialchars($dosen) ?>" required><br><br>
        <label>Semester (1-14):</label><br>
        <input type="number" name="semester" min="1" max="14" value="<?= htmlspecialchars($semester) ?>" required><br><br>
        <button type="submit">Simpan data</button>
        <a href="daftar_mata_kuliah.php">Batal</a>
    </form>
</body>
</html>