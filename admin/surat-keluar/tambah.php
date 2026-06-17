<?php
$page_title = "Tambah Surat Keluar";
require_once __DIR__ . '/../../includes/auth.php';
require_login();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $pdo->prepare("INSERT INTO outgoing_mails (number, date_sent, recipient, subject, description) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$_POST['number'], $_POST['date_sent'], $_POST['recipient'], $_POST['subject'], $_POST['description']]);
    redirect(base_url('admin/surat-keluar/index.php'), 'Berhasil.');
}
require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../includes/sidebar.php';
?>
<form method="POST" class="bg-white p-8 rounded-2xl border border-outline-variant space-y-4">
    <input type="text" name="number" required placeholder="No Surat" class="w-full border rounded-xl p-3">
    <input type="date" name="date_sent" required class="w-full border rounded-xl p-3">
    <input type="text" name="recipient" required placeholder="Penerima" class="w-full border rounded-xl p-3">
    <input type="text" name="subject" required placeholder="Perihal" class="w-full border rounded-xl p-3">
    <textarea name="description" class="w-full border rounded-xl p-3" placeholder="Keterangan"></textarea>
    <button type="submit" class="bg-primary text-white px-8 py-3 rounded-xl font-bold">Simpan</button>
</form>
<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
