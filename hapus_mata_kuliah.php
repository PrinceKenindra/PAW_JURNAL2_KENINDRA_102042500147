<?php
require 'koneksi.php';

// 1. Tangkap ID dari URL
$id_param = isset($_GET['id']) ? $_GET['id'] : '';

// Skenario Uji 1: Cek apakah ID BUKAN ANGKA (mengandung huruf/karakter lain)
if (!is_numeric($id_param)) {
    die("<h3 style='color:red;'>Error: ID bukan angka yang valid!</h3><p>Sistem menolak parameter ID yang tidak sesuai format.</p><a href='daftar_mata_kuliah.php'>Kembali</a>");
}

$id = (int)$id_param;

// Ambil data mata kuliah dari database
$stmt = $conn->prepare("SELECT id, nama FROM mata_kuliah WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result_data = $stmt->get_result();
$data = $result_data->fetch_assoc();
$stmt->close();

// Skenario Uji 2: Cek apakah ID TIDAK ADA di database
if (!$data) {
    die("<h3 style='color:red;'>Error: Data tidak ditemukan!</h3><p>ID mata kuliah tersebut tidak terdaftar di dalam database.</p><a href='daftar_mata_kuliah.php'>Kembali</a>");
}

$error_msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Skenario Uji 3: Cek token CSRF (jika token kosong atau tidak cocok)
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("<h3 style='color:red;'>Akses Ditolak (403 Forbidden)</h3><p>Token CSRF tidak valid atau tidak disertakan!</p><a href='daftar_mata_kuliah.php'>Kembali</a>");
    }

    // Proses pengecekan relasi dan hapus data jika token valid
    $chk_materi = $conn->prepare("SELECT id FROM materi WHERE mata_kuliah_id = ?");
    $chk_materi->bind_param("i", $id);
    $chk_materi->execute();
    $res_materi = $chk_materi->get_result();
    $chk_materi->close();

    $chk_tugas = $conn->prepare("SELECT id FROM tugas WHERE mata_kuliah_id = ?");
    $chk_tugas->bind_param("i", $id);
    $chk_tugas->execute();
    $res_tugas = $chk_tugas->get_result();
    $chk_tugas->close();

    if ($res_materi->num_rows > 0 || $res_tugas->num_rows > 0) {
        $error_msg = "Data belum tersimpan. Mata kuliah masih memiliki relasi materi atau tugas.";
    } else {
        $del = $conn->prepare("DELETE FROM mata_kuliah WHERE id = ?");
        $del->bind_param("i", $id);
        if ($del->execute()) {
            $del->close();
            $_SESSION['pesan'] = "Mata kuliah berhasil dihapus.";
            header("Location: daftar_mata_kuliah.php");
            exit;
        } else {
            $error_msg = "Gagal menghapus data.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Hapus Mata Kuliah</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; } 
        .error { background: #ffe6e6; padding: 10px; color: red; border: 1px solid red; margin-bottom: 15px; }
        .btn-danger { background: darkred; color: white; padding: 8px 15px; border: none; cursor: pointer; }
        .btn-secondary { padding: 8px 15px; text-decoration: none; background: #ccc; color: black; margin-left: 5px; }
    </style>
</head>
<body>
    <h1>Konfirmasi Hapus Mata Kuliah</h1>
    <?php if ($error_msg): ?><div class="error"><?= htmlspecialchars($error_msg) ?></div><?php endif; ?>
    <p>Apakah Anda yakin ingin menghapus mata kuliah: <strong><?= htmlspecialchars($data['nama']) ?></strong>?</p>
    
    <form method="POST" action="">
        <!-- Token CSRF -->
        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? 'invalid_token_test' ?>">
        <button type="submit" class="btn-danger">Ya, hapus data</button>
        <a href="daftar_mata_kuliah.php" class="btn-secondary">Batal</a>
    </form>
</body>
</html>