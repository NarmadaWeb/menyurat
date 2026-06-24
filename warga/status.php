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
        $error = "Nomor registrasi tidak ditemukan.";
    }
}
?>

<div class="max-w-4xl mx-auto py-12 px-6">
    <div class="mb-12 text-center">
        <h1 class="text-4xl font-bold text-on-surface mb-4">Lacak Status Pengajuan</h1>
    </div>

    <div class="bg-white rounded-3xl p-8 shadow-xl border border-outline-variant mb-12">
        <form action="" method="GET" class="flex flex-col md:flex-row gap-4">
            <div class="flex-1 relative">
                <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-on-surface-variant">search</span>
                <input type="text" name="code" value="<?= isset($code) ? $code : '' ?>" required placeholder="REG-..."
                    class="w-full pl-12 pr-4 py-4 bg-surface-container-low border border-outline-variant rounded-2xl focus:ring-2 focus:ring-primary outline-none">
            </div>
            <button type="submit" class="bg-primary text-white px-10 py-4 rounded-2xl font-bold">Cek Status</button>
        </form>
    </div>

    <?php if ($error): ?>
        <p class="text-red-600 text-center"><?= $error ?></p>
    <?php endif; ?>

    <?php if ($request): ?>
        <div class="bg-white rounded-3xl shadow-xl border border-outline-variant p-8">
            <h2 class="text-2xl font-bold mb-4">Status: <span class="text-primary uppercase"><?= $request['status'] ?></span></h2>
            <div class="space-y-4 text-on-surface-variant">
                <p><strong>Nama Pemohon:</strong> <?= $request['nama_lengkap'] ?></p>
                <p><strong>Jenis Surat:</strong> <?= $request['mail_type_name'] ?></p>
                
                <?php if ($request['status'] === 'selesai'): ?>
                    <div class="mt-6 p-6 bg-green-50 border border-green-200 rounded-2xl flex flex-col sm:flex-row justify-between items-center gap-4">
                        <div>
                            <h4 class="font-bold text-green-900 text-lg">Dokumen Selesai Diproses!</h4>
                            <p class="text-sm text-green-700 mt-1">Silakan unduh dokumen hasil di bawah ini atau ambil langsung di kantor desa.</p>
                        </div>
                        <?php if ($request['file_hasil']): ?>
                            <div class="flex flex-wrap gap-3 shrink-0">
                                <button type="button" onclick="openFileViewer('<?= base_url($request['file_hasil']) ?>', 'Dokumen Hasil')" class="bg-primary hover:bg-primary/95 text-white px-6 py-3 rounded-xl font-bold flex items-center gap-2 transition-all">
                                    <span class="material-symbols-outlined text-base">visibility</span> Lihat
                                </button>
                                <a href="<?= base_url($request['file_hasil']) ?>" download class="bg-green-700 hover:bg-green-800 text-white px-6 py-3 rounded-xl font-bold flex items-center gap-2 transition-all">
                                    <span class="material-symbols-outlined text-base">download</span> Unduh
                                </a>
                            </div>
                        <?php else: ?>
                            <span class="text-sm text-green-700 font-semibold italic">Silakan ambil dokumen fisik di kantor desa.</span>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>
</div>

<!-- Modal File Viewer -->
<div id="file-viewer-modal" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center hidden">
    <div class="bg-white rounded-3xl w-full max-w-4xl h-[85vh] flex flex-col overflow-hidden shadow-2xl mx-4">
        <div class="p-6 border-b border-outline-variant flex justify-between items-center bg-surface-container-low">
            <div>
                <h3 class="font-bold text-lg text-on-surface" id="modal-file-title">Detail Lampiran</h3>
                <p class="text-xs text-on-surface-variant mt-0.5" id="modal-file-subtitle"></p>
            </div>
            <button onclick="closeFileViewer()" class="w-10 h-10 rounded-full hover:bg-surface-container-high flex items-center justify-center text-on-surface-variant transition-all">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>
        <div class="flex-1 bg-surface-container-low relative p-4 flex items-center justify-center overflow-auto">
            <!-- PDF Viewer -->
            <iframe id="pdf-viewer" class="w-full h-full rounded-2xl hidden" src=""></iframe>
            <!-- Image Viewer -->
            <img id="image-viewer" class="max-w-full max-h-full object-contain rounded-2xl hidden" src="" alt="Lampiran Image"/>
            <!-- Other file types -->
            <div id="fallback-viewer" class="text-center hidden p-8">
                <span class="material-symbols-outlined text-5xl text-on-surface-variant mb-4">draft</span>
                <p class="text-on-surface font-semibold mb-4">Tipe file ini tidak dapat ditampilkan langsung.</p>
                <a id="download-link" href="" class="inline-flex bg-primary text-white px-6 py-2.5 rounded-xl font-bold text-sm transition-all" download>Unduh File</a>
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
    
    // Hide all first
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
