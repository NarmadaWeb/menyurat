<?php if (is_logged_in()): ?>
<aside class="h-screen w-64 fixed left-0 top-0 bg-surface-container-low border-r border-outline-variant shadow-sm flex flex-col py-4 z-20">
    <div class="px-6 mb-8">
        <h1 class="text-xl text-primary font-bold">Admin Panel</h1>
        <p class="text-sm text-on-surface-variant font-medium"><?= get_village_profile()['nama'] ?></p>
    </div>
    <nav class="flex-1 space-y-1 overflow-y-auto">
        <a class="flex items-center gap-3 px-4 py-3 text-on-surface-variant hover:bg-surface-container-high transition-all" href="<?= base_url('admin/index.php') ?>">
            <span class="material-symbols-outlined">dashboard</span>
            Dashboard
        </a>
        <a class="flex items-center gap-3 px-4 py-3 text-on-surface-variant hover:bg-surface-container-high transition-all" href="<?= base_url('admin/surat-masuk/index.php') ?>">
            <span class="material-symbols-outlined">mail</span>
            Surat Masuk
        </a>
        <a class="flex items-center gap-3 px-4 py-3 text-on-surface-variant hover:bg-surface-container-high transition-all" href="<?= base_url('admin/surat-keluar/index.php') ?>">
            <span class="material-symbols-outlined">send</span>
            Surat Keluar
        </a>
        <a class="flex items-center gap-3 px-4 py-3 text-on-surface-variant hover:bg-surface-container-high transition-all" href="<?= base_url('admin/pengajuan/index.php') ?>">
            <span class="material-symbols-outlined">groups</span>
            Pengajuan Warga
        </a>
        <div class="pt-4 pb-2 px-6 opacity-50 text-xs uppercase tracking-wider">Master Data</div>
        <a class="flex items-center gap-3 px-4 py-3 text-on-surface-variant hover:bg-surface-container-high transition-all" href="<?= base_url('admin/master/jenis-surat.php') ?>">
            <span class="material-symbols-outlined">description</span>
            Jenis Surat
        </a>
        <a class="flex items-center gap-3 px-4 py-3 text-on-surface-variant hover:bg-surface-container-high transition-all" href="<?= base_url('admin/users/index.php') ?>">
            <span class="material-symbols-outlined">person</span>
            Manajemen User
        </a>
        <a class="flex items-center gap-3 px-4 py-3 text-on-surface-variant hover:bg-surface-container-high transition-all" href="<?= base_url('admin/settings/desa.php') ?>">
            <span class="material-symbols-outlined">settings</span>
            Pengaturan Desa
        </a>
        <a class="flex items-center gap-3 px-4 py-3 text-on-surface-variant hover:bg-surface-container-high transition-all" href="<?= base_url('admin/profil.php') ?>">
            <span class="material-symbols-outlined">account_circle</span>
            Profil Saya
        </a>
    </nav>
    <div class="px-4 py-4 border-t border-outline-variant">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-full bg-primary flex items-center justify-center text-white font-bold">
                <?= strtoupper(substr($_SESSION['full_name'], 0, 1)) ?>
            </div>
            <div class="overflow-hidden">
                <p class="text-sm font-semibold truncate"><?= $_SESSION['full_name'] ?></p>
                <a href="<?= base_url('logout.php') ?>" class="text-xs text-error hover:underline">Keluar</a>
            </div>
        </div>
    </div>
</aside>
<main class="ml-64 min-h-screen flex flex-col">
    <header class="w-full sticky top-0 z-10 bg-white border-b border-outline-variant flex justify-between items-center h-16 px-6">
        <h2 class="text-xl font-bold text-primary"><?= $page_title ?? 'Dashboard' ?></h2>
        <div class="flex items-center gap-4">
            <span class="text-sm text-on-surface-variant"><?= format_date(date('Y-m-d')) ?></span>
        </div>
    </header>
    <div class="p-6 flex-1">
        <?php $flash = get_flash(); if ($flash): ?>
            <div class="mb-6 p-4 rounded-lg <?= $flash['type'] === 'success' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' ?>">
                <?= $flash['message'] ?>
            </div>
        <?php endif; ?>
<?php endif; ?>
