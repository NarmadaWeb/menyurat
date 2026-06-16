<?php
$page_title = "Daftar Surat Keluar";
require_once __DIR__ . '/../../includes/auth.php';
require_login();

require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../includes/sidebar.php';

$search = isset($_GET['search']) ? sanitize($_GET['search']) : '';

$query = "SELECT * FROM outgoing_mails WHERE 1=1";
$params = [];

if ($search) {
    $query .= " AND (number LIKE ? OR subject LIKE ? OR recipient LIKE ?)";
    $params = array_merge($params, ["%$search%", "%$search%", "%$search%"]);
}

$query .= " ORDER BY date_sent DESC, created_at DESC";
$stmt = $pdo->prepare($query);
$stmt->execute($params);
$mails = $stmt->fetchAll();
?>

<div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
    <div>
        <h2 class="text-2xl font-bold text-on-surface">Surat Keluar</h2>
        <p class="text-on-surface-variant">Kelola seluruh arsip surat yang diterbitkan desa.</p>
    </div>
    <a href="<?= base_url('admin/surat-keluar/tambah.php') ?>" class="bg-primary text-white px-6 py-2.5 rounded-xl flex items-center gap-2 font-bold hover:bg-primary-container transition-all shadow-md">
        <span class="material-symbols-outlined">add</span>
        Buat Surat Baru
    </a>
</div>

<div class="bg-white p-4 rounded-2xl shadow-sm border border-outline-variant mb-8">
    <form action="" method="GET" class="flex gap-4">
        <div class="flex-1 relative">
            <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-on-surface-variant">search</span>
            <input type="text" name="search" value="<?= $search ?>" placeholder="Cari nomor, perihal, atau tujuan..."
                class="w-full pl-10 pr-4 py-2 bg-surface-container-low border border-outline-variant rounded-xl focus:ring-2 focus:ring-primary outline-none text-sm">
        </div>
        <button type="submit" class="bg-surface-container-high text-primary font-bold px-8 py-2 rounded-xl border border-primary hover:bg-primary hover:text-white transition-all">
            Cari
        </button>
    </form>
</div>

<div class="bg-white rounded-2xl border border-outline-variant shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-surface-container-low border-b border-outline-variant">
                    <th class="px-6 py-4 text-xs font-bold uppercase text-on-surface-variant">No. Surat / Tanggal</th>
                    <th class="px-6 py-4 text-xs font-bold uppercase text-on-surface-variant">Tujuan</th>
                    <th class="px-6 py-4 text-xs font-bold uppercase text-on-surface-variant">Perihal</th>
                    <th class="px-6 py-4 text-xs font-bold uppercase text-on-surface-variant text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-outline-variant">
                <?php if (empty($mails)): ?>
                    <tr>
                        <td colspan="4" class="px-6 py-12 text-center text-on-surface-variant">Tidak ada data surat keluar.</td>
                    </tr>
                <?php endif; ?>
                <?php foreach ($mails as $mail): ?>
                <tr class="hover:bg-surface-container-low transition-colors">
                    <td class="px-6 py-4">
                        <p class="font-bold text-primary text-sm"><?= $mail['number'] ?></p>
                        <p class="text-xs text-on-surface-variant"><?= format_date($mail['date_sent']) ?></p>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg bg-secondary/10 flex items-center justify-center text-secondary">
                                <span class="material-symbols-outlined text-sm">person</span>
                            </div>
                            <span class="text-sm font-medium"><?= $mail['recipient'] ?></span>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <p class="text-sm truncate max-w-xs"><?= $mail['subject'] ?></p>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center justify-end gap-2">
                            <a href="<?= base_url('admin/surat-keluar/detail.php?id=' . $mail['id']) ?>" class="p-2 text-primary hover:bg-primary/10 rounded-lg" title="Detail">
                                <span class="material-symbols-outlined text-lg">visibility</span>
                            </a>
                            <a href="<?= base_url('admin/surat-keluar/edit.php?id=' . $mail['id']) ?>" class="p-2 text-secondary hover:bg-secondary/10 rounded-lg" title="Edit">
                                <span class="material-symbols-outlined text-lg">edit</span>
                            </a>
                            <button onclick="confirmDelete(<?= $mail['id'] ?>)" class="p-2 text-error hover:bg-red-50 rounded-lg" title="Hapus">
                                <span class="material-symbols-outlined text-lg">delete</span>
                            </button>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
function confirmDelete(id) {
    if (confirm('Apakah Anda yakin ingin menghapus surat keluar ini?')) {
        window.location.href = '<?= base_url('admin/surat-keluar/hapus.php?id=') ?>' + id;
    }
}
</script>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
