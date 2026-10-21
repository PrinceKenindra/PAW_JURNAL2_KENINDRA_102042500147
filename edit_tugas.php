<?php
require 'koneksi.php';
$error = '';
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$stmt = $conn->prepare("SELECT * FROM tugas WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$data = $stmt->get_result()->fetch_assoc();

if (!$data) {
    $_SESSION['error'] = "Tugas tidak ditemukan.";
    header("Location: daftar_tugas.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $mk_id = (int)$_POST['mata_kuliah_id'];
    $judul = trim($_POST['judul']);
    $instruksi = trim($_POST['instruksi']);
    $tenggat = trim($_POST['tenggat']);
    $status = trim($_POST['status']);

    $d = DateTime::createFromFormat('Y-m-d', $tenggat);
    $valid_date = $d && $d->format('Y-m-d') === $tenggat;

    if ($mk_id <= 0) {
        $error = "Pilih mata kuliah.";
    } elseif (strlen($judul) < 3 || strlen($judul) > 150) {
        $error = "Judul 3-150 karakter.";
    } elseif (strlen($instruksi) < 3 || strlen($instruksi) > 2000) {
        $error = "Instruksi 3-2000 karakter.";
    } elseif (!$valid_date) {
        $error = "Tanggal tidak valid.";
    } elseif (!in_array($status, ['Draf', 'Aktif', 'Selesai'])) {
        $error = "Status tidak valid.";
    } else {
        if ($mk_id == $data['mata_kuliah_id'] && $judul === $data['judul'] && $instruksi === $data['instruksi'] && $tenggat === $data['tenggat'] && $status === $data['status']) {
            $error = "Tidak ada perubahan data yang disimpan.";
        } else {
            $stmt_up = $conn->prepare("UPDATE tugas SET mata_kuliah_id=?, judul=?, instruksi=?, tenggat=?, status=? WHERE id=?");
            $stmt_up->bind_param("issssi", $mk_id, $judul, $instruksi, $tenggat, $status, $id);
            if ($stmt_up->execute()) {
                $_SESSION['pesan'] = "Tugas berhasil diperbarui.";
                header("Location: daftar_tugas.php");
                exit;
            } else {
                $error = "Gagal memperbarui data.";
            }
        }
    }
} else {
    $mk_id = $data['mata_kuliah_id'];
    $judul = $data['judul'];
    $instruksi = $data['instruksi'];
    $tenggat = $data['tenggat'];
    $status = $data['status'];
}
$mk_list = $conn->query("SELECT id, kode, nama FROM mata_kuliah ORDER BY nama");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Tugas</title>
    <style>body { font-family: Arial, sans-serif; margin: 40px; } .error { color: red; }</style>
</head>
<body>
    <h1>Edit Tugas</h1>
    <?php if ($error): ?><p class="error"><?= $error ?></p><?php endif; ?>
    <form method="POST" action="">
        <label>Mata Kuliah:</label><br>
        <select name="mata_kuliah_id" required>
            <?php while($m = $mk_list->fetch_assoc()): ?>
                <option value="<?= $m['id'] ?>" <?= $mk_id == $m['id'] ? 'selected' : '' ?>><?= htmlspecialchars($m['kode'].' - '.$m['nama']) ?></option>
            <?php endwhile; ?>
        </select><br><br>
        <label>Judul Tugas:</label><br>
        <input type="text" name="judul" value="<?= htmlspecialchars($judul) ?>" required><br><br>
        <label>Instruksi Tugas:</label><br>
        <textarea name="instruksi" rows="5" required><?= htmlspecialchars($instruksi) ?></textarea><br><br>
        <label>Tenggat:</label><br>
        <input type="date" name="tenggat" value="<?= htmlspecialchars($tenggat) ?>" required><br><br>
        <label>Status:</label><br>
        <input type="radio" name="status" value="Draf" <?= $status == 'Draf' ? 'checked' : '' ?>> Draf
        <input type="radio" name="status" value="Aktif" <?= $status == 'Aktif' ? 'checked' : '' ?>> Aktif
        <input type="radio" name="status" value="Selesai" <?= $status == 'Selesai' ? 'checked' : '' ?>> Selesai<br><br>
        <button type="submit">Simpan perubahan</button>
        <a href="daftar_tugas.php">Batal</a>
    </form>
</body>
</html>