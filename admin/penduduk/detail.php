<?php
$page_title = "Detail Penduduk";
require_once __DIR__ . '/../../includes/auth.php';
require_login();

$id = (int)($_GET['id'] ?? 0);
$stmt = $pdo->prepare("SELECT * FROM penduduk WHERE id = ?");
$stmt->execute([$id]);
$citizen = $stmt->fetch();

if (!$citizen) {
    redirect(base_url('admin/penduduk/index.php'), 'Data penduduk tidak ditemukan.', 'error');
}

// Fetch history of letters or requests
$requests = $pdo->prepare("SELECT r.*, mt.nama as jenis_surat_nama FROM pengajuan_warga r LEFT JOIN jenis_surat mt ON r.jenis_surat_id = mt.id WHERE r.nik = ? ORDER BY r.dibuat_pada DESC");
$requests->execute([$citizen['nik']]);
$citizen_requests = $requests->fetchAll();

require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../includes/sidebar.php';
?>

<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 rounded-2xl bg-primary/10 text-primary flex items-center justify-center font-bold text-2xl">
                <?= strtoupper(substr($citizen['nama_lengkap'], 0, 1)) ?>
            </div>
            <div>
                <h2 class="text-2xl font-bold text-on-surface"><?= htmlspecialchars($citizen['nama_lengkap']) ?></h2>
                <p class="text-on-surface-variant text-sm font-mono">NIK: <?= $citizen['nik'] ?></p>
            </div>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <a href="<?= base_url('admin/surat/buat.php?nik=' . urlencode($citizen['nik'])) ?>" class="bg-primary hover:bg-primary-container text-white px-4 py-2 rounded-xl text-sm font-bold shadow-sm transition-all flex items-center gap-1.5">
                <span class="material-symbols-outlined text-base">description</span> Buat Surat
            </a>
            <a href="<?= base_url('admin/penduduk/edit.php?id=' . $citizen['id']) ?>" class="bg-secondary/10 hover:bg-secondary/20 text-secondary px-4 py-2 rounded-xl text-sm font-bold transition-all flex items-center gap-1.5">
                <span class="material-symbols-outlined text-base">edit</span> Edit
            </a>
            <a href="<?= base_url('admin/penduduk/index.php') ?>" class="border border-outline-variant px-4 py-2 rounded-xl text-sm font-semibold hover:bg-surface-container-low transition-all">
                Kembali
            </a>
        </div>
    </div>

    <!-- Data Detail Card -->
    <div class="bg-white rounded-2xl border border-outline-variant shadow-sm overflow-hidden">
        <div class="p-6 border-b border-outline-variant bg-surface-container-low/50">
            <h3 class="font-bold text-base text-primary">Informasi Biodata Kependudukan</h3>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6 text-sm">
                <div>
                    <p class="text-xs font-bold uppercase text-on-surface-variant">Nomor Induk Kependudukan</p>
                    <p class="text-base font-semibold font-mono text-on-surface mt-1"><?= $citizen['nik'] ?></p>
                </div>
                <div>
                    <p class="text-xs font-bold uppercase text-on-surface-variant">Nomor Kartu Keluarga</p>
                    <p class="text-base font-semibold font-mono text-on-surface mt-1"><?= $citizen['no_kk'] ?: '-' ?></p>
                </div>
                <div>
                    <p class="text-xs font-bold uppercase text-on-surface-variant">Nama Lengkap</p>
                    <p class="text-base font-semibold text-on-surface mt-1"><?= htmlspecialchars($citizen['nama_lengkap']) ?></p>
                </div>
                <div>
                    <p class="text-xs font-bold uppercase text-on-surface-variant">Jenis Kelamin</p>
                    <p class="text-sm font-semibold text-on-surface mt-1">
                        <span class="px-2.5 py-1 rounded-full text-xs <?= $citizen['jenis_kelamin'] === 'Laki-laki' ? 'bg-blue-100 text-blue-800' : 'bg-pink-100 text-pink-800' ?>">
                            <?= $citizen['jenis_kelamin'] ?>
                        </span>
                    </p>
                </div>
                <div>
                    <p class="text-xs font-bold uppercase text-on-surface-variant">Tempat, Tanggal Lahir</p>
                    <p class="text-sm font-semibold text-on-surface mt-1"><?= $citizen['tempat_lahir'] ? $citizen['tempat_lahir'] . ', ' : '' ?><?= format_date($citizen['tanggal_lahir']) ?></p>
                </div>
                <div>
                    <p class="text-xs font-bold uppercase text-on-surface-variant">Agama</p>
                    <p class="text-sm font-semibold text-on-surface mt-1"><?= $citizen['agama'] ?: '-' ?></p>
                </div>
                <div>
                    <p class="text-xs font-bold uppercase text-on-surface-variant">Pendidikan</p>
                    <p class="text-sm font-semibold text-on-surface mt-1"><?= $citizen['pendidikan'] ?: '-' ?></p>
                </div>
                <div>
                    <p class="text-xs font-bold uppercase text-on-surface-variant">Pekerjaan</p>
                    <p class="text-sm font-semibold text-on-surface mt-1"><?= $citizen['pekerjaan'] ?: '-' ?></p>
                </div>
                <div>
                    <p class="text-xs font-bold uppercase text-on-surface-variant">Status Perkawinan</p>
                    <p class="text-sm font-semibold text-on-surface mt-1"><?= $citizen['status_perkawinan'] ?: '-' ?></p>
                </div>
                <div>
                    <p class="text-xs font-bold uppercase text-on-surface-variant">Kewarganegaraan</p>
                    <p class="text-sm font-semibold text-on-surface mt-1"><?= $citizen['kewarganegaraan'] ?: 'WNI' ?></p>
                </div>
                <div>
                    <p class="text-xs font-bold uppercase text-on-surface-variant">Nomor Telepon / WhatsApp</p>
                    <p class="text-sm font-semibold text-on-surface mt-1"><?= $citizen['telepon'] ?: '-' ?></p>
                </div>
                <div>
                    <p class="text-xs font-bold uppercase text-on-surface-variant">Terdaftar Pada</p>
                    <p class="text-sm font-semibold text-on-surface mt-1"><?= format_date($citizen['dibuat_pada']) ?></p>
                </div>
                <div class="sm:col-span-2 md:col-span-3">
                    <p class="text-xs font-bold uppercase text-on-surface-variant">Alamat Lengkap</p>
                    <p class="text-sm font-semibold text-on-surface mt-1"><?= htmlspecialchars($citizen['alamat'] ?: '-') ?> <?= $citizen['rt'] ? '(RT ' . $citizen['rt'] . ' / RW ' . $citizen['rw'] . ')' : '' ?></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Riwayat Pengajuan Surat oleh Warga Ini -->
    <div class="bg-white rounded-2xl border border-outline-variant shadow-sm overflow-hidden">
        <div class="p-6 border-b border-outline-variant">
            <h3 class="font-bold text-base text-on-surface">Riwayat Pengajuan Surat Online</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-sm">
                <thead>
                    <tr class="bg-surface-container-low border-b border-outline-variant">
                        <th class="px-6 py-3 text-xs font-bold uppercase text-on-surface-variant">No. Registrasi</th>
                        <th class="px-6 py-3 text-xs font-bold uppercase text-on-surface-variant">Jenis Surat</th>
                        <th class="px-6 py-3 text-xs font-bold uppercase text-on-surface-variant">Tanggal</th>
                        <th class="px-6 py-3 text-xs font-bold uppercase text-on-surface-variant text-center">Status</th>
                        <th class="px-6 py-3 text-xs font-bold uppercase text-on-surface-variant text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-outline-variant">
                    <?php if (empty($citizen_requests)): ?>
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-on-surface-variant">Belum ada riwayat pengajuan surat online untuk NIK ini.</td>
                        </tr>
                    <?php endif; ?>
                    <?php foreach ($citizen_requests as $req): ?>
                        <tr class="hover:bg-surface-container-low transition-colors">
                            <td class="px-6 py-3 font-mono font-bold text-primary"><?= $req['nomor_registrasi'] ?></td>
                            <td class="px-6 py-3"><?= $req['jenis_surat_nama'] ?></td>
                            <td class="px-6 py-3 text-xs"><?= format_date($req['dibuat_pada']) ?></td>
                            <td class="px-6 py-3 text-center">
                                <span class="px-2.5 py-1 rounded-full text-[11px] font-bold uppercase
                                    <?= $req['status'] === 'selesai' ? 'bg-green-100 text-green-800' : ($req['status'] === 'diproses' ? 'bg-yellow-100 text-yellow-800' : 'bg-blue-100 text-blue-800') ?>">
                                    <?= $req['status'] ?>
                                </span>
                            </td>
                            <td class="px-6 py-3 text-right">
                                <a href="<?= base_url('admin/pengajuan/detail.php?id=' . $req['id']) ?>" class="text-primary hover:underline font-bold text-xs">Detail</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
