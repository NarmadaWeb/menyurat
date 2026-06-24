<?php
$page_title = "Daftar Pengajuan Warga";
require_once __DIR__ . '/../../includes/auth.php';
require_login();

require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../includes/sidebar.php';

$search = isset($_GET['search']) ? sanitize($_GET['search']) : '';
$status = isset($_GET['status']) ? sanitize($_GET['status']) : '';

$query = "SELECT r.*, mt.nama as mail_type_name FROM pengajuan_warga r LEFT JOIN jenis_surat mt ON r.jenis_surat_id = mt.id WHERE 1=1";
$params = [];

if ($search) {
    $query .= " AND (r.nama_lengkap LIKE ? OR r.nik LIKE ? OR r.nomor_registrasi LIKE ?)";
    $params = array_merge($params, ["%$search%", "%$search%", "%$search%"]);
}

if ($status) {
    $query .= " AND r.status = ?";
    $params[] = $status;
}

$query .= " ORDER BY r.dibuat_pada DESC";
$stmt = $pdo->prepare($query);
$stmt->execute($params);
$requests = $stmt->fetchAll();
?>

<div class="mb-8">
    <h2 class="text-2xl font-bold text-on-surface">Pengajuan Warga</h2>
    <p class="text-on-surface-variant">Verifikasi dan proses pengajuan surat dari masyarakat.</p>
</div>

<!-- Filter Bar -->
<div class="bg-white p-4 rounded-2xl shadow-sm border border-outline-variant mb-8">
    <form action="" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="md:col-span-2 relative">
            <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-on-surface-variant">search</span>
            <input type="text" name="search" value="<?= $search ?>" placeholder="Cari nama, NIK, atau no registrasi..."
                class="w-full pl-10 pr-4 py-2 bg-surface-container-low border border-outline-variant rounded-xl focus:ring-2 focus:ring-primary outline-none text-sm">
        </div>
        <div>
            <select name="status" class="w-full py-2 bg-surface-container-low border border-outline-variant rounded-xl focus:ring-2 focus:ring-primary outline-none text-sm">
                <option value="">Semua Status</option>
                <option value="baru" <?= $status === 'baru' ? 'selected' : '' ?>>Baru</option>
                <option value="diproses" <?= $status === 'diproses' ? 'selected' : '' ?>>Diproses</option>
                <option value="selesai" <?= $status === 'selesai' ? 'selected' : '' ?>>Selesai</option>
                <option value="ditolak" <?= $status === 'ditolak' ? 'selected' : '' ?>>Ditolak</option>
            </select>
        </div>
        <button type="submit" class="bg-surface-container-high text-primary font-bold py-2 rounded-xl border border-primary hover:bg-primary hover:text-white transition-all">
            Filter
        </button>
    </form>
</div>

<!-- Table -->
<div class="bg-white rounded-2xl border border-outline-variant shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-surface-container-low border-b border-outline-variant">
                    <th class="px-6 py-4 text-xs font-bold uppercase text-on-surface-variant">Pemohon / Reg No</th>
                    <th class="px-6 py-4 text-xs font-bold uppercase text-on-surface-variant">Jenis Surat</th>
                    <th class="px-6 py-4 text-xs font-bold uppercase text-on-surface-variant">Tanggal</th>
                    <th class="px-6 py-4 text-xs font-bold uppercase text-on-surface-variant text-center">Status</th>
                    <th class="px-6 py-4 text-xs font-bold uppercase text-on-surface-variant text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-outline-variant">
                <?php if (empty($requests)): ?>
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-on-surface-variant">Tidak ada pengajuan warga.</td>
                    </tr>
                <?php endif; ?>
                <?php foreach ($requests as $req): ?>
                <tr class="hover:bg-surface-container-low transition-colors">
                    <td class="px-6 py-4">
                        <p class="font-bold text-sm text-on-surface"><?= $req['nama_lengkap'] ?></p>
                        <p class="text-xs text-primary font-mono"><?= $req['nomor_registrasi'] ?></p>
                    </td>
                    <td class="px-6 py-4 text-sm"><?= $req['mail_type_name'] ?></td>
                    <td class="px-6 py-4 text-sm"><?= format_date($req['dibuat_pada']) ?></td>
                    <td class="px-6 py-4 text-center">
                        <?php
                        $st_classes = [
                            'baru' => 'bg-blue-100 text-blue-700',
                            'diproses' => 'bg-yellow-100 text-yellow-800',
                            'selesai' => 'bg-green-100 text-green-800',
                            'ditolak' => 'bg-red-100 text-red-800'
                        ];
                        $st_labels = ['baru' => 'Baru', 'diproses' => 'Proses', 'selesai' => 'Selesai', 'ditolak' => 'Ditolak'];
                        ?>
                        <span class="px-3 py-1 rounded-full text-[10px] font-bold uppercase <?= $st_classes[$req['status']] ?>">
                            <?= $st_labels[$req['status']] ?>
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center justify-end gap-2">
                            <a href="<?= base_url('admin/pengajuan/detail.php?id=' . $req['id']) ?>" class="p-2 text-primary hover:bg-primary/10 rounded-lg" title="Detail & Verifikasi">
                                <span class="material-symbols-outlined text-lg">rule</span>
                            </a>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
