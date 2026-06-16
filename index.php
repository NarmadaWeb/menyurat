<?php
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/sidebar.php';
?>

<section class="relative h-[500px] flex items-center overflow-hidden rounded-3xl mx-6 mt-6">
    <div class="absolute inset-0 z-0">
        <img class="w-full h-full object-cover" src="https://images.unsplash.com/photo-1516450360452-9312f5e86fc7?auto=format&fit=crop&q=80&w=1920" alt="Village Hero"/>
        <div class="absolute inset-0 bg-gradient-to-r from-black/70 to-transparent"></div>
    </div>
    <div class="relative z-10 px-12 w-full">
        <div class="max-w-2xl text-white">
            <h1 class="text-5xl font-bold mb-6 leading-tight">Selamat Datang di E-Office <?= $village['name'] ?></h1>
            <p class="text-xl mb-8 opacity-90">Transformasi digital pelayanan publik untuk masyarakat yang lebih transparan, efisien, dan mudah diakses.</p>
            <div class="flex gap-4">
                <a href="warga/pengajuan.php" class="bg-primary text-white px-8 py-3 rounded-xl font-semibold hover:bg-primary-container transition-all shadow-lg">
                    Mulai Pengajuan
                </a>
                <a href="warga/status.php" class="bg-white/20 backdrop-blur-md border border-white/30 text-white px-8 py-3 rounded-xl font-semibold hover:bg-white/30 transition-all">
                    Cek Status
                </a>
            </div>
        </div>
    </div>
</section>

<section class="py-16 max-w-7xl mx-auto px-6">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        <div class="bg-white p-8 rounded-2xl shadow-sm border border-outline-variant hover:shadow-md transition-all">
            <div class="w-16 h-16 bg-primary/10 rounded-full flex items-center justify-center mb-6 text-primary">
                <span class="material-symbols-outlined text-3xl">description</span>
            </div>
            <h3 class="text-xl font-bold mb-3">Pengajuan Online</h3>
            <p class="text-on-surface-variant mb-6 text-sm">Urus berbagai keperluan administrasi tanpa harus datang ke kantor desa.</p>
            <a href="warga/pengajuan.php" class="text-primary font-bold inline-flex items-center gap-2 hover:underline">
                Buat Pengajuan <span class="material-symbols-outlined text-sm">arrow_forward</span>
            </a>
        </div>
        <div class="bg-white p-8 rounded-2xl shadow-sm border border-outline-variant hover:shadow-md transition-all">
            <div class="w-16 h-16 bg-secondary/10 rounded-full flex items-center justify-center mb-6 text-secondary">
                <span class="material-symbols-outlined text-3xl">search</span>
            </div>
            <h3 class="text-xl font-bold mb-3">Lacak Berkas</h3>
            <p class="text-on-surface-variant mb-6 text-sm">Pantau perkembangan berkas Anda secara real-time dengan kode registrasi.</p>
            <a href="warga/status.php" class="text-primary font-bold inline-flex items-center gap-2 hover:underline">
                Periksa Status <span class="material-symbols-outlined text-sm">arrow_forward</span>
            </a>
        </div>
        <div class="bg-white p-8 rounded-2xl shadow-sm border border-outline-variant hover:shadow-md transition-all">
            <div class="w-16 h-16 bg-primary/10 rounded-full flex items-center justify-center mb-6 text-primary">
                <span class="material-symbols-outlined text-3xl">info</span>
            </div>
            <h3 class="text-xl font-bold mb-3">Informasi Layanan</h3>
            <p class="text-on-surface-variant mb-6 text-sm">Informasi lengkap mengenai jenis layanan, persyaratan, dan alur prosedur.</p>
            <a href="#" class="text-primary font-bold inline-flex items-center gap-2 hover:underline">
                Lihat Panduan <span class="material-symbols-outlined text-sm">arrow_forward</span>
            </a>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
