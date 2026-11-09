<?php
$page_title = "Tambah Penduduk";
require_once __DIR__ . '/../../includes/auth.php';
require_login();

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nik = sanitize($_POST['nik']);
    $no_kk = sanitize($_POST['no_kk']);
    $nama_lengkap = sanitize($_POST['nama_lengkap']);
    $jenis_kelamin = sanitize($_POST['jenis_kelamin']);
    $tempat_lahir = sanitize($_POST['tempat_lahir']);
    $tanggal_lahir = !empty($_POST['tanggal_lahir']) ? sanitize($_POST['tanggal_lahir']) : null;
    $agama = sanitize($_POST['agama']);
    $pendidikan = sanitize($_POST['pendidikan']);
    $pekerjaan = sanitize($_POST['pekerjaan']);
    $status_perkawinan = sanitize($_POST['status_perkawinan']);
    $kewarganegaraan = sanitize($_POST['kewarganegaraan']) ?: 'WNI';
    $alamat = sanitize($_POST['alamat']);
    $rt = sanitize($_POST['rt']) ?: '001';
    $rw = sanitize($_POST['rw']) ?: '001';
    $telepon = sanitize($_POST['telepon']);

    if (empty($nik) || strlen($nik) < 16) {
        $errors[] = "NIK harus terdiri dari minimal 16 digit angka.";
    }
    if (empty($nama_lengkap)) {
        $errors[] = "Nama lengkap wajib diisi.";
    }

    // Check duplicate NIK
    $check = $pdo->prepare("SELECT id FROM penduduk WHERE nik = ?");
    $check->execute([$nik]);
    if ($check->fetch()) {
        $errors[] = "NIK '$nik' sudah terdaftar dalam sistem.";
    }

    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO penduduk (nik, no_kk, nama_lengkap, jenis_kelamin, tempat_lahir, tanggal_lahir, agama, pendidikan, pekerjaan, status_perkawinan, kewarganegaraan, alamat, rt, rw, telepon) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $nik, $no_kk, $nama_lengkap, $jenis_kelamin, $tempat_lahir,
                $tanggal_lahir, $agama, $pendidikan, $pekerjaan, $status_perkawinan,
                $kewarganegaraan, $alamat, $rt, $rw, $telepon
            ]);

            redirect(base_url('admin/penduduk/index.php'), "Data penduduk $nama_lengkap berhasil ditambahkan.");
        } catch (PDOException $e) {
            $errors[] = "Gagal menyimpan data: " . $e->getMessage();
        }
    }
}

require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../includes/sidebar.php';
?>

<div class="max-w-4xl mx-auto">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="text-2xl font-bold text-on-surface">Tambah Data Penduduk</h2>
            <p class="text-on-surface-variant text-sm">Masukkan informasi kependudukan warga desa dengan lengkap.</p>
        </div>
        <a href="<?= base_url('admin/penduduk/index.php') ?>" class="border border-outline-variant px-4 py-2 rounded-xl text-sm font-semibold hover:bg-surface-container-low transition-all flex items-center gap-1">
            <span class="material-symbols-outlined text-base">arrow_back</span> Kembali
        </a>
    </div>

    <?php if (!empty($errors)): ?>
        <div class="mb-6 p-4 rounded-xl bg-red-100 text-red-800 text-sm space-y-1">
            <?php foreach ($errors as $err): ?>
                <p>&bull; <?= $err ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <form action="" method="POST" class="bg-white rounded-2xl border border-outline-variant shadow-sm p-6 sm:p-8 space-y-6">
        <div>
            <h3 class="text-base font-bold text-primary mb-4 flex items-center gap-2 pb-2 border-b border-outline-variant">
                <span class="material-symbols-outlined">badge</span> Nomor Identitas
            </h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold uppercase text-on-surface-variant mb-1">NIK (Nomor Induk Kependudukan) <span class="text-error">*</span></label>
                    <input type="text" name="nik" maxlength="20" required value="<?= $_POST['nik'] ?? '' ?>" placeholder="16 digit NIK"
                        class="w-full px-4 py-2.5 border border-outline-variant rounded-xl focus:ring-2 focus:ring-primary outline-none text-sm font-mono">
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase text-on-surface-variant mb-1">Nomor Kartu Keluarga (No. KK)</label>
                    <input type="text" name="no_kk" maxlength="20" value="<?= $_POST['no_kk'] ?? '' ?>" placeholder="16 digit No KK"
                        class="w-full px-4 py-2.5 border border-outline-variant rounded-xl focus:ring-2 focus:ring-primary outline-none text-sm font-mono">
                </div>
            </div>
        </div>

        <div>
            <h3 class="text-base font-bold text-primary mb-4 flex items-center gap-2 pb-2 border-b border-outline-variant">
                <span class="material-symbols-outlined">person</span> Data Pribadi
            </h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="sm:col-span-2">
                    <label class="block text-xs font-bold uppercase text-on-surface-variant mb-1">Nama Lengkap <span class="text-error">*</span></label>
                    <input type="text" name="nama_lengkap" required value="<?= $_POST['nama_lengkap'] ?? '' ?>" placeholder="Nama sesuai KTP"
                        class="w-full px-4 py-2.5 border border-outline-variant rounded-xl focus:ring-2 focus:ring-primary outline-none text-sm">
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase text-on-surface-variant mb-1">Jenis Kelamin <span class="text-error">*</span></label>
                    <select name="jenis_kelamin" required class="w-full px-4 py-2.5 border border-outline-variant rounded-xl focus:ring-2 focus:ring-primary outline-none text-sm bg-white">
                        <option value="Laki-laki" <?= (isset($_POST['jenis_kelamin']) && $_POST['jenis_kelamin'] === 'Laki-laki') ? 'selected' : '' ?>>Laki-laki</option>
                        <option value="Perempuan" <?= (isset($_POST['jenis_kelamin']) && $_POST['jenis_kelamin'] === 'Perempuan') ? 'selected' : '' ?>>Perempuan</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase text-on-surface-variant mb-1">Agama</label>
                    <select name="agama" class="w-full px-4 py-2.5 border border-outline-variant rounded-xl focus:ring-2 focus:ring-primary outline-none text-sm bg-white">
                        <?php foreach (['Islam', 'Kristen', 'Katolik', 'Hindu', 'Buddha', 'Khonghucu'] as $ag): ?>
                            <option value="<?= $ag ?>" <?= (isset($_POST['agama']) && $_POST['agama'] === $ag) ? 'selected' : '' ?>><?= $ag ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase text-on-surface-variant mb-1">Tempat Lahir</label>
                    <input type="text" name="tempat_lahir" value="<?= $_POST['tempat_lahir'] ?? '' ?>" placeholder="Kota/Kabupaten"
                        class="w-full px-4 py-2.5 border border-outline-variant rounded-xl focus:ring-2 focus:ring-primary outline-none text-sm">
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase text-on-surface-variant mb-1">Tanggal Lahir</label>
                    <input type="date" name="tanggal_lahir" value="<?= $_POST['tanggal_lahir'] ?? '' ?>"
                        class="w-full px-4 py-2.5 border border-outline-variant rounded-xl focus:ring-2 focus:ring-primary outline-none text-sm">
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase text-on-surface-variant mb-1">Status Perkawinan</label>
                    <select name="status_perkawinan" class="w-full px-4 py-2.5 border border-outline-variant rounded-xl focus:ring-2 focus:ring-primary outline-none text-sm bg-white">
                        <?php foreach (['Belum Kawin', 'Kawin', 'Cerai Hidup', 'Cerai Mati'] as $st): ?>
                            <option value="<?= $st ?>" <?= (isset($_POST['status_perkawinan']) && $_POST['status_perkawinan'] === $st) ? 'selected' : '' ?>><?= $st ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase text-on-surface-variant mb-1">Pendidikan Terakhir</label>
                    <select name="pendidikan" class="w-full px-4 py-2.5 border border-outline-variant rounded-xl focus:ring-2 focus:ring-primary outline-none text-sm bg-white">
                        <?php foreach (['Tidak/Belum Sekolah', 'SD/Sederajat', 'SMP/Sederajat', 'SMA/Sederajat', 'D1/D2/D3', 'S1/D4', 'S2', 'S3'] as $pd): ?>
                            <option value="<?= $pd ?>" <?= (isset($_POST['pendidikan']) && $_POST['pendidikan'] === $pd) ? 'selected' : '' ?>><?= $pd ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase text-on-surface-variant mb-1">Pekerjaan</label>
                    <input type="text" name="pekerjaan" value="<?= $_POST['pekerjaan'] ?? '' ?>" placeholder="Petani, Wiraswasta, PNS, dll"
                        class="w-full px-4 py-2.5 border border-outline-variant rounded-xl focus:ring-2 focus:ring-primary outline-none text-sm">
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase text-on-surface-variant mb-1">Kewarganegaraan</label>
                    <input type="text" name="kewarganegaraan" value="<?= $_POST['kewarganegaraan'] ?? 'WNI' ?>"
                        class="w-full px-4 py-2.5 border border-outline-variant rounded-xl focus:ring-2 focus:ring-primary outline-none text-sm">
                </div>
            </div>
        </div>

        <div>
            <h3 class="text-base font-bold text-primary mb-4 flex items-center gap-2 pb-2 border-b border-outline-variant">
                <span class="material-symbols-outlined">home</span> Alamat & Kontak
            </h3>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="sm:col-span-3">
                    <label class="block text-xs font-bold uppercase text-on-surface-variant mb-1">Alamat Domisili / Dusun</label>
                    <textarea name="alamat" rows="2" placeholder="Nama Jalan / Dusun"
                        class="w-full px-4 py-2.5 border border-outline-variant rounded-xl focus:ring-2 focus:ring-primary outline-none text-sm"><?= $_POST['alamat'] ?? '' ?></textarea>
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase text-on-surface-variant mb-1">RT</label>
                    <input type="text" name="rt" value="<?= $_POST['rt'] ?? '001' ?>" placeholder="001"
                        class="w-full px-4 py-2.5 border border-outline-variant rounded-xl focus:ring-2 focus:ring-primary outline-none text-sm">
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase text-on-surface-variant mb-1">RW</label>
                    <input type="text" name="rw" value="<?= $_POST['rw'] ?? '001' ?>" placeholder="001"
                        class="w-full px-4 py-2.5 border border-outline-variant rounded-xl focus:ring-2 focus:ring-primary outline-none text-sm">
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase text-on-surface-variant mb-1">Nomor Telepon / WA</label>
                    <input type="tel" name="telepon" value="<?= $_POST['telepon'] ?? '' ?>" placeholder="08xxxxxxxxxx"
                        class="w-full px-4 py-2.5 border border-outline-variant rounded-xl focus:ring-2 focus:ring-primary outline-none text-sm">
                </div>
            </div>
        </div>

        <div class="flex items-center justify-end gap-3 pt-4 border-t border-outline-variant">
            <a href="<?= base_url('admin/penduduk/index.php') ?>" class="px-5 py-2.5 rounded-xl border border-outline-variant font-bold text-sm hover:bg-surface-container-low transition-all">Batal</a>
            <button type="submit" class="bg-primary hover:bg-primary-container text-white px-6 py-2.5 rounded-xl font-bold text-sm shadow-md transition-all flex items-center gap-2">
                <span class="material-symbols-outlined text-base">save</span> Simpan Penduduk
            </button>
        </div>
    </form>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
