<?php
$page_title = "Profil Saya";
require_once __DIR__ . '/../includes/auth.php';
require_login();

$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT u.*, r.nama as role_name FROM pengguna u JOIN peran r ON u.peran_id = r.id WHERE u.id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_profile'])) {
        $full_name = sanitize($_POST['full_name']);
        $email = sanitize($_POST['email']);

        $stmt = $pdo->prepare("UPDATE pengguna SET nama_lengkap = ?, email = ? WHERE id = ?");
        $stmt->execute([$full_name, $email, $user_id]);
        $_SESSION['full_name'] = $full_name;
        redirect(base_url('admin/profil.php'), 'Profil berhasil diperbarui.');
    } elseif (isset($_POST['change_password'])) {
        $old_pass = $_POST['old_password'];
        $new_pass = $_POST['new_password'];
        $confirm_pass = $_POST['confirm_password'];

        if (password_verify($old_pass, $user['password'])) {
            if ($new_pass === $confirm_pass) {
                $hashed = password_hash($new_pass, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("UPDATE pengguna SET password = ? WHERE id = ?");
                $stmt->execute([$hashed, $user_id]);
                redirect(base_url('admin/profil.php'), 'Password berhasil diubah.');
            } else {
                $error = "Konfirmasi password baru tidak cocok.";
            }
        } else {
            $error = "Password lama salah.";
        }
    }
}

require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/sidebar.php';
?>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
    <!-- Profile Info -->
    <div class="bg-white rounded-2xl border border-outline-variant p-8 shadow-sm">
        <h3 class="text-xl font-bold mb-6 flex items-center gap-2">
            <span class="material-symbols-outlined text-primary">person</span> Informasi Profil
        </h3>
        <form action="" method="POST" class="space-y-4">
            <div>
                <label class="block text-sm font-semibold mb-2">Username</label>
                <input type="text" value="<?= $user['username'] ?>" disabled class="w-full px-4 py-2 bg-gray-50 border rounded-xl cursor-not-allowed">
            </div>
            <div>
                <label class="block text-sm font-semibold mb-2">Role</label>
                <input type="text" value="<?= $user['role_name'] ?>" disabled class="w-full px-4 py-2 bg-gray-50 border rounded-xl cursor-not-allowed">
            </div>
            <div>
                <label for="full_name" class="block text-sm font-semibold mb-2">Nama Lengkap</label>
                <input type="text" name="full_name" id="full_name" value="<?= $user['nama_lengkap'] ?>" required class="w-full px-4 py-2 border rounded-xl focus:ring-2 focus:ring-primary outline-none">
            </div>
            <div>
                <label for="email" class="block text-sm font-semibold mb-2">Email</label>
                <input type="email" name="email" id="email" value="<?= $user['email'] ?>" class="w-full px-4 py-2 border rounded-xl focus:ring-2 focus:ring-primary outline-none">
            </div>
            <button type="submit" name="update_profile" class="bg-primary text-white px-8 py-2 rounded-xl font-bold hover:bg-primary-container transition-all">Simpan Perubahan</button>
        </form>
    </div>

    <!-- Change Password -->
    <div class="bg-white rounded-2xl border border-outline-variant p-8 shadow-sm">
        <h3 class="text-xl font-bold mb-6 flex items-center gap-2">
            <span class="material-symbols-outlined text-primary">lock</span> Ganti Password
        </h3>
        <?php if (isset($error)): ?>
            <div class="mb-4 p-3 bg-red-100 text-red-800 rounded-lg text-sm"><?= $error ?></div>
        <?php endif; ?>
        <form action="" method="POST" class="space-y-4">
            <div>
                <label for="old_password" class="block text-sm font-semibold mb-2">Password Lama</label>
                <input type="password" name="old_password" id="old_password" required class="w-full px-4 py-2 border rounded-xl focus:ring-2 focus:ring-primary outline-none">
            </div>
            <div>
                <label for="new_password" class="block text-sm font-semibold mb-2">Password Baru</label>
                <input type="password" name="new_password" id="new_password" required class="w-full px-4 py-2 border rounded-xl focus:ring-2 focus:ring-primary outline-none">
            </div>
            <div>
                <label for="confirm_password" class="block text-sm font-semibold mb-2">Konfirmasi Password Baru</label>
                <input type="password" name="confirm_password" id="confirm_password" required class="w-full px-4 py-2 border rounded-xl focus:ring-2 focus:ring-primary outline-none">
            </div>
            <button type="submit" name="change_password" class="bg-secondary text-white px-8 py-2 rounded-xl font-bold hover:opacity-90 transition-all">Ganti Password</button>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
