<?php
require 'koneksi.php';
$error = '';
$mk_id = $judul = $instruksi = $tenggat = $status = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $mk_id = (int)$_POST['mata_kuliah_id'];
    $judul = trim($_POST['judul']);
    $instruksi = trim($_POST['instruksi']);
    $tenggat = trim($_POST['tenggat']);
    $status = trim($_POST['status']);

    // Validasi Tanggal
    $d = DateTime::createFromFormat('Y-m-d', $tenggat);
    $valid_date = $d && $d->format('Y-m-d') === $tenggat;

    if ($mk_id <= 0) {
        $error = "Pilih mata kuliah yang valid.";
    } elseif (strlen($judul) < 3 || strlen($judul) > 150) {
        $error = "Judul tugas 3-150 karakter.";
    } elseif (strlen($instruksi) < 3 || strlen($instruksi) > 2000) {
        $error = "Instruksi 3-2000 karakter.";
    } elseif (!$valid_date) {
        $error = "Tanggal tenggat tidak valid.";
    } elseif (!in_array($status, ['Draf', 'Aktif', 'Selesai'])) {
        $error = "Status harus Draf, Aktif, atau Selesai.";
    } else {
        $stmt = $conn->prepare("INSERT INTO tugas (mata_kuliah_id, judul, instruksi, tenggat, status) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("issss", $mk_id, $judul, $instruksi, $tenggat, $status);
        if ($stmt->execute()) {
            $_SESSION['pesan'] = "Tugas berhasil ditambahkan.";
            header("Location: daftar_tugas.php");
            exit;
        } else {
            $error = "Gagal menyimpan tugas.";
        }
    }
}
$mk_list = $conn->query("SELECT id, kode, nama FROM mata_kuliah ORDER BY nama");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tambah Tugas</title>
    <style>body { font-family: Arial, sans-serif; margin: 40px; } .error { color: red; }</style>
</head>
<body>
    <h1>Tambah Tugas</h1>
    <?php if ($error): ?><p class="error"><?= $error ?></p><?php endif; ?>
    <form method="POST" action="">
        <label>Mata Kuliah:</label><br>
        <select name="mata_kuliah_id" required>
            <option value="">Pilih Mata Kuliah</option>
            <?php while($m = $mk_list->fetch_assoc()): ?>
                <option value="<?= $m['id'] ?>" <?= $mk_id == $m['id'] ? 'selected' : '' ?>><?= htmlspecialchars($m['kode'].' - '.$m['nama']) ?></option>
            <?php endwhile; ?>
        </select><br><br>
        <label>Judul Tugas:</label><br>
        <input type="text" name="judul" value="<?= htmlspecialchars($judul) ?>" required><br><br>
        <label>Instruksi Tugas:</label><br>
        <textarea name="instruksi" rows="5" required><?= htmlspecialchars($instruksi) ?></textarea><br><br>
        <label>Tenggat (YYYY-MM-DD):</label><br>
        <input type="date" name="tenggat" value="<?= htmlspecialchars($tenggat) ?>" required><br><br>
        <label>Status:</label><br>
        <input type="radio" name="status" value="Draf" <?= $status == 'Draf' ? 'checked' : '' ?>> Draf
        <input type="radio" name="status" value="Aktif" <?= $status == 'Aktif' ? 'checked' : '' ?>> Aktif
        <input type="radio" name="status" value="Selesai" <?= $status == 'Selesai' ? 'checked' : '' ?>> Selesai<br><br>
        <button type="submit">Simpan data</button>
        <a href="daftar_tugas.php">Batal</a>
    </form>
</body>
</html>