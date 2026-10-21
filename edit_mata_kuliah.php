<?php
require 'koneksi.php';
$error = '';
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$stmt = $conn->prepare("SELECT * FROM mata_kuliah WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$data = $stmt->get_result()->fetch_assoc();

if (!$data) {
    $_SESSION['error'] = "Data mata kuliah tidak ditemukan.";
    header("Location: daftar_mata_kuliah.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $kode = trim($_POST['kode']);
    $nama = trim($_POST['nama']);
    $dosen = trim($_POST['dosen']);
    $semester = (int)$_POST['semester'];

    if (!preg_match('/^[A-Z0-9\-]{3,20}$/', $kode)) {
        $error = "Kode harus 3-20 karakter.";
    } elseif (strlen($nama) < 3 || strlen($nama) > 100) {
        $error = "Nama mata kuliah harus 3-100 karakter.";
    } elseif (strlen($dosen) < 3 || strlen($dosen) > 100) {
        $error = "Nama dosen harus 3-100 karakter.";
    } elseif ($semester < 1 || $semester > 14) {
        $error = "Semester harus antara 1-14.";
    } else {
        // Cek duplikasi kode jika kode diubah
        $stmt_check = $conn->prepare("SELECT id FROM mata_kuliah WHERE kode = ? AND id != ?");
        $stmt_check->bind_param("si", $kode, $id);
        $stmt_check->execute();
        if ($stmt_check->get_result()->num_rows > 0) {
            $error = "Kode mata kuliah sudah digunakan oleh data lain.";
        } else {
            // Cek apakah ada perubahan data
            if ($kode === $data['kode'] && $nama === $data['nama'] && $dosen === $data['dosen'] && $semester == $data['semester']) {
                $error = "Tidak ada perubahan data yang disimpan.";
            } else {
                $stmt_up = $conn->prepare("UPDATE mata_kuliah SET kode=?, nama=?, dosen=?, semester=? WHERE id=?");
                $stmt_up->bind_param("sssii", $kode, $nama, $dosen, $semester, $id);
                if ($stmt_up->execute()) {
                    $_SESSION['pesan'] = "Mata kuliah berhasil diperbarui.";
                    header("Location: daftar_mata_kuliah.php");
                    exit;
                } else {
                    $error = "Gagal memperbarui data.";
                }
            }
        }
    }
} else {
    $kode = $data['kode'];
    $nama = $data['nama'];
    $dosen = $data['dosen'];
    $semester = $data['semester'];
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Mata Kuliah</title>
    <style>body { font-family: Arial, sans-serif; margin: 40px; } .error { color: red; }</style>
</head>
<body>
    <h1>Edit Mata Kuliah</h1>
    <?php if ($error): ?><p class="error"><?= $error ?></p><?php endif; ?>
    <form method="POST" action="">
        <label>Kode Mata Kuliah:</label><br>
        <input type="text" name="kode" value="<?= htmlspecialchars($kode) ?>" required><br><br>
        <label>Nama Mata Kuliah:</label><br>
        <input type="text" name="nama" value="<?= htmlspecialchars($nama) ?>" required><br><br>
        <label>Dosen Pengampu:</label><br>
        <input type="text" name="dosen" value="<?= htmlspecialchars($dosen) ?>" required><br><br>
        <label>Semester:</label><br>
        <input type="number" name="semester" min="1" max="14" value="<?= htmlspecialchars($semester) ?>" required><br><br>
        <button type="submit">Simpan perubahan</button>
        <a href="daftar_mata_kuliah.php">Batal</a>
    </form>
</body>
</html>