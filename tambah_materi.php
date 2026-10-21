<?php
require 'koneksi.php';
$error = '';
$mk_id = $judul = $isi = $tautan = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $mk_id = (int)$_POST['mata_kuliah_id'];
    $judul = trim($_POST['judul']);
    $isi = trim($_POST['isi']);
    $tautan = trim($_POST['tautan']);

    if ($mk_id <= 0) {
        $error = "Pilih mata kuliah yang valid.";
    } elseif (strlen($judul) < 3 || strlen($judul) > 150) {
        $error = "Judul materi 3-150 karakter.";
    } elseif (strlen($isi) < 3 || strlen($isi) > 2000) {
        $error = "Isi materi 3-2000 karakter.";
    } elseif (strlen($tautan) > 255 || (!str_starts_with($tautan, 'http://') && !str_starts_with($tautan, 'https://'))) {
        $error = "ERROR J01: Tautan materi tidak valid (harus diawali http:// atau https://).";
    } else {
        $stmt = $conn->prepare("INSERT INTO materi (mata_kuliah_id, judul, isi, tautan) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("isss", $mk_id, $judul, $isi, $tautan);
        if ($stmt->execute()) {
            $_SESSION['pesan'] = "Materi berhasil ditambahkan.";
            header("Location: daftar_materi.php");
            exit;
        } else {
            $error = "Gagal menyimpan materi.";
        }
    }
}
$mk_list = $conn->query("SELECT id, kode, nama FROM mata_kuliah ORDER BY nama");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tambah Materi</title>
    <style>body { font-family: Arial, sans-serif; margin: 40px; } .error { color: red; }</style>
</head>
<body>
    <h1>Tambah Materi</h1>
    <?php if ($error): ?><p class="error"><?= $error ?></p><?php endif; ?>
    <form method="POST" action="">
        <label>Mata Kuliah:</label><br>
        <select name="mata_kuliah_id" required>
            <option value="">Pilih Mata Kuliah</option>
            <?php while($m = $mk_list->fetch_assoc()): ?>
                <option value="<?= $m['id'] ?>" <?= $mk_id == $m['id'] ? 'selected' : '' ?>><?= htmlspecialchars($m['kode'].' - '.$m['nama']) ?></option>
            <?php endwhile; ?>
        </select><br><br>
        <label>Judul Materi:</label><br>
        <input type="text" name="judul" value="<?= htmlspecialchars($judul) ?>" required><br><br>
        <label>Isi Materi:</label><br>
        <textarea name="isi" rows="5" required><?= htmlspecialchars($isi) ?></textarea><br><br>
        <label>Tautan Referensi:</label><br>
        <input type="text" name="tautan" value="<?= htmlspecialchars($tautan) ?>" placeholder="https://..." required><br><br>
        <button type="submit">Simpan data</button>
        <a href="daftar_materi.php">Batal</a>
    </form>
</body>
</html>