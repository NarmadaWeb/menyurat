<?php
$page_title = "Cek Status Pengajuan";
require_once __DIR__ . '/../includes/header.php';

$request = null;
$error = null;

if (isset($_GET['code'])) {
    $code = sanitize($_GET['code']);
    $stmt = $pdo->prepare("SELECT r.*, mt.name as mail_type_name FROM citizen_requests r LEFT JOIN mail_types mt ON r.mail_type_id = mt.id WHERE r.registration_number = ?");
    $stmt->execute([$code]);
    $request = $stmt->fetch();

    if (!$request) {
        $error = "Nomor registrasi tidak ditemukan.";
    }
}
?>

<div class="max-w-4xl mx-auto py-12 px-6">
    <div class="mb-12 text-center">
        <h1 class="text-4xl font-bold text-on-surface mb-4">Lacak Status Pengajuan</h1>
    </div>

    <div class="bg-white rounded-3xl p-8 shadow-xl border border-outline-variant mb-12">
        <form action="" method="GET" class="flex flex-col md:flex-row gap-4">
            <div class="flex-1 relative">
                <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-on-surface-variant">search</span>
                <input type="text" name="code" value="<?= isset($code) ? $code : '' ?>" required placeholder="REG-..."
                    class="w-full pl-12 pr-4 py-4 bg-surface-container-low border border-outline-variant rounded-2xl focus:ring-2 focus:ring-primary outline-none">
            </div>
            <button type="submit" class="bg-primary text-white px-10 py-4 rounded-2xl font-bold">Cek Status</button>
        </form>
    </div>

    <?php if ($error): ?>
        <p class="text-red-600 text-center"><?= $error ?></p>
    <?php endif; ?>

    <?php if ($request): ?>
        <div class="bg-white rounded-3xl shadow-xl border border-outline-variant p-8">
            <h2 class="text-2xl font-bold mb-4">Status: <?= $request['status'] ?></h2>
            <p><strong>Nama:</strong> <?= $request['full_name'] ?></p>
            <p><strong>Jenis Surat:</strong> <?= $request['mail_type_name'] ?></p>
        </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
