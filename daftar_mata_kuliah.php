<?php
require 'koneksi.php';

$keyword = isset($_GET['keyword']) ? trim($_GET['keyword']) : '';

if ($keyword !== '') {
    $sql = "SELECT * FROM mata_kuliah WHERE kode LIKE ? OR nama LIKE ? ORDER BY nama";
    $stmt = $conn->prepare($sql);
    $like = "%$keyword%";
    $stmt->bind_param("ss", $like, $like);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $sql = "SELECT * FROM mata_kuliah ORDER BY nama";
    $result = $conn->query($sql);
}

$total_data = $result ? $result->num_rows : 0;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Mata Kuliah</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 30px; background-color: #ffffff; color: #333; }
        .top-bar { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
        .btn-primary { background-color: #1a436e; color: white; padding: 10px 16px; text-decoration: none; border-radius: 4px; font-weight: bold; font-size: 14px; }
        .btn-cari { background-color: #1a436e; color: white; border: none; padding: 8px 16px; border-radius: 4px; cursor: pointer; font-weight: bold; }
        .btn-reset { color: #0066cc; text-decoration: none; margin-left: 10px; font-size: 14px; }
        .search-container { margin-bottom: 20px; display: flex; align-items: center; gap: 10px; }
        .search-input { width: 350px; padding: 8px 12px; border: 1px solid #ccc; border-radius: 4px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th { background-color: #f5f5f5; text-align: left; padding: 12px; border-bottom: 2px solid #ddd; font-size: 14px; }
        td { padding: 12px; border-bottom: 1px solid #eee; font-size: 14px; vertical-align: middle; }
        .btn-action { text-decoration: none; padding: 3px 8px; border-radius: 3px; font-size: 13px; color: #0066cc; }
        .btn-hapus { color: #9e2a2b; }
        .back-link { display: inline-block; margin-top: 25px; color: #0066cc; text-decoration: none; font-weight: bold; }
        .no-data { text-align: center; color: #666; padding: 20px; }
    </style>
</head>
<body>

    <div class="top-bar">
        <h1 style="margin:0;">Mata Kuliah</h1>
        <a href="tambah_mata_kuliah.php" class="btn-primary">+ Tambah Mata Kuliah</a>
    </div>

    <p style="color: #555; margin-top: 5px;">Menampilkan <?= $total_data ?> data.</p>

    <form method="GET" action="" class="search-container">
        <input type="text" name="keyword" class="search-input" value="<?= htmlspecialchars($keyword) ?>" placeholder="Cari kode atau nama...">
        <button type="submit" class="btn-cari">Cari</button>
        <?php if ($keyword !== ''): ?>
            <a href="daftar_mata_kuliah.php" class="btn-reset">Reset</a>
        <?php endif; ?>
    </form>

    <table>
        <thead>
            <tr>
                <th style="width: 20%;">Kode</th>
                <th style="width: 40%;">Nama Mata Kuliah</th>
                <th style="width: 25%;">Dosen / Semester</th>
                <th style="width: 15%;">Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($total_data > 0): ?>
                <?php while($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><strong><?= htmlspecialchars($row['kode']) ?></strong></td>
                    <td><?= htmlspecialchars($row['nama']) ?></td>
                    <td><?= htmlspecialchars($row['dosen']) ?> / Semester <?= (int)$row['semester'] ?></td>
                    <td>
                        <a href="edit_mata_kuliah.php?id=<?= $row['id'] ?>" class="btn-action">Edit</a> | 
                        <a href="hapus_mata_kuliah.php?id=<?= $row['id'] ?>" class="btn-action btn-hapus">Hapus</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="4" class="no-data">
                        Data tidak ditemukan. <?php if ($keyword !== ''): ?><a href="daftar_mata_kuliah.php">Reset pencarian</a><?php endif; ?>
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <a href="index.php" class="back-link">← Beranda</a>

</body>
</html>