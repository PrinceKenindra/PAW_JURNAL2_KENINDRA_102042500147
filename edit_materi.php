=<?php
require 'koneksi.php';
$error = '';
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$stmt = $conn->prepare("SELECT * FROM materi WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$data = $stmt->get_result()->fetch_assoc();

if (!$data) {
    $_SESSION['error'] = "Materi tidak ditemukan.";
    header("Location: daftar_materi.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $mk_id = (int)$_POST['mata_kuliah_id'];
    $judul = trim($_POST['judul']);
    $isi = trim($_POST['isi']);
    $tautan = trim($_POST['tautan']);

    if ($mk_id <= 0) {
        $error = "Pilih mata kuliah yang valid.";
    } elseif (strlen($judul) < 3 || strlen($judul) > 150) {
        $error = "Judul 3-150 karakter.";
    } elseif (strlen($isi) < 3 || strlen($isi) > 2000) {
        $error = "Isi 3-2000 karakter.";
    } elseif (strlen($tautan) > 255 || (!str_starts_with($tautan, 'http://') && !str_starts_with($tautan, 'https://'))) {
        $error = "Tautan tidak valid.";
    } else {
        if ($mk_id == $data['mata_kuliah_id'] && $judul === $data['judul'] && $isi === $data['isi'] && $tautan === $data['tautan']) {
            $error = "Tidak ada perubahan data yang disimpan.";
        } else {
            $stmt_up = $conn->prepare("UPDATE materi SET mata_kuliah_id=?, judul=?, isi=?, tautan=? WHERE id=?");
            $stmt_up->bind_param("isssi", $mk_id, $judul, $isi, $tautan, $id);
            if ($stmt_up->execute()) {
                $_SESSION['pesan'] = "Materi berhasil diperbarui.";
                header("Location: daftar_materi.php");
                exit;
            } else {
                $error = "Gagal memperbarui data.";
            }
        }
    }
} else {
    $mk_id = $data['mata_kuliah_id'];
    $judul = $data['judul'];
    $isi = $data['isi'];
    $tautan = $data['tautan'];
}
$mk_list = $conn->query("SELECT id, kode, nama FROM mata_kuliah ORDER BY nama");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Materi</title>
    <style>body { font-family: Arial, sans-serif; margin: 40px; } .error { color: red; }</style>
</head>
<body>
    <h1>Edit Materi</h1>
    <?php if ($error): ?><p class="error"><?= $error ?></p><?php endif; ?>
    <form method="POST" action="">
        <label>Mata Kuliah:</label><br>
        <select name="mata_kuliah_id" required>
            <?php while($m = $mk_list->fetch_assoc()): ?>
                <option value="<?= $m['id'] ?>" <?= $mk_id == $m['id'] ? 'selected' : '' ?>><?= htmlspecialchars($m['kode'].' - '.$m['nama']) ?></option>
            <?php endwhile; ?>
        </select><br><br>
        <label>Judul Materi:</label><br>
        <input type="text" name="judul" value="<?= htmlspecialchars($judul) ?>" required><br><br>
        <label>Isi Materi:</label><br>
        <textarea name="isi" rows="5" required><?= htmlspecialchars($isi) ?></textarea><br><br>
        <label>Tautan Referensi:</label><br>
        <input type="text" name="tautan" value="<?= htmlspecialchars($tautan) ?>" required><br><br>
        <button type="submit">Simpan perubahan</button>
        <a href="daftar_materi.php">Batal</a>
    </form>
</body>
</html>