<?php
$page_title = "Jenis Surat";
require_once __DIR__ . '/../../includes/auth.php';
require_login();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitize($_POST['name']);
    $code = sanitize($_POST['code']);

    if (isset($_POST['id']) && !empty($_POST['id'])) {
        $id = (int)$_POST['id'];
        $stmt = $pdo->prepare("UPDATE jenis_surat SET nama = ?, kode = ? WHERE id = ?");
        $stmt->execute([$name, $code, $id]);
        $msg = "Jenis surat berhasil diperbarui.";
    } else {
        $stmt = $pdo->prepare("INSERT INTO jenis_surat (nama, kode) VALUES (?, ?)");
        $stmt->execute([$name, $code]);
        $msg = "Jenis surat baru ditambahkan.";
    }
    redirect(base_url('admin/master/jenis-surat.php'), $msg);
}

if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $pdo->prepare("DELETE FROM jenis_surat WHERE id = ?")->execute([$id]);
    redirect(base_url('admin/master/jenis-surat.php'), 'Jenis surat dihapus.');
}

$types = $pdo->query("SELECT * FROM jenis_surat ORDER BY nama ASC")->fetchAll();

require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../includes/sidebar.php';
?>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    <div class="lg:col-span-1">
        <div class="bg-white rounded-2xl shadow-sm border border-outline-variant p-6 sticky top-24">
            <h3 class="font-bold mb-6" id="form-title">Tambah Jenis Surat</h3>
            <form action="" method="POST" class="space-y-4">
                <input type="hidden" name="id" id="type-id">
                <div>
                    <label for="name" class="block text-sm font-semibold mb-2">Nama Jenis Surat</label>
                    <input type="text" name="name" id="type-name" required placeholder="Contoh: Surat Domisili"
                        class="w-full px-4 py-2 border border-outline-variant rounded-xl focus:ring-2 focus:ring-primary outline-none transition-all">
                </div>
                <div>
                    <label for="code" class="block text-sm font-semibold mb-2">Kode (Opsional)</label>
                    <input type="text" name="code" id="type-code" placeholder="Contoh: DOM"
                        class="w-full px-4 py-2 border border-outline-variant rounded-xl focus:ring-2 focus:ring-primary outline-none transition-all">
                </div>
                <div class="flex gap-2">
                    <button type="submit" class="flex-1 bg-primary text-white py-2 rounded-xl font-bold hover:bg-primary-container transition-all">
                        Simpan
                    </button>
                    <button type="button" onclick="resetForm()" class="px-4 py-2 border border-outline-variant rounded-xl hover:bg-surface-container-low transition-all">
                        Reset
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="lg:col-span-2">
        <div class="bg-white rounded-2xl border border-outline-variant shadow-sm overflow-hidden">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-surface-container-low border-b border-outline-variant">
                        <th class="px-6 py-4 text-xs font-bold uppercase text-on-surface-variant">Nama Jenis Surat</th>
                        <th class="px-6 py-4 text-xs font-bold uppercase text-on-surface-variant">Kode</th>
                        <th class="px-6 py-4 text-xs font-bold uppercase text-on-surface-variant text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-outline-variant">
                    <?php if (empty($types)): ?>
                        <tr>
                            <td colspan="3" class="px-6 py-8 text-center text-on-surface-variant">Belum ada data.</td>
                        </tr>
                    <?php endif; ?>
                    <?php foreach ($types as $type): ?>
                    <tr class="hover:bg-surface-container-low transition-colors">
                        <td class="px-6 py-4 font-medium"><?= $type['nama'] ?></td>
                        <td class="px-6 py-4 font-mono text-sm"><?= $type['kode'] ?: '-' ?></td>
                        <td class="px-6 py-4">
                            <div class="flex items-center justify-end gap-2">
                                <button onclick="editType(<?= $type['id'] ?>, '<?= $type['nama'] ?>', '<?= $type['kode'] ?>')" class="p-2 text-secondary hover:bg-secondary/10 rounded-lg">
                                    <span class="material-symbols-outlined text-lg">edit</span>
                                </button>
                                <a href="?delete=<?= $type['id'] ?>" onclick="return confirm('Hapus jenis surat ini?')" class="p-2 text-error hover:bg-red-50 rounded-lg">
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
</div>

<script>
function editType(id, name, code) {
    document.getElementById('form-title').textContent = 'Edit Jenis Surat';
    document.getElementById('type-id').value = id;
    document.getElementById('type-name').value = name;
    document.getElementById('type-code').value = code;
}

function resetForm() {
    document.getElementById('form-title').textContent = 'Tambah Jenis Surat';
    document.getElementById('type-id').value = '';
    document.getElementById('type-name').value = '';
    document.getElementById('type-code').value = '';
}
</script>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
