<?php if (is_logged_in()): ?>
<!-- Backdrop for mobile sidebar -->
<div id="sidebar-backdrop" onclick="toggleAdminSidebar()" class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-30 hidden md:hidden transition-opacity"></div>

<aside id="admin-sidebar" class="h-screen w-64 fixed left-0 top-0 bg-surface-container-low border-r border-outline-variant shadow-lg md:shadow-none flex flex-col py-4 z-40 transform -translate-x-full md:translate-x-0 transition-transform duration-300 ease-in-out">
    <div class="px-6 mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-xl text-primary font-bold flex items-center gap-2">
                <span class="material-symbols-outlined text-2xl">local_post_office</span> E-Office
            </h1>
            <p class="text-xs text-on-surface-variant font-medium"><?= htmlspecialchars(get_village_profile()['nama']) ?></p>
        </div>
        <button type="button" onclick="toggleAdminSidebar()" class="md:hidden text-on-surface-variant hover:text-on-surface">
            <span class="material-symbols-outlined">close</span>
        </button>
    </div>

    <nav class="flex-1 space-y-1 overflow-y-auto px-3">
        <a class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium text-sm text-on-surface-variant hover:bg-surface-container-high hover:text-primary transition-all" href="<?= base_url('admin/index.php') ?>">
            <span class="material-symbols-outlined text-xl">dashboard</span>
            Dashboard
        </a>
        <a class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium text-sm text-on-surface-variant hover:bg-surface-container-high hover:text-primary transition-all" href="<?= base_url('admin/surat-masuk/index.php') ?>">
            <span class="material-symbols-outlined text-xl">mail</span>
            Surat Masuk
        </a>
        <a class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium text-sm text-on-surface-variant hover:bg-surface-container-high hover:text-primary transition-all" href="<?= base_url('admin/surat-keluar/index.php') ?>">
            <span class="material-symbols-outlined text-xl">send</span>
            Surat Keluar
        </a>
        <a class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium text-sm text-on-surface-variant hover:bg-surface-container-high hover:text-primary transition-all" href="<?= base_url('admin/pengajuan/index.php') ?>">
            <span class="material-symbols-outlined text-xl">inbox</span>
            Pengajuan Warga
        </a>

        <div class="pt-4 pb-2 px-3 text-[11px] font-bold text-on-surface-variant/70 uppercase tracking-wider">Kependudukan & Surat</div>
        <a class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium text-sm text-on-surface-variant hover:bg-surface-container-high hover:text-primary transition-all" href="<?= base_url('admin/penduduk/index.php') ?>">
            <span class="material-symbols-outlined text-xl">groups</span>
            Data Penduduk
        </a>
        <a class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium text-sm text-on-surface-variant hover:bg-surface-container-high hover:text-primary transition-all" href="<?= base_url('admin/surat/buat.php') ?>">
            <span class="material-symbols-outlined text-xl">post_add</span>
            Template Surat
        </a>

        <div class="pt-4 pb-2 px-3 text-[11px] font-bold text-on-surface-variant/70 uppercase tracking-wider">Master Data</div>
        <a class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium text-sm text-on-surface-variant hover:bg-surface-container-high hover:text-primary transition-all" href="<?= base_url('admin/master/jenis-surat.php') ?>">
            <span class="material-symbols-outlined text-xl">description</span>
            Jenis Surat
        </a>
        <a class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium text-sm text-on-surface-variant hover:bg-surface-container-high hover:text-primary transition-all" href="<?= base_url('admin/users/index.php') ?>">
            <span class="material-symbols-outlined text-xl">manage_accounts</span>
            Manajemen User
        </a>
        <a class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium text-sm text-on-surface-variant hover:bg-surface-container-high hover:text-primary transition-all" href="<?= base_url('admin/settings/desa.php') ?>">
            <span class="material-symbols-outlined text-xl">settings</span>
            Pengaturan Desa
        </a>
        <a class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium text-sm text-on-surface-variant hover:bg-surface-container-high hover:text-primary transition-all" href="<?= base_url('admin/profil.php') ?>">
            <span class="material-symbols-outlined text-xl">account_circle</span>
            Profil Saya
        </a>
    </nav>
    <div class="px-4 py-4 border-t border-outline-variant bg-white/40">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-full bg-primary flex items-center justify-center text-white font-bold shrink-0">
                <?= strtoupper(substr($_SESSION['full_name'] ?? 'A', 0, 1)) ?>
            </div>
            <div class="overflow-hidden flex-1">
                <p class="text-sm font-semibold truncate text-on-surface"><?= htmlspecialchars($_SESSION['full_name'] ?? 'User') ?></p>
                <a href="<?= base_url('logout.php') ?>" class="text-xs text-error hover:underline flex items-center gap-1">
                    <span class="material-symbols-outlined text-xs">logout</span> Keluar
                </a>
            </div>
        </div>
    </div>
</aside>

<main class="ml-0 md:ml-64 min-h-screen flex flex-col transition-all">
    <header class="w-full sticky top-0 z-20 bg-white/95 backdrop-blur-md border-b border-outline-variant flex justify-between items-center h-16 px-4 sm:px-6">
        <div class="flex items-center gap-3">
            <button type="button" onclick="toggleAdminSidebar()" class="md:hidden p-2 rounded-xl text-on-surface-variant hover:bg-surface-container-low transition-colors">
                <span class="material-symbols-outlined text-2xl">menu</span>
            </button>
            <h2 class="text-lg sm:text-xl font-bold text-primary truncate"><?= $page_title ?? 'Dashboard' ?></h2>
        </div>
        <div class="flex items-center gap-4">
            <span class="hidden sm:inline-block text-xs font-semibold px-3 py-1.5 rounded-full bg-surface-container-low text-on-surface-variant border border-outline-variant">
                📅 <?= format_date(date('Y-m-d')) ?>
            </span>
            <a href="<?= base_url() ?>" target="_blank" class="text-xs font-bold text-primary hover:underline flex items-center gap-1" title="Lihat Website">
                <span class="material-symbols-outlined text-sm">public</span> Web Desa
            </a>
        </div>
    </header>
    <div class="p-4 sm:p-6 lg:p-8 flex-1">
        <?php $flash = get_flash(); if ($flash): ?>
            <div class="mb-6 p-4 rounded-xl shadow-sm border <?= $flash['type'] === 'success' ? 'bg-emerald-50 text-emerald-800 border-emerald-200' : 'bg-red-50 text-red-800 border-red-200' ?> flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <span class="material-symbols-outlined text-lg"><?= $flash['type'] === 'success' ? 'check_circle' : 'error' ?></span>
                    <span class="text-sm font-semibold"><?= $flash['message'] ?></span>
                </div>
            </div>
        <?php endif; ?>

<script>
function toggleAdminSidebar() {
    const sidebar = document.getElementById('admin-sidebar');
    const backdrop = document.getElementById('sidebar-backdrop');
    if (sidebar.classList.contains('-translate-x-full')) {
        sidebar.classList.remove('-translate-x-full');
        backdrop.classList.remove('hidden');
    } else {
        sidebar.classList.add('-translate-x-full');
        backdrop.classList.add('hidden');
    }
}
</script>
<?php endif; ?>
