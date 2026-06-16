<?php
require_once __DIR__ . '/functions.php';
$village = get_village_profile();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title><?= isset($page_title) ? $page_title . ' - ' : '' ?>E-Office <?= $village['name'] ?></title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet"/>
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
              "surface": "#f8f9ff",
              "surface-container-low": "#eff4ff",
              "surface-container-high": "#dce9ff",
              "outline-variant": "#bccac0",
              "on-surface": "#0b1c30",
              "on-surface-variant": "#3d4a42",
              "error": "#ba1a1a",
              "secondary": "#795900"
            }
          }
        }
      }
    </script>
    <style>
        body { font-family: 'Inter', sans-serif; }
        .material-symbols-outlined { font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24; }
    </style>
</head>
<body class="bg-background text-on-surface">
<?php if (is_logged_in()): ?>
<?php else: ?>
    <header class="w-full top-0 sticky z-50 bg-white border-b border-outline-variant">
        <div class="flex justify-between items-center h-16 px-6 max-w-7xl mx-auto">
            <div class="font-bold text-primary text-xl">
                E-Office <?= $village['name'] ?>
            </div>
            <nav class="hidden md:flex items-center gap-8">
                <a class="hover:text-primary transition-colors" href="<?= base_url() ?>">Home</a>
                <a class="hover:text-primary transition-colors" href="<?= base_url('warga/pengajuan.php') ?>">Layanan</a>
                <a class="hover:text-primary transition-colors" href="<?= base_url('warga/status.php') ?>">Cek Status</a>
            </nav>
            <div>
                <a href="<?= base_url('login.php') ?>" class="bg-primary text-white px-6 py-2 rounded-lg font-medium hover:bg-primary-container transition-all">
                    Login
                </a>
            </div>
        </div>
    </header>
<?php endif; ?>
