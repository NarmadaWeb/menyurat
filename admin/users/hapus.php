<?php
require_once __DIR__ . '/../../includes/auth.php';
require_role('Admin');

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id == $_SESSION['user_id']) {
    redirect(base_url('admin/users/index.php'), 'Anda tidak dapat menghapus akun Anda sendiri.', 'error');
}

$stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
$stmt->execute([$id]);

redirect(base_url('admin/users/index.php'), 'User berhasil dihapus.');
?>
