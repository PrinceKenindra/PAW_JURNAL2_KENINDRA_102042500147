<?php
require 'koneksi.php';

$keyword = isset($_GET['keyword']) ? trim($_GET['keyword']) : '';
$mk_filter = isset($_GET['mk_id']) ? (int)$_GET['mk_id'] : 0;

$sql = "SELECT materi.*, mata_kuliah.nama as nama_mk FROM materi 
        JOIN mata_kuliah ON materi.mata_kuliah_id = mata_kuliah.id 
        WHERE materi.judul LIKE ?";
$params = ["%$keyword%"];
$types = "s";

if ($mk_filter > 0) {
    $sql .= " AND materi.mata_kuliah_id = ?";
    $params[] = $mk_filter;
    $types .= "i";
}
$sql .= " ORDER BY materi.id DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();

$mk_list = $conn->query("SELECT id, kode, nama FROM mata_kuliah ORDER BY nama");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Daftar Materi</title>
    <style>body { font-family: Arial, sans-serif; margin: 40px; } table { width: 100%; border-collapse: collapse; margin-top: 20px; } th, td { border: 1px solid #ddd; padding: 10px; text-align: left; } th { background: #f2f2f2; }</style>
</head>
<body>
    <h1>Materi</h1>
    <p><a href="index.php">← Beranda</a> | <a href="tambah_materi.php">+ Tambah Materi</a></p>

    <?php if (isset($_SESSION['pesan'])): ?>
        <p style="color: green;"><?= $_SESSION['pesan']; unset($_SESSION['pesan']); ?></p>
    <?php endif; ?>

    <form method="GET" action="">
        <select name="mk_id">
            <option value="0">Semua Mata Kuliah</option>
            <?php while($m = $mk_list->fetch_assoc()): ?>
                <option value="<?= $m['id'] ?>" <?= $mk_filter == $m['id'] ? 'selected' : '' ?>><?= htmlspecialchars($m['kode'].' - '.$m['nama']) ?></option>
            <?php endwhile; ?>
        </select>
        <input type="text" name="keyword" value="<?= htmlspecialchars($keyword) ?>" placeholder="Cari judul...">
        <button type="submit">Cari</button>
        <a href="daftar_materi.php">Reset</a>
    </form>

    <p>Menampilkan <?= $result->num_rows ?> data.</p>
    <table>
        <tr>
            <th>Judul Materi</th>
            <th>Mata Kuliah</th>
            <th>Aksi</th>
        </tr>
        <?php if ($result->num_rows > 0): ?>
            <?php while($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($row['judul']) ?></td>
                <td><?= htmlspecialchars($row['nama_mk']) ?></td>
                <td>
                    <a href="detail_materi.php?id=<?= $row['id'] ?>">Detail</a> | 
                    <a href="edit_materi.php?id=<?= $row['id'] ?>">Edit</a> | 
                    <a href="hapus_materi.php?id=<?= $row['id'] ?>">Hapus</a>
                </td>
            </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr><td colspan="3">Data tidak ditemukan.</td></tr>
        <?php endif; ?>
    </table>
</body>
</html>