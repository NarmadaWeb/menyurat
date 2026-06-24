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

<div class="max-w-4xl mx-auto py-12 px-6">
    <?php if (isset($success_msg)): ?>
        <div class="bg-white rounded-3xl p-12 shadow-xl border border-outline-variant text-center">
            <div class="w-24 h-24 bg-primary text-white rounded-full flex items-center justify-center mx-auto mb-8 shadow-lg shadow-primary/20">
                <span class="material-symbols-outlined text-6xl">verified</span>
            </div>
            <h2 class="text-3xl font-bold mb-4">Pengajuan Berhasil!</h2>
            <p class="text-on-surface-variant text-lg mb-8">Nomor registrasi Anda adalah:</p>
            <div class="bg-surface-container-low py-4 px-8 rounded-2xl inline-block mb-8">
                <span class="text-3xl font-mono font-bold text-primary"><?= $success_msg ?></span>
            </div>
            <p class="text-on-surface-variant mb-12">Harap simpan nomor ini untuk melakukan pengecekan status secara berkala di halaman <b>Cek Status</b>.</p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="<?= base_url('warga/pengajuan.php') ?>" class="bg-primary text-white px-8 py-3 rounded-xl font-bold hover:bg-primary-container transition-all">Ajukan Lagi</a>
                <a href="<?= base_url() ?>" class="border border-outline-variant px-8 py-3 rounded-xl font-bold hover:bg-surface-container-low transition-all">Kembali ke Beranda</a>
            </div>
        </div>
    <?php else: ?>
        <div class="mb-12">
            <h1 class="text-4xl font-bold text-on-surface mb-4">Pengajuan Surat Online</h1>
            <p class="text-on-surface-variant text-lg">Silakan lengkapi formulir di bawah ini untuk memproses dokumen administrasi Anda secara digital.</p>
        </div>

        <form action="" method="POST" enctype="multipart/form-data" class="space-y-8">
            <section class="bg-white rounded-2xl p-8 shadow-sm border border-outline-variant">
                <h2 class="text-xl font-bold mb-6 flex items-center gap-2">
                    <span class="material-symbols-outlined text-primary">person</span> Identitas Pemohon
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label for="nik" class="block text-sm font-semibold">NIK (Sesuai KTP)</label>
                        <input type="text" id="nik" name="nik" required class="w-full px-4 py-3 border border-outline-variant rounded-xl focus:ring-2 focus:ring-primary outline-none">
                    </div>
                    <div class="space-y-2">
                        <label for="full_name" class="block text-sm font-semibold">Nama Lengkap</label>
                        <input type="text" id="full_name" name="full_name" required class="w-full px-4 py-3 border border-outline-variant rounded-xl focus:ring-2 focus:ring-primary outline-none">
                    </div>
                    <div class="md:col-span-2 space-y-2">
                        <label for="address" class="block text-sm font-semibold">Alamat Lengkap</label>
                        <textarea id="address" name="address" required rows="3" class="w-full px-4 py-3 border border-outline-variant rounded-xl focus:ring-2 focus:ring-primary outline-none"></textarea>
                    </div>
                    <div class="space-y-2">
                        <label for="phone" class="block text-sm font-semibold">Nomor WhatsApp</label>
                        <input type="tel" id="phone" name="phone" required class="w-full px-4 py-3 border border-outline-variant rounded-xl focus:ring-2 focus:ring-primary outline-none" placeholder="08xxxxxxxxxx">
                    </div>
                    <div class="space-y-2">
                        <label for="email" class="block text-sm font-semibold">Email Aktif</label>
                        <input type="email" id="email" name="email" required class="w-full px-4 py-3 border border-outline-variant rounded-xl focus:ring-2 focus:ring-primary outline-none" placeholder="warga@email.com">
                    </div>
                </div>
            </section>

            <section class="bg-white rounded-2xl p-8 shadow-sm border border-outline-variant">
                <h2 class="text-xl font-bold mb-6 flex items-center gap-2">
                    <span class="material-symbols-outlined text-primary">description</span> Detail Pengajuan
                </h2>
                <div class="space-y-6">
                    <div class="space-y-2">
                        <label for="mail_type_id" class="block text-sm font-semibold">Jenis Surat</label>
                        <select id="mail_type_id" name="mail_type_id" required class="w-full px-4 py-3 border border-outline-variant rounded-xl focus:ring-2 focus:ring-primary outline-none">
                            <option value="">-- Pilih Jenis Surat --</option>
                            <?php foreach ($mail_types as $type): ?>
                                <option value="<?= $type['id'] ?>" data-code="<?= $type['kode'] ?>"><?= $type['nama'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="space-y-2">
                        <label for="description" class="block text-sm font-semibold">Keperluan / Keterangan</label>
                        <textarea id="description" name="description" required rows="4" class="w-full px-4 py-3 border border-outline-variant rounded-xl focus:ring-2 focus:ring-primary outline-none" placeholder="Jelaskan alasan pengajuan Anda..."></textarea>
                    </div>
                </div>
            </section>

            <section class="bg-white rounded-2xl p-8 shadow-sm border border-outline-variant">
                <h2 class="text-xl font-bold mb-6 flex items-center gap-2">
                    <span class="material-symbols-outlined text-primary">upload_file</span> Unggah Lampiran Persyaratan
                </h2>
                <div class="bg-amber-50 border border-amber-200 text-amber-900 rounded-xl p-4 mb-6 text-sm flex items-start gap-2" id="req-notice">
                    <span class="material-symbols-outlined text-amber-700 shrink-0">info</span>
                    <span>Pilih jenis surat terlebih dahulu untuk melihat daftar dokumen persyaratan yang harus diunggah.</span>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4" id="attachment-container">
                    <!-- Dynamic slots generated here -->
                </div>
                <button type="button" onclick="addAttachmentField()" class="mt-4 text-primary font-bold text-sm flex items-center gap-1 hover:underline hidden" id="btn-add-attachment">
                    <span class="material-symbols-outlined text-lg">add_circle</span> Tambah Lampiran Tambahan
                </button>
            </section>

            <button type="submit" class="w-full bg-primary text-white py-4 rounded-2xl font-bold text-xl hover:bg-primary-container transition-all shadow-lg flex items-center justify-center gap-3">
                <span class="material-symbols-outlined">send</span> Kirim Pengajuan
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

document.getElementById('mail_type_id').addEventListener('change', function() {
    const selectedOption = this.options[this.selectedIndex];
    const code = selectedOption.getAttribute('data-code');
    const container = document.getElementById('attachment-container');
    const notice = document.getElementById('req-notice');
    const btnAdd = document.getElementById('btn-add-attachment');
    
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
    div.className = "border-2 border-dashed border-outline-variant rounded-2xl p-6 flex flex-col items-center justify-center gap-2 hover:border-primary cursor-pointer bg-surface-container-low transition-all relative";
    div.onclick = function() { this.querySelector('input').click(); };
    div.innerHTML = `
        <span class="material-symbols-outlined text-3xl text-primary">upload_file</span>
        <p class="text-sm font-bold text-on-surface text-center">${label} ${required ? '<span class="text-error">*</span>' : ''}</p>
        <p class="text-xs text-on-surface-variant text-center file-name">Belum ada file terpilih</p>
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
        input.parentElement.classList.replace('border-outline-variant', 'border-primary');
        input.parentElement.classList.add('bg-primary/5');
    }
}

function addAttachmentField() {
    createAttachmentSlot('Lampiran Tambahan', false);
}
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
