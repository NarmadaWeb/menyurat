<?php
$page_title = "Edit Pengguna";
require_once __DIR__ . '/../../includes/auth.php';
require_role('Admin');

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$stmt = $pdo->prepare("SELECT * FROM pengguna WHERE id = ?");
$stmt->execute([$id]);
$user = $stmt->fetch();

if (!$user) {
    redirect(base_url('admin/users/index.php'), 'User tidak ditemukan.', 'error');
}

$roles = $pdo->query("SELECT * FROM peran")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = sanitize($_POST['username']);
    $full_name = sanitize($_POST['full_name']);
    $email = sanitize($_POST['email']);
    $role_id = (int)$_POST['role_id'];
    $status = sanitize($_POST['status']);

    try {
        if (!empty($_POST['password'])) {
            $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE pengguna SET username = ?, password = ?, nama_lengkap = ?, email = ?, peran_id = ?, status = ? WHERE id = ?");
            $stmt->execute([$username, $password, $full_name, $email, $role_id, $status, $id]);
        } else {
            $stmt = $pdo->prepare("UPDATE pengguna SET username = ?, nama_lengkap = ?, email = ?, peran_id = ?, status = ? WHERE id = ?");
            $stmt->execute([$username, $full_name, $email, $role_id, $status, $id]);
        }

        redirect(base_url('admin/users/index.php'), 'User berhasil diperbarui.');
    } catch (PDOException $e) {
        $error = "Gagal memperbarui user: Username mungkin sudah digunakan.";
    }
}

require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../includes/sidebar.php';
?>

<div class="mb-8 flex items-center gap-4">
    <a href="<?= base_url('admin/users/index.php') ?>" class="p-2 rounded-full hover:bg-surface-container-low transition-all">
        <span class="material-symbols-outlined text-on-surface-variant">arrow_back</span>
    </a>
    <div>
        <h2 class="text-2xl font-bold text-on-surface">Edit User</h2>
        <p class="text-on-surface-variant">Perbarui informasi akun staf.</p>
    </div>
</div>

<div class="max-w-2xl">
    <form action="" method="POST" class="bg-white rounded-2xl shadow-sm border border-outline-variant p-8 space-y-6">
        <?php if (isset($error)): ?>
            <div class="p-4 bg-red-100 text-red-800 rounded-lg text-sm"><?= $error ?></div>
        <?php endif; ?>

        <div>
            <label for="full_name" class="block text-sm font-semibold mb-2">Nama Lengkap</label>
            <input type="text" name="full_name" id="full_name" required value="<?= $user['nama_lengkap'] ?>"
                class="w-full px-4 py-3 border border-outline-variant rounded-xl focus:ring-2 focus:ring-primary outline-none transition-all">
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label for="username" class="block text-sm font-semibold mb-2">Username</label>
                <input type="text" name="username" id="username" required value="<?= $user['username'] ?>"
                    class="w-full px-4 py-3 border border-outline-variant rounded-xl focus:ring-2 focus:ring-primary outline-none transition-all">
            </div>
            <div>
                <label for="password" class="block text-sm font-semibold mb-2">Password (Kosongkan jika tidak diubah)</label>
                <input type="password" name="password" id="password"
                    class="w-full px-4 py-3 border border-outline-variant rounded-xl focus:ring-2 focus:ring-primary outline-none transition-all">
            </div>
        </div>

        <div>
            <label for="email" class="block text-sm font-semibold mb-2">Email (Opsional)</label>
            <input type="email" name="email" id="email" value="<?= $user['email'] ?>"
                class="w-full px-4 py-3 border border-outline-variant rounded-xl focus:ring-2 focus:ring-primary outline-none transition-all">
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label for="role_id" class="block text-sm font-semibold mb-2">Role / Hak Akses</label>
                <select name="role_id" id="role_id" required class="w-full px-4 py-3 border border-outline-variant rounded-xl focus:ring-2 focus:ring-primary outline-none transition-all">
                    <?php foreach ($roles as $role): ?>
                        <option value="<?= $role['id'] ?>" <?= $user['peran_id'] == $role['id'] ? 'selected' : '' ?>><?= $role['nama'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label for="status" class="block text-sm font-semibold mb-2">Status Akun</label>
                <select name="status" id="status" required class="w-full px-4 py-3 border border-outline-variant rounded-xl focus:ring-2 focus:ring-primary outline-none transition-all">
                    <option value="aktif" <?= $user['status'] === 'aktif' ? 'selected' : '' ?>>Aktif</option>
                    <option value="tidak_aktif" <?= $user['status'] === 'tidak_aktif' ? 'selected' : '' ?>>Nonaktif</option>
                </select>
            </div>
        </div>

        <div class="pt-4 flex justify-end gap-4">
            <a href="<?= base_url('admin/users/index.php') ?>" class="px-8 py-3 rounded-xl font-bold text-on-surface-variant hover:bg-surface-container-low transition-all">Batal</a>
            <button type="submit" class="bg-primary text-white px-10 py-3 rounded-xl font-bold hover:bg-primary-container transition-all shadow-lg active:scale-95">
                Simpan Perubahan
            </button>
        </div>
    </form>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
