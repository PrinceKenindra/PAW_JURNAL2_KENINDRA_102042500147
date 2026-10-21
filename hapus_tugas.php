<?php
require 'koneksi.php';
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$stmt = $conn->prepare("SELECT judul FROM tugas WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$data = $stmt->get_result()->fetch_assoc();

if (!$data) {
    header("Location: daftar_tugas.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("Token CSRF tidak valid.");
    }
    $del = $conn->prepare("DELETE FROM tugas WHERE id = ?");
    $del->bind_param("i", $id);
    if ($del->execute()) {
        $_SESSION['pesan'] = "Tugas berhasil dihapus.";
        header("Location: daftar_tugas.php");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head><meta charset="UTF-8"><title>Hapus Tugas</title></head>
<body style="font-family: Arial; margin: 40px;">
    <h1>Hapus Tugas</h1>
    <p>Hapus tugas: <strong><?= htmlspecialchars($data['judul']) ?></strong>?</p>
    <form method="POST">
        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
        <button type="submit" style="background: darkred; color: white; padding: 8px;">Ya, hapus</button>
        <a href="daftar_tugas.php">Batal</a>
    </form>
</body>
</html>