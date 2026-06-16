<?php
require_once __DIR__ . '/../../includes/auth.php';
require_login();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $mail_id = (int)$_POST['mail_id'];
    $receiver_ids = $_POST['receiver_ids'] ?? [];
    $instruction = sanitize($_POST['instruction']);
    $deadline = sanitize($_POST['deadline']) ?: null;
    $notes = sanitize($_POST['notes']);
    $status_update = sanitize($_POST['status_update']);

    if (empty($receiver_ids)) {
        redirect(base_url('admin/surat-masuk/detail.php?id=' . $mail_id), 'Pilih minimal satu penerima disposisi.', 'error');
    }

    try {
        $pdo->beginTransaction();
        $stmt = $pdo->prepare("INSERT INTO dispositions (mail_id, receiver_id, instruction, deadline, notes) VALUES (?, ?, ?, ?, ?)");
        foreach ($receiver_ids as $receiver_id) {
            $stmt->execute([$mail_id, $receiver_id, $instruction, $deadline, $notes]);
        }
        $pdo->prepare("UPDATE incoming_mails SET status = ? WHERE id = ?")->execute([$status_update, $mail_id]);
        $pdo->prepare("INSERT INTO activity_logs (user_id, action, description) VALUES (?, 'mengirim disposisi', ?)")->execute([$_SESSION['user_id'], "Surat ID: $mail_id"]);
        $pdo->commit();
        redirect(base_url('admin/surat-masuk/detail.php?id=' . $mail_id), 'Disposisi berhasil dikirim.');
    } catch (PDOException $e) {
        $pdo->rollBack();
        redirect(base_url('admin/surat-masuk/detail.php?id=' . $mail_id), 'Gagal: ' . $e->getMessage(), 'error');
    }
}
?>
