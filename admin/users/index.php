<?php
$page_title = "Daftar Pengguna";
require_once __DIR__ . '/../../includes/auth.php';
require_role('Admin');

require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../includes/sidebar.php';

$stmt = $pdo->query("SELECT u.*, r.nama as role_name FROM pengguna u JOIN peran r ON u.peran_id = r.id ORDER BY u.dibuat_pada DESC");
$users = $stmt->fetchAll();
?>

<div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
    <div>
        <h2 class="text-2xl font-bold text-on-surface">Manajemen Pengguna</h2>
        <p class="text-on-surface-variant">Kelola hak akses staf pemerintahan desa.</p>
    </div>
    <a href="<?= base_url('admin/users/tambah.php') ?>" class="bg-primary text-white px-6 py-2.5 rounded-xl flex items-center gap-2 font-bold hover:bg-primary-container transition-all shadow-md">
        <span class="material-symbols-outlined">person_add</span>
        Tambah User
    </a>
</div>

<div class="bg-white rounded-2xl border border-outline-variant shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-surface-container-low border-b border-outline-variant">
                    <th class="px-6 py-4 text-xs font-bold uppercase text-on-surface-variant">Nama & Profil</th>
                    <th class="px-6 py-4 text-xs font-bold uppercase text-on-surface-variant">Username</th>
                    <th class="px-6 py-4 text-xs font-bold uppercase text-on-surface-variant">Role</th>
                    <th class="px-6 py-4 text-xs font-bold uppercase text-on-surface-variant text-center">Status</th>
                    <th class="px-6 py-4 text-xs font-bold uppercase text-on-surface-variant text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-outline-variant">
                <?php foreach ($users as $user): ?>
                <tr class="hover:bg-surface-container-low transition-colors group">
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-primary/10 flex items-center justify-center text-primary font-bold">
                                <?= strtoupper(substr($user['nama_lengkap'], 0, 1)) ?>
                            </div>
                            <div>
                                <p class="font-bold text-sm"><?= $user['nama_lengkap'] ?></p>
                                <p class="text-xs text-on-surface-variant"><?= $user['email'] ?: '-' ?></p>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-sm font-mono"><?= $user['username'] ?></td>
                    <td class="px-6 py-4">
                        <span class="px-3 py-1 rounded-full text-[10px] font-bold uppercase bg-surface-container-high text-primary">
                            <?= $user['role_name'] ?>
                        </span>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <span class="px-3 py-1 rounded-full text-[10px] font-bold uppercase <?= $user['status'] === 'aktif' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' ?>">
                            <?= $user['status'] === 'aktif' ? 'Aktif' : 'Nonaktif' ?>
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center justify-end gap-2">
                            <a href="<?= base_url('admin/users/edit.php?id=' . $user['id']) ?>" class="p-2 text-secondary hover:bg-secondary/10 rounded-lg" title="Edit">
                                <span class="material-symbols-outlined text-lg">edit</span>
                            </a>
                            <?php if ($user['id'] != $_SESSION['user_id']): ?>
                            <button onclick="confirmDelete(<?= $user['id'] ?>)" class="p-2 text-error hover:bg-red-50 rounded-lg" title="Hapus">
                                <span class="material-symbols-outlined text-lg">delete</span>
                            </button>
                            <?php endif; ?>
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
    if (confirm('Apakah Anda yakin ingin menghapus user ini?')) {
        window.location.href = '<?= base_url('admin/users/hapus.php?id=') ?>' + id;
    }
}
</script>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
