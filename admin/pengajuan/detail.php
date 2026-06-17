<?php
$page_title = "Detail Pengajuan";
require_once __DIR__ . '/../../includes/auth.php';
require_login();
$id = (int)$_GET['id'];
$request = $pdo->query("SELECT r.*, mt.name as mail_type_name FROM citizen_requests r LEFT JOIN mail_types mt ON r.mail_type_id = mt.id WHERE r.id = $id")->fetch();
require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../includes/sidebar.php';
?>
<div class="bg-white p-8 rounded-2xl border border-outline-variant shadow-sm">
    <h2 class="text-2xl font-bold mb-4">Pengajuan: <?= $request['full_name'] ?></h2>
    <p>NIK: <?= $request['nik'] ?> | Jenis: <?= $request['mail_type_name'] ?></p>
    <div class="mt-8">
        <form action="proses.php" method="POST" class="space-y-4">
            <input type="hidden" name="request_id" value="<?= $id ?>">
            <select name="status" class="w-full border rounded-xl p-3">
                <option value="processed">Proses</option>
                <option value="finished">Selesai</option>
                <option value="rejected">Tolak</option>
            </select>
            <button type="submit" class="bg-primary text-white px-8 py-3 rounded-xl font-bold">Update Status</button>
        </form>
    </div>
</div>
<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
