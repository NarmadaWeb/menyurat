<?php
$page_title = "Pengajuan Surat Online";
require_once __DIR__ . '/../includes/header.php';

$mail_types = $pdo->query("SELECT * FROM mail_types ORDER BY name ASC")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nik = sanitize($_POST['nik']);
    $full_name = sanitize($_POST['full_name']);
    $address = sanitize($_POST['address']);
    $phone = sanitize($_POST['phone']);
    $mail_type_id = (int)$_POST['mail_type_id'];
    $description = sanitize($_POST['description']);
    $reg_number = generate_registration_number();

    try {
        $pdo->beginTransaction();

        $stmt = $pdo->prepare("INSERT INTO citizen_requests (registration_number, nik, full_name, address, phone, mail_type_id, description) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$reg_number, $nik, $full_name, $address, $phone, $mail_type_id, $description]);
        $request_id = $pdo->lastInsertId();

        if (isset($_FILES['attachments'])) {
            $upload_dir = __DIR__ . '/../uploads/requests/';
            if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);

            foreach ($_FILES['attachments']['tmp_name'] as $key => $tmp_name) {
                if ($_FILES['attachments']['error'][$key] === UPLOAD_ERR_OK) {
                    $filename = time() . '_' . $key . '_' . basename($_FILES['attachments']['name'][$key]);
                    if (move_uploaded_file($tmp_name, $upload_dir . $filename)) {
                        $stmt_att = $pdo->prepare("INSERT INTO request_attachments (request_id, file_path) VALUES (?, ?)");
                        $stmt_att->execute([$request_id, 'uploads/requests/' . $filename]);
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
                                <option value="<?= $type['id'] ?>"><?= $type['name'] ?></option>
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
                    <span class="material-symbols-outlined text-primary">upload_file</span> Unggah Lampiran
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4" id="attachment-container">
                    <div class="border-2 border-dashed border-outline-variant rounded-2xl p-6 flex flex-col items-center justify-center gap-2 hover:border-primary cursor-pointer" onclick="this.querySelector('input').click()">
                        <span class="material-symbols-outlined text-3xl text-on-surface-variant">add_a_photo</span>
                        <p class="text-xs font-bold text-on-surface-variant">Pilih File</p>
                        <input type="file" name="attachments[]" class="hidden" onchange="showFileName(this)">
                    </div>
                </div>
                <button type="button" onclick="addAttachmentField()" class="mt-4 text-primary font-bold text-sm flex items-center gap-1 hover:underline">
                    <span class="material-symbols-outlined text-lg">add_circle</span> Tambah Lampiran
                </button>
            </section>

            <button type="submit" class="w-full bg-primary text-white py-4 rounded-2xl font-bold text-xl hover:bg-primary-container transition-all shadow-lg flex items-center justify-center gap-3">
                <span class="material-symbols-outlined">send</span> Kirim Pengajuan
            </button>
        </form>
    <?php endif; ?>
</div>

<script>
function showFileName(input) {
    if (input.files.length > 0) {
        const p = input.parentElement.querySelector('p');
        p.textContent = input.files[0].name;
        input.parentElement.classList.replace('border-outline-variant', 'border-primary');
        input.parentElement.classList.add('bg-primary/5');
    }
}

function addAttachmentField() {
    const container = document.getElementById('attachment-container');
    const div = document.createElement('div');
    div.className = "border-2 border-dashed border-outline-variant rounded-2xl p-6 flex flex-col items-center justify-center gap-2 hover:border-primary cursor-pointer";
    div.onclick = function() { this.querySelector('input').click(); };
    div.innerHTML = `
        <span class="material-symbols-outlined text-3xl text-on-surface-variant">add_a_photo</span>
        <p class="text-xs font-bold text-on-surface-variant">Pilih File</p>
        <input type="file" name="attachments[]" class="hidden" onchange="showFileName(this)">
    `;
    container.appendChild(div);
}
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
