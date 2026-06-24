<?php
require_once __DIR__ . '/../../includes/auth.php';
require_login();
$id = (int)$_GET['id'];
$pdo->prepare("DELETE FROM surat_keluar WHERE id = ?")->execute([$id]);
redirect(base_url('admin/surat-keluar/index.php'), 'Berhasil hapus.');
?>
