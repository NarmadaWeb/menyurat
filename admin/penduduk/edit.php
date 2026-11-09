<?php
$page_title = "Edit Data Penduduk";
require_once __DIR__ . '/../../includes/auth.php';
require_login();

$id = (int)($_GET['id'] ?? 0);
$stmt = $pdo->prepare("SELECT * FROM penduduk WHERE id = ?");
$stmt->execute([$id]);
$citizen = $stmt->fetch();

if (!$citizen) {
    redirect(base_url('admin/penduduk/index.php'), 'Data penduduk tidak ditemukan.', 'error');
}

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

    // Check duplicate NIK for other records
    $check = $pdo->prepare("SELECT id FROM penduduk WHERE nik = ? AND id != ?");
    $check->execute([$nik, $id]);
    if ($check->fetch()) {
        $errors[] = "NIK '$nik' sudah digunakan oleh penduduk lain.";
    }

    if (empty($errors)) {
        try {
            $update = $pdo->prepare("UPDATE penduduk SET nik = ?, no_kk = ?, nama_lengkap = ?, jenis_kelamin = ?, tempat_lahir = ?, tanggal_lahir = ?, agama = ?, pendidikan = ?, pekerjaan = ?, status_perkawinan = ?, kewarganegaraan = ?, alamat = ?, rt = ?, rw = ?, telepon = ? WHERE id = ?");
            $update->execute([
                $nik, $no_kk, $nama_lengkap, $jenis_kelamin, $tempat_lahir,
                $tanggal_lahir, $agama, $pendidikan, $pekerjaan, $status_perkawinan,
                $kewarganegaraan, $alamat, $rt, $rw, $telepon, $id
            ]);

            redirect(base_url('admin/penduduk/detail.php?id=' . $id), "Data penduduk $nama_lengkap berhasil diperbarui.");
        } catch (PDOException $e) {
            $errors[] = "Gagal memperbarui data: " . $e->getMessage();
        }
    }
}

require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../includes/sidebar.php';
?>

<div class="max-w-4xl mx-auto">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="text-2xl font-bold text-on-surface">Edit Data Penduduk</h2>
            <p class="text-on-surface-variant text-sm">Perbarui informasi kependudukan warga desa.</p>
        </div>
        <a href="<?= base_url('admin/penduduk/detail.php?id=' . $id) ?>" class="border border-outline-variant px-4 py-2 rounded-xl text-sm font-semibold hover:bg-surface-container-low transition-all flex items-center gap-1">
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
                    <label class="block text-xs font-bold uppercase text-on-surface-variant mb-1">NIK <span class="text-error">*</span></label>
                    <input type="text" name="nik" maxlength="20" required value="<?= htmlspecialchars($citizen['nik']) ?>"
                        class="w-full px-4 py-2.5 border border-outline-variant rounded-xl focus:ring-2 focus:ring-primary outline-none text-sm font-mono">
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase text-on-surface-variant mb-1">Nomor Kartu Keluarga (No. KK)</label>
                    <input type="text" name="no_kk" maxlength="20" value="<?= htmlspecialchars($citizen['no_kk'] ?? '') ?>"
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
                    <input type="text" name="nama_lengkap" required value="<?= htmlspecialchars($citizen['nama_lengkap']) ?>"
                        class="w-full px-4 py-2.5 border border-outline-variant rounded-xl focus:ring-2 focus:ring-primary outline-none text-sm">
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase text-on-surface-variant mb-1">Jenis Kelamin <span class="text-error">*</span></label>
                    <select name="jenis_kelamin" required class="w-full px-4 py-2.5 border border-outline-variant rounded-xl focus:ring-2 focus:ring-primary outline-none text-sm bg-white">
                        <option value="Laki-laki" <?= $citizen['jenis_kelamin'] === 'Laki-laki' ? 'selected' : '' ?>>Laki-laki</option>
                        <option value="Perempuan" <?= $citizen['jenis_kelamin'] === 'Perempuan' ? 'selected' : '' ?>>Perempuan</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase text-on-surface-variant mb-1">Agama</label>
                    <select name="agama" class="w-full px-4 py-2.5 border border-outline-variant rounded-xl focus:ring-2 focus:ring-primary outline-none text-sm bg-white">
                        <?php foreach (['Islam', 'Kristen', 'Katolik', 'Hindu', 'Buddha', 'Khonghucu'] as $ag): ?>
                            <option value="<?= $ag ?>" <?= $citizen['agama'] === $ag ? 'selected' : '' ?>><?= $ag ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase text-on-surface-variant mb-1">Tempat Lahir</label>
                    <input type="text" name="tempat_lahir" value="<?= htmlspecialchars($citizen['tempat_lahir'] ?? '') ?>"
                        class="w-full px-4 py-2.5 border border-outline-variant rounded-xl focus:ring-2 focus:ring-primary outline-none text-sm">
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase text-on-surface-variant mb-1">Tanggal Lahir</label>
                    <input type="date" name="tanggal_lahir" value="<?= $citizen['tanggal_lahir'] ?>"
                        class="w-full px-4 py-2.5 border border-outline-variant rounded-xl focus:ring-2 focus:ring-primary outline-none text-sm">
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase text-on-surface-variant mb-1">Status Perkawinan</label>
                    <select name="status_perkawinan" class="w-full px-4 py-2.5 border border-outline-variant rounded-xl focus:ring-2 focus:ring-primary outline-none text-sm bg-white">
                        <?php foreach (['Belum Kawin', 'Kawin', 'Cerai Hidup', 'Cerai Mati'] as $st): ?>
                            <option value="<?= $st ?>" <?= $citizen['status_perkawinan'] === $st ? 'selected' : '' ?>><?= $st ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase text-on-surface-variant mb-1">Pendidikan Terakhir</label>
                    <select name="pendidikan" class="w-full px-4 py-2.5 border border-outline-variant rounded-xl focus:ring-2 focus:ring-primary outline-none text-sm bg-white">
                        <?php foreach (['Tidak/Belum Sekolah', 'SD/Sederajat', 'SMP/Sederajat', 'SMA/Sederajat', 'D1/D2/D3', 'S1/D4', 'S2', 'S3'] as $pd): ?>
                            <option value="<?= $pd ?>" <?= $citizen['pendidikan'] === $pd ? 'selected' : '' ?>><?= $pd ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase text-on-surface-variant mb-1">Pekerjaan</label>
                    <input type="text" name="pekerjaan" value="<?= htmlspecialchars($citizen['pekerjaan'] ?? '') ?>"
                        class="w-full px-4 py-2.5 border border-outline-variant rounded-xl focus:ring-2 focus:ring-primary outline-none text-sm">
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase text-on-surface-variant mb-1">Kewarganegaraan</label>
                    <input type="text" name="kewarganegaraan" value="<?= htmlspecialchars($citizen['kewarganegaraan'] ?? 'WNI') ?>"
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
                    <textarea name="alamat" rows="2"
                        class="w-full px-4 py-2.5 border border-outline-variant rounded-xl focus:ring-2 focus:ring-primary outline-none text-sm"><?= htmlspecialchars($citizen['alamat'] ?? '') ?></textarea>
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase text-on-surface-variant mb-1">RT</label>
                    <input type="text" name="rt" value="<?= htmlspecialchars($citizen['rt'] ?? '001') ?>"
                        class="w-full px-4 py-2.5 border border-outline-variant rounded-xl focus:ring-2 focus:ring-primary outline-none text-sm">
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase text-on-surface-variant mb-1">RW</label>
                    <input type="text" name="rw" value="<?= htmlspecialchars($citizen['rw'] ?? '001') ?>"
                        class="w-full px-4 py-2.5 border border-outline-variant rounded-xl focus:ring-2 focus:ring-primary outline-none text-sm">
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase text-on-surface-variant mb-1">Nomor Telepon / WA</label>
                    <input type="tel" name="telepon" value="<?= htmlspecialchars($citizen['telepon'] ?? '') ?>"
                        class="w-full px-4 py-2.5 border border-outline-variant rounded-xl focus:ring-2 focus:ring-primary outline-none text-sm">
                </div>
            </div>
        </div>

        <div class="flex items-center justify-end gap-3 pt-4 border-t border-outline-variant">
            <a href="<?= base_url('admin/penduduk/detail.php?id=' . $id) ?>" class="px-5 py-2.5 rounded-xl border border-outline-variant font-bold text-sm hover:bg-surface-container-low transition-all">Batal</a>
            <button type="submit" class="bg-primary hover:bg-primary-container text-white px-6 py-2.5 rounded-xl font-bold text-sm shadow-md transition-all flex items-center gap-2">
                <span class="material-symbols-outlined text-base">save</span> Simpan Perubahan
            </button>
        </div>
    </form>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
