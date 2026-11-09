<?php
$page_title = "Cek Status Pengajuan";
require_once __DIR__ . '/../includes/header.php';

$request = null;
$error = null;

if (isset($_GET['code'])) {
    $code = sanitize($_GET['code']);
    $stmt = $pdo->prepare("SELECT r.*, mt.nama as mail_type_name FROM pengajuan_warga r LEFT JOIN jenis_surat mt ON r.jenis_surat_id = mt.id WHERE r.nomor_registrasi = ?");
    $stmt->execute([$code]);
    $request = $stmt->fetch();

    if (!$request) {
        $error = "Nomor registrasi '$code' tidak ditemukan. Pastikan nomor yang dimasukkan benar.";
    }
}
?>

<div class="max-w-4xl mx-auto py-8 sm:py-12 px-4 sm:px-6">
    <div class="mb-8 sm:mb-12 text-center">
        <h1 class="text-3xl sm:text-4xl font-extrabold text-on-surface mb-2">Lacak Status Pengajuan</h1>
        <p class="text-on-surface-variant text-sm sm:text-base">Ketahui perkembangan proses verifikasi surat permohonan Anda secara real-time.</p>
    </div>

    <div class="bg-white rounded-3xl p-6 sm:p-8 shadow-xl border border-outline-variant mb-8">
        <form action="" method="GET" class="flex flex-col sm:flex-row gap-3">
            <div class="flex-1 relative">
                <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-on-surface-variant">search</span>
                <input type="text" name="code" value="<?= isset($code) ? htmlspecialchars($code) : '' ?>" required placeholder="Contoh: REG-202502..."
                    class="w-full pl-12 pr-4 py-3.5 bg-surface-container-low border border-outline-variant rounded-2xl focus:ring-2 focus:ring-primary outline-none font-mono text-sm sm:text-base">
            </div>
            <button type="submit" class="bg-primary hover:bg-primary-container text-white px-8 py-3.5 rounded-2xl font-bold text-sm sm:text-base transition-all shadow-md flex items-center justify-center gap-1.5 shrink-0">
                <span class="material-symbols-outlined text-lg">track_changes</span> Lacak Berkas
            </button>
        </form>
    </div>

    <?php if ($error): ?>
        <div class="bg-red-50 border border-red-200 text-red-800 p-5 rounded-2xl text-center text-sm font-semibold mb-8 flex items-center justify-center gap-2">
            <span class="material-symbols-outlined">warning</span> <?= $error ?>
        </div>
    <?php endif; ?>

    <?php if ($request): ?>
        <div class="bg-white rounded-3xl shadow-xl border border-outline-variant p-6 sm:p-8 space-y-6">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-6 border-b border-outline-variant">
                <div>
                    <span class="text-xs font-bold uppercase tracking-wider text-on-surface-variant">Nomor Registrasi</span>
                    <h3 class="text-xl sm:text-2xl font-mono font-extrabold text-primary"><?= $request['nomor_registrasi'] ?></h3>
                </div>
                <div>
                    <?php
                    $status_badges = [
                        'baru' => 'bg-blue-100 text-blue-800 border-blue-200',
                        'diproses' => 'bg-amber-100 text-amber-800 border-amber-200',
                        'selesai' => 'bg-emerald-100 text-emerald-800 border-emerald-200',
                        'ditolak' => 'bg-red-100 text-red-800 border-red-200'
                    ];
                    $badge_class = $status_badges[$request['status']] ?? 'bg-slate-100 text-slate-800';
                    ?>
                    <span class="px-4 py-2 rounded-full text-xs font-bold uppercase border <?= $badge_class ?> inline-flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-current"></span> Status: <?= $request['status'] ?>
                    </span>
                </div>
            </div>

            <!-- Detail Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                <div class="p-4 rounded-xl bg-surface-container-low border border-outline-variant/60">
                    <p class="text-xs font-bold text-on-surface-variant uppercase">Nama Pemohon</p>
                    <p class="font-bold text-on-surface text-base mt-0.5"><?= htmlspecialchars($request['nama_lengkap']) ?></p>
                </div>
                <div class="p-4 rounded-xl bg-surface-container-low border border-outline-variant/60">
                    <p class="text-xs font-bold text-on-surface-variant uppercase">Jenis Surat</p>
                    <p class="font-bold text-on-surface text-base mt-0.5"><?= htmlspecialchars($request['mail_type_name']) ?></p>
                </div>
                <div class="p-4 rounded-xl bg-surface-container-low border border-outline-variant/60">
                    <p class="text-xs font-bold text-on-surface-variant uppercase">Tanggal Pengajuan</p>
                    <p class="font-medium text-on-surface mt-0.5"><?= format_date($request['dibuat_pada']) ?></p>
                </div>
                <div class="p-4 rounded-xl bg-surface-container-low border border-outline-variant/60">
                    <p class="text-xs font-bold text-on-surface-variant uppercase">Alamat Pemohon</p>
                    <p class="font-medium text-on-surface mt-0.5 truncate"><?= htmlspecialchars($request['alamat']) ?></p>
                </div>
                <div class="sm:col-span-2 p-4 rounded-xl bg-surface-container-low border border-outline-variant/60">
                    <p class="text-xs font-bold text-on-surface-variant uppercase">Keperluan</p>
                    <p class="font-medium text-on-surface mt-0.5"><?= htmlspecialchars($request['deskripsi'] ?: '-') ?></p>
                </div>
            </div>

            <?php if ($request['status'] === 'selesai'): ?>
                <div class="p-6 bg-emerald-50 border border-emerald-200 rounded-2xl flex flex-col sm:flex-row justify-between items-center gap-4">
                    <div>
                        <h4 class="font-bold text-emerald-900 text-lg flex items-center gap-2">
                            <span class="material-symbols-outlined text-emerald-700">task_alt</span> Dokumen Surat Selesai Diterbitkan!
                        </h4>
                        <p class="text-xs sm:text-sm text-emerald-700 mt-1">Surat resmi Anda telah disetujui. Anda dapat mengunduh dokumen atau mengambil fisik surat di kantor desa.</p>
                    </div>
                    <?php if ($request['file_hasil']): ?>
                        <div class="flex flex-wrap gap-2 shrink-0">
                            <button type="button" onclick="openFileViewer('<?= base_url($request['file_hasil']) ?>', 'Dokumen Hasil')" class="bg-primary hover:bg-primary-container text-white px-5 py-2.5 rounded-xl font-bold text-xs sm:text-sm flex items-center gap-1.5 transition-all shadow-sm">
                                <span class="material-symbols-outlined text-base">visibility</span> Lihat
                            </button>
                            <a href="<?= base_url($request['file_hasil']) ?>" download class="bg-emerald-700 hover:bg-emerald-800 text-white px-5 py-2.5 rounded-xl font-bold text-xs sm:text-sm flex items-center gap-1.5 transition-all shadow-sm">
                                <span class="material-symbols-outlined text-base">download</span> Unduh
                            </a>
                        </div>
                    <?php else: ?>
                        <span class="text-xs sm:text-sm text-emerald-800 font-semibold italic bg-emerald-100/60 px-4 py-2 rounded-xl">Silakan ambil berkas fisik di kantor desa pada jam kerja.</span>
                    <?php endif; ?>
                </div>
            <?php elseif ($request['status'] === 'ditolak'): ?>
                <div class="p-6 bg-red-50 border border-red-200 text-red-900 rounded-2xl">
                    <h4 class="font-bold text-base flex items-center gap-2">
                        <span class="material-symbols-outlined text-red-700">cancel</span> Pengajuan Ditolak
                    </h4>
                    <p class="text-xs sm:text-sm text-red-700 mt-1">Mohon maaf, berkas pengajuan Anda belum memenuhi persyaratan atau data tidak sesuai. Silakan hubungi kantor desa atau ajukan ulang.</p>
                </div>
            <?php else: ?>
                <div class="p-5 bg-amber-50 border border-amber-200 text-amber-900 rounded-2xl flex items-center gap-3">
                    <span class="material-symbols-outlined text-amber-700 text-2xl shrink-0">hourglass_top</span>
                    <p class="text-xs sm:text-sm">Pengajuan Anda saat ini sedang dalam antrean verifikasi petugas kantor desa. Silakan cek berkala status berkas Anda.</p>
                </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>

<!-- Modal File Viewer -->
<div id="file-viewer-modal" class="fixed inset-0 bg-black/60 backdrop-blur-sm z-50 flex items-center justify-center hidden p-4">
    <div class="bg-white rounded-3xl w-full max-w-4xl h-[85vh] flex flex-col overflow-hidden shadow-2xl">
        <div class="p-5 border-b border-outline-variant flex justify-between items-center bg-surface-container-low">
            <div>
                <h3 class="font-bold text-base text-on-surface" id="modal-file-title">Dokumen Hasil</h3>
                <p class="text-xs text-on-surface-variant mt-0.5 truncate max-w-xs sm:max-w-md" id="modal-file-subtitle"></p>
            </div>
            <button onclick="closeFileViewer()" class="w-9 h-9 rounded-full hover:bg-surface-container-high flex items-center justify-center text-on-surface-variant transition-all">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>
        <div class="flex-1 bg-surface-container-low relative p-4 flex items-center justify-center overflow-auto">
            <iframe id="pdf-viewer" class="w-full h-full rounded-2xl hidden" src=""></iframe>
            <img id="image-viewer" class="max-w-full max-h-full object-contain rounded-2xl hidden" src="" alt="Pratinjau File"/>
            <div id="fallback-viewer" class="text-center hidden p-8">
                <span class="material-symbols-outlined text-5xl text-on-surface-variant mb-4">draft</span>
                <p class="text-on-surface font-semibold mb-4 text-sm">Tipe file ini tidak dapat ditampilkan langsung di browser.</p>
                <a id="download-link" href="" class="inline-flex bg-primary text-white px-6 py-2.5 rounded-xl font-bold text-xs" download>Unduh Berkas</a>
            </div>
        </div>
    </div>
</div>

<script>
function openFileViewer(path, label) {
    const modal = document.getElementById('file-viewer-modal');
    const pdfViewer = document.getElementById('pdf-viewer');
    const imageViewer = document.getElementById('image-viewer');
    const fallbackViewer = document.getElementById('fallback-viewer');
    const downloadLink = document.getElementById('download-link');
    
    document.getElementById('modal-file-title').textContent = label;
    document.getElementById('modal-file-subtitle').textContent = path.split('/').pop();
    
    pdfViewer.classList.add('hidden');
    imageViewer.classList.add('hidden');
    fallbackViewer.classList.add('hidden');
    
    const ext = path.split('.').pop().toLowerCase();
    if (ext === 'pdf') {
        pdfViewer.src = path;
        pdfViewer.classList.remove('hidden');
    } else if (['jpg', 'jpeg', 'png', 'gif', 'webp'].includes(ext)) {
        imageViewer.src = path;
        imageViewer.classList.remove('hidden');
    } else {
        downloadLink.href = path;
        fallbackViewer.classList.remove('hidden');
    }
    
    modal.classList.remove('hidden');
}

function closeFileViewer() {
    const modal = document.getElementById('file-viewer-modal');
    document.getElementById('pdf-viewer').src = '';
    document.getElementById('image-viewer').src = '';
    modal.classList.add('hidden');
}
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
