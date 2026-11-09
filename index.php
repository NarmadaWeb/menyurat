<?php
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/sidebar.php';
$stats_penduduk = $pdo->query("SELECT COUNT(*) FROM penduduk")->fetchColumn();
$stats_surat_selesai = $pdo->query("SELECT COUNT(*) FROM pengajuan_warga WHERE status = 'selesai'")->fetchColumn();
?>

<!-- Hero Section -->
<section class="relative min-h-[460px] sm:min-h-[500px] flex items-center overflow-hidden rounded-3xl mx-4 sm:mx-8 mt-4 sm:mt-6 shadow-2xl">
    <div class="absolute inset-0 z-0">
        <img class="w-full h-full object-cover" src="https://imgs.search.brave.com/j_UJV7F0AZSctFi4H3Uer44_uU-pFWH7L8eitvNu0NU/rs:fit:860:0:0:0/g:ce/aHR0cHM6Ly9iYW5r/dGFuYWguaWQvd3At/Y29udGVudC91cGxv/YWRzLzIwMjQvMDQv/bG9tYm9rdXRhcmEt/U3Vycm91bmRpbmct/SG90ZWwtRGV2ZWxv/cG1lbnQtMy02NDB4/NDI3LnBuZw" alt="Village Hero"/>
        <div class="absolute inset-0 bg-gradient-to-r from-slate-950/90 via-slate-900/70 to-transparent"></div>
    </div>
    <div class="relative z-10 px-6 sm:px-12 lg:px-16 py-12 w-full">
        <div class="max-w-2xl text-white">
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-white/20 backdrop-blur-md border border-white/20 text-xs font-semibold uppercase tracking-wider mb-4">
                <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span> Pelayanan Publik Desa Online
            </span>
            <h1 class="text-3xl sm:text-5xl lg:text-6xl font-extrabold mb-4 sm:mb-6 leading-tight tracking-tight">
                Selamat Datang di <span class="text-emerald-400">E-Office</span> <?= htmlspecialchars($village['nama']) ?>
            </h1>
            <p class="text-base sm:text-lg mb-8 opacity-90 leading-relaxed font-normal">
                Permudah urusan persuratan dan administrasi kependudukan Anda secara online, transparan, cepat, dan tanpa antre.
            </p>
            <div class="flex flex-col sm:flex-row gap-3 sm:gap-4">
                <a href="warga/pengajuan.php" class="bg-primary hover:bg-primary-container text-white px-7 py-3.5 rounded-xl font-bold transition-all shadow-lg shadow-primary/30 flex items-center justify-center gap-2 group">
                    <span>Mulai Buat Pengajuan</span>
                    <span class="material-symbols-outlined text-sm group-hover:translate-x-1 transition-transform">arrow_forward</span>
                </a>
                <a href="warga/status.php" class="bg-white/10 hover:bg-white/20 backdrop-blur-md border border-white/30 text-white px-7 py-3.5 rounded-xl font-bold transition-all flex items-center justify-center gap-2">
                    <span class="material-symbols-outlined text-sm">search</span> Cek Status Berkas
                </a>
            </div>
        </div>
    </div>
</section>

<!-- Quick Stats Bar -->
<section class="max-w-7xl mx-auto px-4 sm:px-6 -mt-8 relative z-20">
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 bg-white p-5 rounded-2xl shadow-xl border border-outline-variant">
        <div class="text-center p-3 border-r border-outline-variant/60">
            <p class="text-2xl sm:text-3xl font-extrabold text-primary"><?= $stats_penduduk ?: '3.800+' ?></p>
            <p class="text-xs text-on-surface-variant font-semibold uppercase tracking-wider mt-1">Total Penduduk</p>
        </div>
        <div class="text-center p-3 md:border-r border-outline-variant/60">
            <p class="text-2xl sm:text-3xl font-extrabold text-primary">10+</p>
            <p class="text-xs text-on-surface-variant font-semibold uppercase tracking-wider mt-1">Jenis Layanan Surat</p>
        </div>
        <div class="text-center p-3 border-r border-outline-variant/60">
            <p class="text-2xl sm:text-3xl font-extrabold text-primary"><?= $stats_surat_selesai ?: '150+' ?></p>
            <p class="text-xs text-on-surface-variant font-semibold uppercase tracking-wider mt-1">Surat Diterbitkan</p>
        </div>
        <div class="text-center p-3">
            <p class="text-2xl sm:text-3xl font-extrabold text-primary">100%</p>
            <p class="text-xs text-on-surface-variant font-semibold uppercase tracking-wider mt-1">Layanan Gratis</p>
        </div>
    </div>
</section>

<!-- Service Feature Cards -->
<section class="py-16 sm:py-20 max-w-7xl mx-auto px-4 sm:px-6">
    <div class="text-center max-w-2xl mx-auto mb-12 sm:mb-16">
        <h2 class="text-2xl sm:text-3xl font-bold text-on-surface mb-3">Layanan Mandiri Masyarakat</h2>
        <p class="text-on-surface-variant text-sm sm:text-base">Akses pelayanan administrasi desa secara digital tanpa perlu antre di kantor desa.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 sm:gap-8">
        <div class="bg-white p-8 rounded-3xl shadow-sm border border-outline-variant hover:shadow-xl hover:border-primary/40 transition-all group flex flex-col justify-between">
            <div>
                <div class="w-16 h-16 bg-primary/10 rounded-2xl flex items-center justify-center mb-6 text-primary group-hover:bg-primary group-hover:text-white transition-all shadow-sm">
                    <span class="material-symbols-outlined text-3xl">edit_document</span>
                </div>
                <h3 class="text-xl font-bold mb-3 text-on-surface">Pengajuan Online</h3>
                <p class="text-on-surface-variant mb-6 text-sm leading-relaxed">Ajukan surat pengantar, surat domisili, surat keterangan usaha, kematian, hingga pengantar SKCK langsung dari rumah Anda.</p>
            </div>
            <a href="warga/pengajuan.php" class="text-primary font-bold inline-flex items-center gap-2 group-hover:gap-3 transition-all text-sm">
                Ajukan Sekarang <span class="material-symbols-outlined text-sm">arrow_forward</span>
            </a>
        </div>

        <div class="bg-white p-8 rounded-3xl shadow-sm border border-outline-variant hover:shadow-xl hover:border-secondary/40 transition-all group flex flex-col justify-between">
            <div>
                <div class="w-16 h-16 bg-secondary/10 rounded-2xl flex items-center justify-center mb-6 text-secondary group-hover:bg-secondary group-hover:text-white transition-all shadow-sm">
                    <span class="material-symbols-outlined text-3xl">search_check</span>
                </div>
                <h3 class="text-xl font-bold mb-3 text-on-surface">Lacak Permohonan</h3>
                <p class="text-on-surface-variant mb-6 text-sm leading-relaxed">Cek posisi dan status permohonan Anda secara real-time cukup dengan memasukkan nomor registrasi surat.</p>
            </div>
            <a href="warga/status.php" class="text-secondary font-bold inline-flex items-center gap-2 group-hover:gap-3 transition-all text-sm">
                Periksa Status <span class="material-symbols-outlined text-sm">arrow_forward</span>
            </a>
        </div>

        <div class="bg-white p-8 rounded-3xl shadow-sm border border-outline-variant hover:shadow-xl hover:border-emerald-500/40 transition-all group flex flex-col justify-between">
            <div>
                <div class="w-16 h-16 bg-emerald-50 rounded-2xl flex items-center justify-center mb-6 text-emerald-700 group-hover:bg-emerald-700 group-hover:text-white transition-all shadow-sm">
                    <span class="material-symbols-outlined text-3xl">verified</span>
                </div>
                <h3 class="text-xl font-bold mb-3 text-on-surface">Cepat & Resmi</h3>
                <p class="text-on-surface-variant mb-6 text-sm leading-relaxed">Surat yang diterbitkan memiliki nomor surat sah dari Desa <?= htmlspecialchars($village['nama']) ?> dan ditandatangani langsung oleh Kepala Desa.</p>
            </div>
            <a href="warga/pengajuan.php" class="text-emerald-700 font-bold inline-flex items-center gap-2 group-hover:gap-3 transition-all text-sm">
                Lihat Persyaratan <span class="material-symbols-outlined text-sm">arrow_forward</span>
            </a>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
