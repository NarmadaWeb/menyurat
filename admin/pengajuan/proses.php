<?php
require_once __DIR__ . '/../../includes/auth.php';
require_login();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $pdo->prepare("UPDATE citizen_requests SET status = ? WHERE id = ?");
    $stmt->execute([$_POST['status'], $_POST['request_id']]);
    redirect(base_url('admin/pengajuan/index.php'), 'Status updated.');
}
?>
