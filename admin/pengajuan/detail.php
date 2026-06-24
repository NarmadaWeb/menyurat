<?php
$page_title = "Detail Pengajuan";
require_once __DIR__ . '/../../includes/auth.php';
require_login();
$id = (int)$_GET['id'];
$request = $pdo->query("SELECT r.*, mt.nama as mail_type_name FROM pengajuan_warga r LEFT JOIN jenis_surat mt ON r.jenis_surat_id = mt.id WHERE r.id = $id")->fetch();
$attachments = $pdo->query("SELECT * FROM lampiran_pengajuan WHERE pengajuan_id = $id")->fetchAll();
require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../includes/sidebar.php';
?>
<div class="bg-white p-8 rounded-2xl border border-outline-variant shadow-sm max-w-2xl">
    <h2 class="text-2xl font-bold mb-4">Pengajuan: <?= $request['nama_lengkap'] ?></h2>
    <div class="space-y-2 text-sm text-on-surface-variant mb-6">
        <p><strong>NIK:</strong> <?= $request['nik'] ?></p>
        <p><strong>Email:</strong> <?= $request['email'] ?: '-' ?></p>
        <p><strong>Nomor Telepon:</strong> <?= $request['telepon'] ?: '-' ?></p>
        <p><strong>Alamat:</strong> <?= $request['alamat'] ?: '-' ?></p>
        <p><strong>Jenis Surat:</strong> <?= $request['mail_type_name'] ?></p>
        <p><strong>Deskripsi / Keperluan:</strong> <?= $request['deskripsi'] ?: '-' ?></p>
        <p><strong>Status Saat Ini:</strong> <span class="font-bold uppercase text-primary"><?= $request['status'] ?></span></p>
    </div>

    <!-- Citizen Attachments -->
    <div class="mt-6 pt-6 border-t border-outline-variant">
        <h3 class="font-bold mb-4 text-on-surface">Lampiran Persyaratan Warga</h3>
        <?php if (empty($attachments)): ?>
            <p class="text-sm text-on-surface-variant italic">Tidak ada lampiran.</p>
        <?php else: ?>
            <div class="grid grid-cols-1 gap-3">
                <?php foreach ($attachments as $att): ?>
                    <div class="flex items-center justify-between p-4 bg-surface-container-low rounded-xl border border-outline-variant">
                        <div class="flex items-center gap-3">
                            <span class="material-symbols-outlined text-primary">description</span>
                            <div>
                                <p class="text-sm font-semibold text-on-surface"><?= htmlspecialchars($att['tipe_file'] ?? '') ?></p>
                                <p class="text-xs text-on-surface-variant"><?= basename($att['path_file']) ?></p>
                            </div>
                        </div>
                        <button type="button" onclick="openFileViewer('<?= base_url($att['path_file']) ?>', '<?= htmlspecialchars($att['tipe_file'] ?? 'Lampiran') ?>')" class="bg-primary/10 hover:bg-primary/20 text-primary px-3 py-1.5 rounded-lg text-xs font-bold transition-all">
                            Lihat File
                        </button>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- Completed Document Link if exists -->
    <?php if ($request['file_hasil']): ?>
        <div class="mt-6 pt-6 border-t border-outline-variant">
            <h3 class="font-bold mb-2 text-on-surface">Dokumen Hasil yang Dikirim</h3>
            <div class="p-4 bg-green-50 border border-green-200 text-green-900 rounded-xl flex items-center justify-between">
                <span class="text-sm">Dokumen: <b><?= basename($request['file_hasil']) ?></b></span>
                <div class="flex items-center gap-4">
                    <button type="button" onclick="openFileViewer('<?= base_url($request['file_hasil']) ?>', 'Dokumen Hasil')" class="text-green-700 font-bold text-sm hover:underline">Lihat</button>
                    <a href="<?= base_url($request['file_hasil']) ?>" download class="text-green-700 font-bold text-sm hover:underline">Unduh</a>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <div class="mt-8 pt-6 border-t border-outline-variant">
        <h3 class="font-bold mb-4">Update Status & Unggah Hasil</h3>
        <form action="proses.php" method="POST" enctype="multipart/form-data" class="space-y-4">
            <input type="hidden" name="request_id" value="<?= $id ?>">
            <div>
                <label class="block text-xs font-bold text-on-surface-variant uppercase mb-2">Status Pengajuan</label>
                <select name="status" id="status-select" class="w-full border rounded-xl p-3 bg-white">
                    <option value="diproses" <?= $request['status'] === 'diproses' ? 'selected' : '' ?>>Proses</option>
                    <option value="selesai" <?= $request['status'] === 'selesai' ? 'selected' : '' ?>>Selesai</option>
                    <option value="ditolak" <?= $request['status'] === 'ditolak' ? 'selected' : '' ?>>Tolak</option>
                </select>
            </div>
            
            <div id="file-hasil-container" class="space-y-2 <?= $request['status'] === 'selesai' ? '' : 'hidden' ?>">
                <label class="block text-xs font-bold text-on-surface-variant uppercase">Unggah Dokumen Hasil (PDF/DOCX/JPG/PNG)</label>
                <input type="file" name="file_hasil" class="w-full border rounded-xl p-3 bg-white">
            </div>

            <button type="submit" class="bg-primary text-white px-8 py-3 rounded-xl font-bold">Simpan Perubahan</button>
        </form>
    </div>
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
document.getElementById('status-select').addEventListener('change', function() {
    const container = document.getElementById('file-hasil-container');
    if (this.value === 'selesai') {
        container.classList.remove('hidden');
    } else {
        container.classList.add('hidden');
    }
});

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
<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
