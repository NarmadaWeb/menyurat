<?php
$page_title = "Detail Surat Masuk";
require_once __DIR__ . '/../../includes/auth.php';
require_login();
$id = (int)$_GET['id'];
$mail = $pdo->query("SELECT * FROM surat_masuk WHERE id = $id")->fetch();
if (!$mail) redirect(base_url('admin/surat-masuk/index.php'));
$dispositions = $pdo->query("SELECT d.*, u.nama_lengkap FROM disposisi d JOIN pengguna u ON d.penerima_id = u.id WHERE surat_masuk_id = $id")->fetchAll();
require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../includes/sidebar.php';
?>
<div class="bg-white p-8 rounded-2xl border border-outline-variant shadow-sm">
    <h2 class="text-2xl font-bold mb-4"><?= $mail['perihal'] ?></h2>
    <p>No: <?= $mail['nomor'] ?> | Dari: <?= $mail['pengirim'] ?></p>
    <div class="mt-8">
        <h3 class="font-bold mb-4">Form Disposisi</h3>
        <form action="disposisi_proses.php" method="POST" class="space-y-4">
            <input type="hidden" name="mail_id" value="<?= $id ?>">
            <textarea name="instruction" required class="w-full border rounded-xl p-4" placeholder="Instruksi..."></textarea>
            <div class="grid grid-cols-2 gap-4">
                <input type="date" name="deadline" class="border rounded-xl p-2">
                <select name="status_update" class="border rounded-xl p-2">
                    <option value="processed">Proses</option>
                    <option value="finished">Selesai</option>
                </select>
            </div>
            <div class="space-y-2">
                <?php $users = $pdo->query("SELECT * FROM pengguna WHERE id != {$_SESSION['user_id']}")->fetchAll();
                foreach($users as $u): ?>
                    <label class="block"><input type="checkbox" name="receiver_ids[]" value="<?= $u['id'] ?>"> <?= $u['nama_lengkap'] ?></label>
                <?php endforeach; ?>
            </div>
            <button type="submit" class="bg-primary text-white px-8 py-2 rounded-xl font-bold">Kirim Disposisi</button>
        </form>
    </div>
</div>
<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
