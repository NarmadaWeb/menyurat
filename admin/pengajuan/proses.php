<?php
require_once __DIR__ . '/../../includes/auth.php';
require_login();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $status = sanitize($_POST['status']);
    $request_id = (int)$_POST['request_id'];
    $file_hasil_path = null;

    if ($status === 'selesai' && isset($_FILES['file_hasil']) && $_FILES['file_hasil']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = __DIR__ . '/../../uploads/completed/';
        if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);

        $filename = time() . '_' . basename($_FILES['file_hasil']['name']);
        if (move_uploaded_file($_FILES['file_hasil']['tmp_name'], $upload_dir . $filename)) {
            $file_hasil_path = 'uploads/completed/' . $filename;
        }
    }

    if ($file_hasil_path) {
        $stmt = $pdo->prepare("UPDATE pengajuan_warga SET status = ?, file_hasil = ? WHERE id = ?");
        $stmt->execute([$status, $file_hasil_path, $request_id]);
    } else {
        $stmt = $pdo->prepare("UPDATE pengajuan_warga SET status = ? WHERE id = ?");
        $stmt->execute([$status, $request_id]);
    }
    
    redirect(base_url('admin/pengajuan/detail.php?id=' . $request_id), 'Status pengajuan berhasil diupdate.');
}
?>
