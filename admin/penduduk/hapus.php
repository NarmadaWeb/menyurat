<?php
require_once __DIR__ . '/../../includes/auth.php';
require_login();

$id = (int)($_GET['id'] ?? 0);
if ($id > 0) {
    try {
        $stmt = $pdo->prepare("SELECT nama_lengkap FROM penduduk WHERE id = ?");
        $stmt->execute([$id]);
        $name = $stmt->fetchColumn();

        $del = $pdo->prepare("DELETE FROM penduduk WHERE id = ?");
        $del->execute([$id]);

        redirect(base_url('admin/penduduk/index.php'), "Data penduduk $name berhasil dihapus.");
    } catch (PDOException $e) {
        redirect(base_url('admin/penduduk/index.php'), "Gagal menghapus data penduduk: " . $e->getMessage(), 'error');
    }
} else {
    redirect(base_url('admin/penduduk/index.php'));
}
