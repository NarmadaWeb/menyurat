<?php
$page_title = "Login";
require_once __DIR__ . '/includes/header.php';
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
        $error = "Username atau password salah.";
    }
}
?>

<div class="min-h-[80vh] flex items-center justify-center px-6">
    <div class="max-w-md w-full bg-white rounded-2xl shadow-xl border border-outline-variant p-8">
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-primary mb-2">Login Admin</h1>
            <p class="text-on-surface-variant">E-Office Desa Pendua</p>
        </div>

        <?php if (isset($error)): ?>
            <div class="mb-6 p-4 bg-red-100 text-red-800 rounded-lg text-sm">
                <?= $error ?>
            </div>
        <?php endif; ?>

        <form action="" method="POST" class="space-y-6">
            <div>
                <label for="username" class="block text-sm font-semibold mb-2">Username</label>
                <input type="text" name="username" id="username" required
                    class="w-full px-4 py-3 border border-outline-variant rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition-all">
            </div>
            <div>
                <label for="password" class="block text-sm font-semibold mb-2">Password</label>
                <input type="password" name="password" id="password" required
                    class="w-full px-4 py-3 border border-outline-variant rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition-all">
            </div>
            <button type="submit" class="w-full bg-primary text-white py-3 rounded-xl font-bold hover:bg-primary-container transition-all shadow-lg active:scale-[0.98]">
                Masuk
            </button>
        </form>

        <div class="mt-8 text-center">
            <a href="<?= base_url() ?>" class="text-sm text-primary hover:underline inline-flex items-center gap-2">
                <span class="material-symbols-outlined text-sm">arrow_back</span> Kembali ke Beranda
            </a>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
