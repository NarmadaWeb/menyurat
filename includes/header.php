<?php
require_once __DIR__ . '/functions.php';
$village = get_village_profile();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title><?= isset($page_title) ? $page_title . ' - ' : '' ?>E-Office <?= htmlspecialchars($village['nama']) ?></title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
    <script>
      tailwind.config = {
        theme: {
          extend: {
            colors: {
              "primary": "#006948",
              "primary-container": "#00855d",
              "on-primary-container": "#f5fff7",
              "background": "#f8f9ff",
              "surface": "#ffffff",
              "surface-container-low": "#f2f5f9",
              "surface-container-high": "#e4ecf5",
              "outline-variant": "#d3dfd8",
              "on-surface": "#0f172a",
              "on-surface-variant": "#475569",
              "error": "#dc2626",
              "secondary": "#b45309"
            },
            fontFamily: {
              sans: ['Plus Jakarta Sans', 'Inter', 'sans-serif'],
            }
          }
        }
      }
    </script>
    <style>
        body { font-family: 'Plus Jakarta Sans', 'Inter', sans-serif; -webkit-tap-highlight-color: transparent; }
        .material-symbols-outlined { font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24; }
        @media print {
            .no-print { display: none !important; }
        }
    </style>
</head>
<body class="bg-background text-on-surface antialiased selection:bg-primary/20 selection:text-primary min-h-screen flex flex-col">
<?php if (!is_logged_in()): ?>
    <!-- Public Header -->
    <header class="w-full top-0 sticky z-50 bg-white/95 backdrop-blur-md border-b border-outline-variant shadow-sm transition-all">
        <div class="flex justify-between items-center h-16 sm:h-20 px-4 sm:px-6 max-w-7xl mx-auto">
            <a href="<?= base_url() ?>" class="flex items-center gap-3">
                <?php if (!empty($village['logo'])): ?>
                    <img src="<?= base_url($village['logo']) ?>" alt="Logo" class="w-10 h-10 object-contain rounded-lg">
                <?php else: ?>
                    <div class="w-10 h-10 rounded-xl bg-primary/10 text-primary flex items-center justify-center font-bold">
                        <span class="material-symbols-outlined text-2xl">account_balance</span>
                    </div>
                <?php endif; ?>
                <div>
                    <span class="font-bold text-primary text-lg sm:text-xl block leading-tight">E-Office</span>
                    <span class="text-xs text-on-surface-variant font-medium"><?= htmlspecialchars($village['nama']) ?></span>
                </div>
            </a>

            <nav class="hidden md:flex items-center gap-8">
                <a class="font-medium text-sm text-on-surface hover:text-primary transition-colors flex items-center gap-1.5" href="<?= base_url() ?>">
                    <span class="material-symbols-outlined text-lg">home</span> Beranda
                </a>
                <a class="font-medium text-sm text-on-surface hover:text-primary transition-colors flex items-center gap-1.5" href="<?= base_url('warga/pengajuan.php') ?>">
                    <span class="material-symbols-outlined text-lg">post_add</span> Layanan Surat
                </a>
                <a class="font-medium text-sm text-on-surface hover:text-primary transition-colors flex items-center gap-1.5" href="<?= base_url('warga/status.php') ?>">
                    <span class="material-symbols-outlined text-lg">track_changes</span> Cek Status
                </a>
            </nav>

            <div class="flex items-center gap-3">
                <a href="<?= base_url('login.php') ?>" class="hidden sm:inline-flex items-center gap-1.5 bg-primary text-white px-5 py-2.5 rounded-xl font-bold text-sm hover:bg-primary-container transition-all shadow-sm">
                    <span class="material-symbols-outlined text-sm">lock</span> Login Admin
                </a>
                <!-- Mobile hamburger button -->
                <button type="button" onclick="togglePublicMobileMenu()" class="md:hidden p-2 rounded-xl text-on-surface-variant hover:bg-surface-container-low transition-colors">
                    <span class="material-symbols-outlined text-2xl" id="public-menu-icon">menu</span>
                </button>
            </div>
        </div>

        <!-- Mobile dropdown menu -->
        <div id="public-mobile-menu" class="hidden md:hidden border-t border-outline-variant bg-white px-6 py-4 space-y-3">
            <a class="block py-2 text-sm font-semibold text-on-surface hover:text-primary" href="<?= base_url() ?>">Beranda</a>
            <a class="block py-2 text-sm font-semibold text-on-surface hover:text-primary" href="<?= base_url('warga/pengajuan.php') ?>">Layanan Surat</a>
            <a class="block py-2 text-sm font-semibold text-on-surface hover:text-primary" href="<?= base_url('warga/status.php') ?>">Cek Status</a>
            <a class="block py-2 text-sm font-bold text-primary" href="<?= base_url('login.php') ?>">Login Admin &rarr;</a>
        </div>
    </header>

    <script>
    function togglePublicMobileMenu() {
        const menu = document.getElementById('public-mobile-menu');
        const icon = document.getElementById('public-menu-icon');
        if (menu.classList.contains('hidden')) {
            menu.classList.remove('hidden');
            icon.textContent = 'close';
        } else {
            menu.classList.add('hidden');
            icon.textContent = 'menu';
        }
    }
    </script>
<?php endif; ?>
