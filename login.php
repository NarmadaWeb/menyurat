<?php
require_once __DIR__ . '/includes/auth.php';

if (is_logged_in()) {
    redirect(base_url('admin/index.php'));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = sanitize($_POST['username']);
    $password = $_POST['password'];

    if (authenticate($username, $password)) {
        redirect(base_url('admin/index.php'), 'Selamat datang kembali, ' . $_SESSION['full_name']);
    } else {
        $error = "Username atau password yang Anda masukkan salah.";
    }
}

$page_title = "Login Petugas Desa";
require_once __DIR__ . '/includes/header.php';
?>

<div class="min-h-[80vh] flex items-center justify-center px-4 sm:px-6 py-12">
    <div class="max-w-md w-full bg-white rounded-3xl shadow-xl border border-outline-variant p-6 sm:p-10">
        <div class="text-center mb-8">
            <div class="w-16 h-16 bg-primary/10 text-primary rounded-2xl flex items-center justify-center mx-auto mb-4">
                <span class="material-symbols-outlined text-3xl">admin_panel_settings</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-on-surface mb-1">Masuk Panel Admin</h1>
            <p class="text-xs sm:text-sm text-on-surface-variant font-medium">E-Office Pelayanan <?= htmlspecialchars($village['nama']) ?></p>
        </div>

        <?php if (isset($error)): ?>
            <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-800 rounded-xl text-xs sm:text-sm font-semibold flex items-center gap-2">
                <span class="material-symbols-outlined text-base">error</span> <?= $error ?>
            </div>
        <?php endif; ?>

        <form action="" method="POST" class="space-y-5">
            <div>
                <label for="username" class="block text-xs font-bold uppercase text-on-surface-variant mb-1.5">Username</label>
                <div class="relative">
                    <span class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-on-surface-variant text-lg">person</span>
                    <input type="text" name="username" id="username" required placeholder="Masukkan username"
                        class="w-full pl-11 pr-4 py-3 border border-outline-variant rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition-all text-sm outline-none">
                </div>
            </div>
            <div>
                <label for="password" class="block text-xs font-bold uppercase text-on-surface-variant mb-1.5">Password</label>
                <div class="relative">
                    <span class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-on-surface-variant text-lg">lock</span>
                    <input type="password" name="password" id="password" required placeholder="Masukkan password"
                        class="w-full pl-11 pr-4 py-3 border border-outline-variant rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition-all text-sm outline-none">
                </div>
            </div>
            <button type="submit" class="w-full bg-primary hover:bg-primary-container text-white py-3.5 rounded-xl font-bold text-sm sm:text-base transition-all shadow-lg shadow-primary/20 active:scale-[0.99] flex items-center justify-center gap-2">
                <span>Masuk Sekarang</span>
                <span class="material-symbols-outlined text-base">arrow_forward</span>
            </button>
        </form>

        <div class="mt-8 pt-6 border-t border-outline-variant text-center">
            <a href="<?= base_url() ?>" class="text-xs sm:text-sm font-semibold text-primary hover:underline inline-flex items-center gap-1.5">
                <span class="material-symbols-outlined text-base">arrow_back</span> Kembali ke Beranda
            </a>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
