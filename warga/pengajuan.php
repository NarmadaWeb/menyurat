<?php
$page_title = "Pengajuan Surat Online";
require_once __DIR__ . '/../includes/header.php';

$mail_types = $pdo->query("SELECT * FROM jenis_surat ORDER BY nama ASC")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nik = sanitize($_POST['nik']);
    $full_name = sanitize($_POST['full_name']);
    $address = sanitize($_POST['address']);
    $phone = sanitize($_POST['phone']);
    $email = sanitize($_POST['email']);
    $mail_type_id = (int)$_POST['mail_type_id'];
    $description = sanitize($_POST['description']);
    $reg_number = generate_registration_number();

    try {
        $pdo->beginTransaction();

        $stmt = $pdo->prepare("INSERT INTO pengajuan_warga (nomor_registrasi, nik, nama_lengkap, alamat, telepon, email, jenis_surat_id, deskripsi) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$reg_number, $nik, $full_name, $address, $phone, $email, $mail_type_id, $description]);
        $request_id = $pdo->lastInsertId();

        if (isset($_FILES['attachments'])) {
            $upload_dir = __DIR__ . '/../uploads/requests/';
            if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);

            foreach ($_FILES['attachments']['tmp_name'] as $key => $tmp_name) {
                if ($_FILES['attachments']['error'][$key] === UPLOAD_ERR_OK) {
                    $filename = time() . '_' . $key . '_' . basename($_FILES['attachments']['name'][$key]);
                    if (move_uploaded_file($tmp_name, $upload_dir . $filename)) {
                        $tipe_file = sanitize($_POST['attachment_labels'][$key] ?? 'Lampiran');
                        $stmt_att = $pdo->prepare("INSERT INTO lampiran_pengajuan (pengajuan_id, path_file, tipe_file) VALUES (?, ?, ?)");
                        $stmt_att->execute([$request_id, 'uploads/requests/' . $filename, $tipe_file]);
                    }
                }
            }
        }

        $pdo->commit();
        $success_msg = $reg_number;
    } catch (PDOException $e) {
        $pdo->rollBack();
        $error = "Gagal mengirim pengajuan: " . $e->getMessage();
    }
}
?>

<div class="max-w-4xl mx-auto py-8 sm:py-12 px-4 sm:px-6">
    <?php if (isset($success_msg)): ?>
        <div class="bg-white rounded-3xl p-8 sm:p-12 shadow-xl border border-outline-variant text-center max-w-2xl mx-auto">
            <div class="w-20 h-20 sm:w-24 sm:h-24 bg-primary text-white rounded-3xl flex items-center justify-center mx-auto mb-6 shadow-xl shadow-primary/20">
                <span class="material-symbols-outlined text-4xl sm:text-6xl">verified</span>
            </div>
            <h2 class="text-2xl sm:text-3xl font-extrabold mb-3 text-on-surface">Pengajuan Berhasil Terkirim!</h2>
            <p class="text-on-surface-variant text-sm sm:text-base mb-6">Berkas Anda telah diterima sistem dan akan segera diverifikasi oleh petugas desa.</p>
            <div class="bg-surface-container-low py-4 px-6 sm:px-8 rounded-2xl inline-block mb-6 border border-outline-variant">
                <p class="text-xs text-on-surface-variant font-bold uppercase tracking-wider mb-1">Nomor Registrasi Anda</p>
                <span class="text-2xl sm:text-3xl font-mono font-extrabold text-primary select-all"><?= $success_msg ?></span>
            </div>
            <p class="text-xs sm:text-sm text-on-surface-variant mb-8 max-w-md mx-auto">Simpan nomor registrasi di atas untuk memeriksa status penerbitan dokumen pada menu Cek Status.</p>
            <div class="flex flex-col sm:flex-row gap-3 justify-center">
                <a href="<?= base_url('warga/status.php?code=' . urlencode($success_msg)) ?>" class="bg-primary hover:bg-primary-container text-white px-6 py-3 rounded-xl font-bold transition-all shadow-md text-sm">
                    Lihat Status Sekarang
                </a>
                <a href="<?= base_url() ?>" class="border border-outline-variant px-6 py-3 rounded-xl font-bold hover:bg-surface-container-low transition-all text-sm">
                    Kembali ke Beranda
                </a>
            </div>
        </div>
    <?php else: ?>
        <div class="mb-8 sm:mb-10 text-center sm:text-left">
            <h1 class="text-3xl sm:text-4xl font-extrabold text-on-surface mb-2">Formulir Pengajuan Surat</h1>
            <p class="text-on-surface-variant text-sm sm:text-base">Lengkapi data diri dan lampiran persyaratan dokumen sesuai ketentuan pelayanan.</p>
        </div>

        <?php if (isset($error)): ?>
            <div class="mb-6 p-4 rounded-xl bg-red-100 text-red-800 text-sm font-semibold">
                <?= $error ?>
            </div>
        <?php endif; ?>

        <form action="" method="POST" enctype="multipart/form-data" class="space-y-6 sm:space-y-8">
            <section class="bg-white rounded-3xl p-6 sm:p-8 shadow-sm border border-outline-variant">
                <h2 class="text-lg sm:text-xl font-bold mb-6 flex items-center gap-2 text-primary pb-3 border-b border-outline-variant">
                    <span class="material-symbols-outlined">person</span> 1. Identitas Pemohon
                </h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6">
                    <div class="space-y-1.5">
                        <label for="nik" class="block text-xs font-bold uppercase text-on-surface-variant">NIK (Sesuai KTP) <span class="text-error">*</span></label>
                        <input type="text" id="nik" name="nik" required maxlength="16" placeholder="16 digit NIK" class="w-full px-4 py-3 border border-outline-variant rounded-xl focus:ring-2 focus:ring-primary outline-none text-sm font-mono transition-all">
                    </div>
                    <div class="space-y-1.5">
                        <label for="full_name" class="block text-xs font-bold uppercase text-on-surface-variant">Nama Lengkap <span class="text-error">*</span></label>
                        <input type="text" id="full_name" name="full_name" required placeholder="Nama lengkap sesuai KTP" class="w-full px-4 py-3 border border-outline-variant rounded-xl focus:ring-2 focus:ring-primary outline-none text-sm transition-all">
                    </div>
                    <div class="sm:col-span-2 space-y-1.5">
                        <label for="address" class="block text-xs font-bold uppercase text-on-surface-variant">Alamat Tempat Tinggal <span class="text-error">*</span></label>
                        <textarea id="address" name="address" required rows="2" placeholder="Nama dusun, RT/RW, Desa Pendua" class="w-full px-4 py-3 border border-outline-variant rounded-xl focus:ring-2 focus:ring-primary outline-none text-sm transition-all"></textarea>
                    </div>
                    <div class="space-y-1.5">
                        <label for="phone" class="block text-xs font-bold uppercase text-on-surface-variant">Nomor WhatsApp Aktif <span class="text-error">*</span></label>
                        <input type="tel" id="phone" name="phone" required placeholder="08xxxxxxxxxx" class="w-full px-4 py-3 border border-outline-variant rounded-xl focus:ring-2 focus:ring-primary outline-none text-sm transition-all">
                    </div>
                    <div class="space-y-1.5">
                        <label for="email" class="block text-xs font-bold uppercase text-on-surface-variant">Email</label>
                        <input type="email" id="email" name="email" placeholder="nama@email.com (opsional)" class="w-full px-4 py-3 border border-outline-variant rounded-xl focus:ring-2 focus:ring-primary outline-none text-sm transition-all">
                    </div>
                </div>
            </section>

            <section class="bg-white rounded-3xl p-6 sm:p-8 shadow-sm border border-outline-variant">
                <h2 class="text-lg sm:text-xl font-bold mb-6 flex items-center gap-2 text-primary pb-3 border-b border-outline-variant">
                    <span class="material-symbols-outlined">description</span> 2. Rincian Surat yang Dibutuhkan
                </h2>
                <div class="space-y-5">
                    <div class="space-y-1.5">
                        <label for="mail_type_id" class="block text-xs font-bold uppercase text-on-surface-variant">Pilih Jenis Surat <span class="text-error">*</span></label>
                        <select id="mail_type_id" name="mail_type_id" required class="w-full px-4 py-3 border border-outline-variant rounded-xl focus:ring-2 focus:ring-primary outline-none text-sm bg-white transition-all">
                            <option value="">-- Pilih Jenis Layanan Surat --</option>
                            <?php foreach ($mail_types as $type): ?>
                                <option value="<?= $type['id'] ?>" data-code="<?= $type['kode'] ?>"><?= htmlspecialchars($type['nama']) ?> (<?= $type['kode'] ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="space-y-1.5">
                        <label for="description" class="block text-xs font-bold uppercase text-on-surface-variant">Keperluan / Alasan Pembuatan <span class="text-error">*</span></label>
                        <textarea id="description" name="description" required rows="3" class="w-full px-4 py-3 border border-outline-variant rounded-xl focus:ring-2 focus:ring-primary outline-none text-sm transition-all" placeholder="Jelaskan untuk keperluan apa surat ini diajukan..."></textarea>
                    </div>
                </div>
            </section>

            <section class="bg-white rounded-3xl p-6 sm:p-8 shadow-sm border border-outline-variant">
                <h2 class="text-lg sm:text-xl font-bold mb-4 flex items-center gap-2 text-primary pb-3 border-b border-outline-variant">
                    <span class="material-symbols-outlined">upload_file</span> 3. Dokumen Lampiran Persyaratan
                </h2>
                <div class="bg-amber-50 border border-amber-200 text-amber-900 rounded-2xl p-4 mb-6 text-xs sm:text-sm flex items-start gap-3" id="req-notice">
                    <span class="material-symbols-outlined text-amber-700 shrink-0 text-xl">info</span>
                    <span>Pilih jenis surat di atas terlebih dahulu untuk memunculkan dokumen persyaratan yang harus diunggah (Format gambar/PDF).</span>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4" id="attachment-container">
                    <!-- Dynamic slots generated here -->
                </div>
                <button type="button" onclick="addAttachmentField()" class="mt-4 text-primary font-bold text-xs sm:text-sm flex items-center gap-1 hover:underline hidden" id="btn-add-attachment">
                    <span class="material-symbols-outlined text-base">add_circle</span> Tambah Lampiran Tambahan
                </button>
            </section>

            <button type="submit" class="w-full bg-primary hover:bg-primary-container text-white py-4 rounded-2xl font-bold text-base sm:text-lg transition-all shadow-xl shadow-primary/20 flex items-center justify-center gap-2 active:scale-[0.99]">
                <span class="material-symbols-outlined text-xl">send</span> Kirim Permohonan Surat
            </button>
        </form>
    <?php endif; ?>
</div>

<script>
const requirements = {
    'SKM': ['KTP Almarhum', 'Kartu Keluarga (KK)', 'Surat Keterangan Kematian dari RS/RT'],
    'SKU': ['KTP Pemohon', 'Kartu Keluarga (KK)', 'Foto Tempat Usaha / Pengantar'],
    'SKD': ['KTP Pemohon', 'Kartu Keluarga (KK)', 'Surat Pengantar RT/RW'],
    'SKTM': ['KTP Pemohon', 'Kartu Keluarga (KK)', 'Surat Pengantar / Bukti Foto Rumah'],
    'SKBM': ['KTP Pemohon', 'Kartu Keluarga (KK)', 'Surat Pernyataan Belum Menikah'],
    'SKCK': ['KTP Pemohon', 'Kartu Keluarga (KK)', 'Akta Kelahiran', 'Surat Pengantar RT/RW'],
    'SKIK': ['KTP Penanggung Jawab', 'Kartu Keluarga (KK)', 'Surat Pengantar RT/RW', 'Rundown / Proposal Acara'],
    'SKP': ['KTP Pemohon', 'Kartu Keluarga (KK)', 'Pas Foto 3x4', 'Surat Pengantar RT/RW'],
    'SKKB': ['KTP Pemohon', 'Kartu Keluarga (KK)', 'Surat Pengantar RT/RW'],
    'SKDL': ['KTP Pimpinan Lembaga', 'Akta Pendirian / SK Kemenkumham', 'Surat Pengantar RT/RW']
};

document.getElementById('mail_type_id')?.addEventListener('change', function() {
    const selectedOption = this.options[this.selectedIndex];
    const code = selectedOption.getAttribute('data-code');
    const container = document.getElementById('attachment-container');
    const notice = document.getElementById('req-notice');
    const btnAdd = document.getElementById('btn-add-attachment');
    
    if (!container) return;
    container.innerHTML = '';
    
    if (!code) {
        notice.classList.remove('hidden');
        btnAdd.classList.add('hidden');
        return;
    }
    
    notice.classList.add('hidden');
    btnAdd.classList.remove('hidden');
    
    const docs = requirements[code] || ['KTP Pemohon', 'Kartu Keluarga (KK)', 'Dokumen Pendukung'];
    
    docs.forEach((doc) => {
        createAttachmentSlot(doc, true);
    });
});

function createAttachmentSlot(label, required = false) {
    const container = document.getElementById('attachment-container');
    const div = document.createElement('div');
    div.className = "border-2 border-dashed border-outline-variant rounded-2xl p-5 flex flex-col items-center justify-center gap-2 hover:border-primary cursor-pointer bg-surface-container-low transition-all relative text-center";
    div.onclick = function() { this.querySelector('input').click(); };
    div.innerHTML = `
        <span class="material-symbols-outlined text-3xl text-primary">upload_file</span>
        <p class="text-xs sm:text-sm font-bold text-on-surface">${label} ${required ? '<span class="text-error">*</span>' : ''}</p>
        <p class="text-[11px] text-on-surface-variant file-name truncate max-w-full">Pilih file (JPG, PNG, PDF)</p>
        <input type="file" name="attachments[]" ${required ? 'required' : ''} class="hidden" onchange="updateFileLabel(this)">
        <input type="hidden" name="attachment_labels[]" value="${label}">
    `;
    container.appendChild(div);
}

function updateFileLabel(input) {
    if (input.files.length > 0) {
        const p = input.parentElement.querySelector('.file-name');
        p.textContent = input.files[0].name;
        p.classList.replace('text-on-surface-variant', 'text-primary');
        p.classList.add('font-semibold');
        input.parentElement.classList.replace('border-outline-variant', 'border-primary');
        input.parentElement.classList.add('bg-primary/5');
    }
}

function addAttachmentField() {
    createAttachmentSlot('Lampiran Tambahan', false);
}
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
