<?php
$page_title = "Data Penduduk";
require_once __DIR__ . '/../../includes/auth.php';
require_login();

require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../includes/sidebar.php';

$search = isset($_GET['search']) ? sanitize($_GET['search']) : '';
$gender = isset($_GET['gender']) ? sanitize($_GET['gender']) : '';

$query = "SELECT * FROM penduduk WHERE 1=1";
$params = [];

if ($search) {
    $query .= " AND (nama_lengkap LIKE ? OR nik LIKE ? OR no_kk LIKE ? OR alamat LIKE ?)";
    $params = array_merge($params, ["%$search%", "%$search%", "%$search%", "%$search%"]);
}

if ($gender) {
    $query .= " AND jenis_kelamin = ?";
    $params[] = $gender;
}

$query .= " ORDER BY nama_lengkap ASC";
$stmt = $pdo->prepare($query);
$stmt->execute($params);
$citizens = $stmt->fetchAll();

// Statistics
$total_warga = $pdo->query("SELECT COUNT(*) FROM penduduk")->fetchColumn();
$total_laki = $pdo->query("SELECT COUNT(*) FROM penduduk WHERE jenis_kelamin = 'Laki-laki'")->fetchColumn();
$total_perempuan = $pdo->query("SELECT COUNT(*) FROM penduduk WHERE jenis_kelamin = 'Perempuan'")->fetchColumn();
?>

<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
    <div>
        <h2 class="text-2xl font-bold text-on-surface">Data Penduduk</h2>
        <p class="text-on-surface-variant text-sm">Kelola master data kependudukan desa untuk mempermudah pelayanan surat.</p>
    </div>
    <div class="flex flex-wrap gap-2">
        <a href="<?= base_url('admin/penduduk/tambah.php') ?>" class="bg-primary hover:bg-primary-container text-white px-5 py-2.5 rounded-xl flex items-center gap-2 font-bold transition-all shadow-sm text-sm">
            <span class="material-symbols-outlined text-lg">person_add</span>
            Tambah Penduduk
        </a>
    </div>
</div>

<!-- Stats mini -->
<div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
    <div class="bg-white p-5 rounded-2xl border border-outline-variant shadow-sm flex items-center gap-4">
        <div class="w-12 h-12 rounded-xl bg-primary/10 flex items-center justify-center text-primary">
            <span class="material-symbols-outlined text-2xl">groups</span>
        </div>
        <div>
            <p class="text-xs text-on-surface-variant font-bold uppercase">Total Penduduk</p>
            <h3 class="text-2xl font-bold text-on-surface"><?= $total_warga ?></h3>
        </div>
    </div>
    <div class="bg-white p-5 rounded-2xl border border-outline-variant shadow-sm flex items-center gap-4">
        <div class="w-12 h-12 rounded-xl bg-blue-50 flex items-center justify-center text-blue-700">
            <span class="material-symbols-outlined text-2xl">male</span>
        </div>
        <div>
            <p class="text-xs text-on-surface-variant font-bold uppercase">Laki-Laki</p>
            <h3 class="text-2xl font-bold text-on-surface"><?= $total_laki ?></h3>
        </div>
    </div>
    <div class="bg-white p-5 rounded-2xl border border-outline-variant shadow-sm flex items-center gap-4">
        <div class="w-12 h-12 rounded-xl bg-pink-50 flex items-center justify-center text-pink-700">
            <span class="material-symbols-outlined text-2xl">female</span>
        </div>
        <div>
            <p class="text-xs text-on-surface-variant font-bold uppercase">Perempuan</p>
            <h3 class="text-2xl font-bold text-on-surface"><?= $total_perempuan ?></h3>
        </div>
    </div>
</div>

<!-- Filter Bar -->
<div class="bg-white p-4 rounded-2xl shadow-sm border border-outline-variant mb-6">
    <form action="" method="GET" class="grid grid-cols-1 sm:grid-cols-4 gap-3">
        <div class="sm:col-span-2 relative">
            <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-on-surface-variant text-sm">search</span>
            <input type="text" name="search" value="<?= $search ?>" placeholder="Cari NIK, KK, Nama, atau Alamat..."
                class="w-full pl-10 pr-4 py-2.5 bg-surface-container-low border border-outline-variant rounded-xl focus:ring-2 focus:ring-primary outline-none text-sm">
        </div>
        <div>
            <select name="gender" class="w-full py-2.5 px-3 bg-surface-container-low border border-outline-variant rounded-xl focus:ring-2 focus:ring-primary outline-none text-sm">
                <option value="">Semua Jenis Kelamin</option>
                <option value="Laki-laki" <?= $gender === 'Laki-laki' ? 'selected' : '' ?>>Laki-laki</option>
                <option value="Perempuan" <?= $gender === 'Perempuan' ? 'selected' : '' ?>>Perempuan</option>
            </select>
        </div>
        <div class="flex gap-2">
            <button type="submit" class="flex-1 bg-primary text-white font-bold py-2.5 rounded-xl hover:bg-primary-container transition-all text-sm flex items-center justify-center gap-1">
                <span class="material-symbols-outlined text-sm">filter_alt</span> Filter
            </button>
            <?php if ($search || $gender): ?>
                <a href="<?= base_url('admin/penduduk/index.php') ?>" class="px-3 py-2.5 border border-outline-variant rounded-xl hover:bg-surface-container-low text-xs flex items-center justify-center text-on-surface-variant">Reset</a>
            <?php endif; ?>
        </div>
    </form>
</div>

<!-- Table -->
<div class="bg-white rounded-2xl border border-outline-variant shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-surface-container-low border-b border-outline-variant">
                    <th class="px-6 py-4 text-xs font-bold uppercase text-on-surface-variant">NIK / No. KK</th>
                    <th class="px-6 py-4 text-xs font-bold uppercase text-on-surface-variant">Nama Lengkap</th>
                    <th class="px-6 py-4 text-xs font-bold uppercase text-on-surface-variant">TTL & JK</th>
                    <th class="px-6 py-4 text-xs font-bold uppercase text-on-surface-variant">Pekerjaan</th>
                    <th class="px-6 py-4 text-xs font-bold uppercase text-on-surface-variant">Alamat</th>
                    <th class="px-6 py-4 text-xs font-bold uppercase text-on-surface-variant text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-outline-variant">
                <?php if (empty($citizens)): ?>
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-on-surface-variant">
                            <span class="material-symbols-outlined text-4xl text-outline-variant mb-2">person_off</span>
                            <p>Belum ada data penduduk.</p>
                        </td>
                    </tr>
                <?php endif; ?>
                <?php foreach ($citizens as $c): ?>
                <tr class="hover:bg-surface-container-low transition-colors">
                    <td class="px-6 py-4">
                        <p class="font-bold text-sm text-primary font-mono"><?= $c['nik'] ?></p>
                        <p class="text-xs text-on-surface-variant font-mono"><?= $c['no_kk'] ?: '-' ?></p>
                    </td>
                    <td class="px-6 py-4">
                        <a href="<?= base_url('admin/penduduk/detail.php?id=' . $c['id']) ?>" class="font-bold text-sm text-on-surface hover:text-primary transition-colors">
                            <?= $c['nama_lengkap'] ?>
                        </a>
                        <p class="text-xs text-on-surface-variant"><?= $c['status_perkawinan'] ?> &bull; <?= $c['agama'] ?></p>
                    </td>
                    <td class="px-6 py-4 text-sm">
                        <p class="text-xs text-on-surface"><?= $c['tempat_lahir'] ? $c['tempat_lahir'] . ', ' : '' ?><?= format_date($c['tanggal_lahir']) ?></p>
                        <span class="inline-block mt-1 px-2 py-0.5 rounded text-[11px] font-medium <?= $c['jenis_kelamin'] === 'Laki-laki' ? 'bg-blue-100 text-blue-700' : 'bg-pink-100 text-pink-700' ?>">
                            <?= $c['jenis_kelamin'] ?>
                        </span>
                    </td>
                    <td class="px-6 py-4 text-sm text-on-surface-variant">
                        <?= $c['pekerjaan'] ?: '-' ?>
                    </td>
                    <td class="px-6 py-4 text-xs text-on-surface-variant max-w-xs truncate">
                        <?= $c['alamat'] ?> <?= $c['rt'] ? '(RT ' . $c['rt'] . '/RW ' . $c['rw'] . ')' : '' ?>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center justify-end gap-1">
                            <a href="<?= base_url('admin/surat/buat.php?nik=' . urlencode($c['nik'])) ?>" class="p-2 text-primary hover:bg-primary/10 rounded-lg transition-colors" title="Buat Surat untuk Penduduk Ini">
                                <span class="material-symbols-outlined text-lg">description</span>
                            </a>
                            <a href="<?= base_url('admin/penduduk/detail.php?id=' . $c['id']) ?>" class="p-2 text-on-surface-variant hover:bg-surface-container-high rounded-lg transition-colors" title="Detail">
                                <span class="material-symbols-outlined text-lg">visibility</span>
                            </a>
                            <a href="<?= base_url('admin/penduduk/edit.php?id=' . $c['id']) ?>" class="p-2 text-secondary hover:bg-secondary/10 rounded-lg transition-colors" title="Edit">
                                <span class="material-symbols-outlined text-lg">edit</span>
                            </a>
                            <a href="<?= base_url('admin/penduduk/hapus.php?id=' . $c['id']) ?>" onclick="return confirm('Apakah Anda yakin ingin menghapus data penduduk <?= htmlspecialchars($c['nama_lengkap']) ?>?');" class="p-2 text-error hover:bg-red-50 rounded-lg transition-colors" title="Hapus">
                                <span class="material-symbols-outlined text-lg">delete</span>
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
