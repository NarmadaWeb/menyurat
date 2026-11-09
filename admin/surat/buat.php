<?php
$page_title = "Buat Template Surat";
require_once __DIR__ . '/../../includes/auth.php';
require_login();

$village = get_village_profile();
$mail_types = $pdo->query("SELECT * FROM jenis_surat ORDER BY nama ASC")->fetchAll();
$all_citizens = $pdo->query("SELECT id, nik, no_kk, nama_lengkap, jenis_kelamin, tempat_lahir, tanggal_lahir, agama, pekerjaan, alamat, rt, rw FROM penduduk ORDER BY nama_lengkap ASC")->fetchAll();

// Pre-fill if nik or pengajuan_id is passed
$selected_nik = isset($_GET['nik']) ? sanitize($_GET['nik']) : '';
$pengajuan_id = isset($_GET['pengajuan_id']) ? (int)$_GET['pengajuan_id'] : 0;
$initial_data = [
    'jenis_surat_id' => '',
    'nomor_surat' => '',
    'nik' => '',
    'nama_lengkap' => '',
    'jenis_kelamin' => 'Laki-laki',
    'tempat_lahir' => '',
    'tanggal_lahir' => '',
    'agama' => 'Islam',
    'pekerjaan' => '',
    'alamat' => '',
    'keperluan' => '',
    'keterangan_tambahan' => '',
    'tanggal_surat' => date('Y-m-d')
];

if ($selected_nik) {
    $stmt = $pdo->prepare("SELECT * FROM penduduk WHERE nik = ?");
    $stmt->execute([$selected_nik]);
    $c = $stmt->fetch();
    if ($c) {
        $initial_data['nik'] = $c['nik'];
        $initial_data['nama_lengkap'] = $c['nama_lengkap'];
        $initial_data['jenis_kelamin'] = $c['jenis_kelamin'];
        $initial_data['tempat_lahir'] = $c['tempat_lahir'];
        $initial_data['tanggal_lahir'] = $c['tanggal_lahir'];
        $initial_data['agama'] = $c['agama'];
        $initial_data['pekerjaan'] = $c['pekerjaan'];
        $initial_data['alamat'] = $c['alamat'] . ($c['rt'] ? ' RT ' . $c['rt'] . '/RW ' . $c['rw'] : '');
    }
} elseif ($pengajuan_id > 0) {
    $stmt = $pdo->prepare("SELECT r.*, mt.kode as mail_code FROM pengajuan_warga r LEFT JOIN jenis_surat mt ON r.jenis_surat_id = mt.id WHERE r.id = ?");
    $stmt->execute([$pengajuan_id]);
    $p = $stmt->fetch();
    if ($p) {
        $initial_data['jenis_surat_id'] = $p['jenis_surat_id'];
        $initial_data['nik'] = $p['nik'];
        $initial_data['nama_lengkap'] = $p['nama_lengkap'];
        $initial_data['alamat'] = $p['alamat'];
        $initial_data['keperluan'] = $p['deskripsi'];

        // check if citizen exists in DB for additional details
        $stmt_c = $pdo->prepare("SELECT * FROM penduduk WHERE nik = ?");
        $stmt_c->execute([$p['nik']]);
        $c = $stmt_c->fetch();
        if ($c) {
            $initial_data['jenis_kelamin'] = $c['jenis_kelamin'];
            $initial_data['tempat_lahir'] = $c['tempat_lahir'];
            $initial_data['tanggal_lahir'] = $c['tanggal_lahir'];
            $initial_data['agama'] = $c['agama'];
            $initial_data['pekerjaan'] = $c['pekerjaan'];
        }
    }
}

// Generate default nomor surat
if (empty($initial_data['nomor_surat'])) {
    $initial_data['nomor_surat'] = '470/' . date('m') . '/' . date('Y');
}

require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../includes/sidebar.php';
?>

<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-on-surface">Buat & Cetak Template Surat</h2>
            <p class="text-on-surface-variant text-sm">Pilih jenis surat, gunakan data kependudukan atau isi manual, lalu cetak surat resmi berkop desa.</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="<?= base_url('admin/pengajuan/index.php') ?>" class="border border-outline-variant px-4 py-2 rounded-xl text-sm font-semibold hover:bg-surface-container-low transition-all">
                Daftar Pengajuan
            </a>
        </div>
    </div>

    <!-- Quick Population Picker -->
    <div class="bg-primary/5 border border-primary/20 rounded-2xl p-5">
        <label class="block text-xs font-bold uppercase text-primary mb-2 flex items-center gap-1.5">
            <span class="material-symbols-outlined text-base">person_search</span> Pilih dari Data Penduduk (Auto-Fill Form)
        </label>
        <div class="grid grid-cols-1 sm:grid-cols-4 gap-3">
            <div class="sm:col-span-3">
                <select id="select-resident" class="w-full px-4 py-2.5 bg-white border border-outline-variant rounded-xl focus:ring-2 focus:ring-primary outline-none text-sm">
                    <option value="">-- Pilih Nama atau NIK Penduduk --</option>
                    <?php foreach ($all_citizens as $cit): ?>
                        <option value="<?= htmlspecialchars(json_encode($cit)) ?>" <?= $cit['nik'] === $initial_data['nik'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($cit['nama_lengkap']) ?> - NIK: <?= $cit['nik'] ?> (<?= $cit['alamat'] ?: 'Pendua' ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="button" onclick="applyResidentData()" class="bg-primary hover:bg-primary-container text-white px-4 py-2.5 rounded-xl font-bold text-sm transition-all flex items-center justify-center gap-1">
                <span class="material-symbols-outlined text-sm">download</span> Terapkan Data
            </button>
        </div>
    </div>

    <!-- Form Buat Surat -->
    <form action="<?= base_url('admin/surat/cetak.php') ?>" method="POST" target="_blank" class="bg-white rounded-2xl border border-outline-variant shadow-sm p-6 sm:p-8 space-y-6">
        <?php if ($pengajuan_id): ?>
            <input type="hidden" name="pengajuan_id" value="<?= $pengajuan_id ?>">
        <?php endif; ?>

        <div>
            <h3 class="text-base font-bold text-primary mb-4 flex items-center gap-2 pb-2 border-b border-outline-variant">
                <span class="material-symbols-outlined">description</span> 1. Format & Jenis Surat
            </h3>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="sm:col-span-2">
                    <label class="block text-xs font-bold uppercase text-on-surface-variant mb-1">Jenis Surat Resmi <span class="text-error">*</span></label>
                    <select name="jenis_surat_id" id="jenis_surat_id" required onchange="handleJenisSuratChange(this)" class="w-full px-4 py-2.5 border border-outline-variant rounded-xl focus:ring-2 focus:ring-primary outline-none text-sm bg-white">
                        <option value="">-- Pilih Jenis Template Surat --</option>
                        <?php foreach ($mail_types as $mt): ?>
                            <option value="<?= $mt['id'] ?>" data-kode="<?= $mt['kode'] ?>" data-nama="<?= htmlspecialchars($mt['nama']) ?>" <?= $mt['id'] == $initial_data['jenis_surat_id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($mt['nama']) ?> (<?= $mt['kode'] ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase text-on-surface-variant mb-1">Nomor Surat <span class="text-error">*</span></label>
                    <input type="text" name="nomor_surat" id="nomor_surat" required value="<?= htmlspecialchars($initial_data['nomor_surat']) ?>"
                        class="w-full px-4 py-2.5 border border-outline-variant rounded-xl focus:ring-2 focus:ring-primary outline-none text-sm font-mono">
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase text-on-surface-variant mb-1">Tanggal Surat <span class="text-error">*</span></label>
                    <input type="date" name="tanggal_surat" required value="<?= $initial_data['tanggal_surat'] ?>"
                        class="w-full px-4 py-2.5 border border-outline-variant rounded-xl focus:ring-2 focus:ring-primary outline-none text-sm">
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase text-on-surface-variant mb-1">Penandatangan <span class="text-error">*</span></label>
                    <input type="text" name="nama_pejabat" required value="<?= htmlspecialchars($village['nama_kades'] ?? 'Kepala Desa') ?>"
                        class="w-full px-4 py-2.5 border border-outline-variant rounded-xl focus:ring-2 focus:ring-primary outline-none text-sm">
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase text-on-surface-variant mb-1">Jabatan Penandatangan <span class="text-error">*</span></label>
                    <input type="text" name="jabatan_pejabat" required value="Kepala Desa Pendua"
                        class="w-full px-4 py-2.5 border border-outline-variant rounded-xl focus:ring-2 focus:ring-primary outline-none text-sm">
                </div>
            </div>
        </div>

        <div>
            <h3 class="text-base font-bold text-primary mb-4 flex items-center gap-2 pb-2 border-b border-outline-variant">
                <span class="material-symbols-outlined">person</span> 2. Data Pemohon / Penduduk
            </h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold uppercase text-on-surface-variant mb-1">NIK <span class="text-error">*</span></label>
                    <input type="text" name="nik" id="f_nik" required value="<?= htmlspecialchars($initial_data['nik']) ?>" placeholder="16 digit NIK"
                        class="w-full px-4 py-2.5 border border-outline-variant rounded-xl focus:ring-2 focus:ring-primary outline-none text-sm font-mono">
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase text-on-surface-variant mb-1">Nama Lengkap <span class="text-error">*</span></label>
                    <input type="text" name="nama_lengkap" id="f_nama" required value="<?= htmlspecialchars($initial_data['nama_lengkap']) ?>" placeholder="Nama lengkap"
                        class="w-full px-4 py-2.5 border border-outline-variant rounded-xl focus:ring-2 focus:ring-primary outline-none text-sm">
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase text-on-surface-variant mb-1">Jenis Kelamin</label>
                    <select name="jenis_kelamin" id="f_jk" class="w-full px-4 py-2.5 border border-outline-variant rounded-xl focus:ring-2 focus:ring-primary outline-none text-sm bg-white">
                        <option value="Laki-laki" <?= $initial_data['jenis_kelamin'] === 'Laki-laki' ? 'selected' : '' ?>>Laki-laki</option>
                        <option value="Perempuan" <?= $initial_data['jenis_kelamin'] === 'Perempuan' ? 'selected' : '' ?>>Perempuan</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase text-on-surface-variant mb-1">Tempat Lahir</label>
                    <input type="text" name="tempat_lahir" id="f_tempat_lahir" value="<?= htmlspecialchars($initial_data['tempat_lahir']) ?>" placeholder="Tempat Lahir"
                        class="w-full px-4 py-2.5 border border-outline-variant rounded-xl focus:ring-2 focus:ring-primary outline-none text-sm">
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase text-on-surface-variant mb-1">Tanggal Lahir</label>
                    <input type="date" name="tanggal_lahir" id="f_tanggal_lahir" value="<?= $initial_data['tanggal_lahir'] ?>"
                        class="w-full px-4 py-2.5 border border-outline-variant rounded-xl focus:ring-2 focus:ring-primary outline-none text-sm">
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase text-on-surface-variant mb-1">Agama</label>
                    <input type="text" name="agama" id="f_agama" value="<?= htmlspecialchars($initial_data['agama'] ?: 'Islam') ?>" placeholder="Agama"
                        class="w-full px-4 py-2.5 border border-outline-variant rounded-xl focus:ring-2 focus:ring-primary outline-none text-sm">
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase text-on-surface-variant mb-1">Pekerjaan</label>
                    <input type="text" name="pekerjaan" id="f_pekerjaan" value="<?= htmlspecialchars($initial_data['pekerjaan']) ?>" placeholder="Pekerjaan"
                        class="w-full px-4 py-2.5 border border-outline-variant rounded-xl focus:ring-2 focus:ring-primary outline-none text-sm">
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase text-on-surface-variant mb-1">Kewarganegaraan</label>
                    <input type="text" name="kewarganegaraan" id="f_kewarganegaraan" value="WNI"
                        class="w-full px-4 py-2.5 border border-outline-variant rounded-xl focus:ring-2 focus:ring-primary outline-none text-sm">
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-xs font-bold uppercase text-on-surface-variant mb-1">Alamat Tempat Tinggal</label>
                    <textarea name="alamat" id="f_alamat" rows="2" class="w-full px-4 py-2.5 border border-outline-variant rounded-xl focus:ring-2 focus:ring-primary outline-none text-sm"><?= htmlspecialchars($initial_data['alamat']) ?></textarea>
                </div>
            </div>
        </div>

        <div>
            <h3 class="text-base font-bold text-primary mb-4 flex items-center gap-2 pb-2 border-b border-outline-variant">
                <span class="material-symbols-outlined">edit_note</span> 3. Isi Keterangan Surat
            </h3>
            <div class="space-y-4">
                <div>
                    <label class="block text-xs font-bold uppercase text-on-surface-variant mb-1">Keperluan / Keterangan Surat <span class="text-error">*</span></label>
                    <textarea name="keperluan" id="f_keperluan" required rows="3" placeholder="Contoh: Mengurus persyaratan SKCK di Polres Lombok Utara / Membuka rekening bank / Memulai usaha warung kelontong..."
                        class="w-full px-4 py-2.5 border border-outline-variant rounded-xl focus:ring-2 focus:ring-primary outline-none text-sm"><?= htmlspecialchars($initial_data['keperluan']) ?></textarea>
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase text-on-surface-variant mb-1">Keterangan Khusus / Tambahan (Opsional)</label>
                    <textarea name="keterangan_tambahan" id="f_keterangan_tambahan" rows="2" placeholder="Informasi tambahan spesifik jika diperlukan (misal nama usaha, bidang usaha, dsb)..."
                        class="w-full px-4 py-2.5 border border-outline-variant rounded-xl focus:ring-2 focus:ring-primary outline-none text-sm"></textarea>
                </div>
                <div class="flex items-center gap-2 pt-2">
                    <input type="checkbox" name="simpan_surat_keluar" id="simpan_surat_keluar" value="1" checked class="w-4 h-4 text-primary rounded border-outline-variant focus:ring-primary">
                    <label for="simpan_surat_keluar" class="text-xs text-on-surface-variant font-medium">Otomatis catat surat ini ke buku agenda <b>Surat Keluar</b></label>
                </div>
            </div>
        </div>

        <div class="flex flex-col sm:flex-row items-center justify-between gap-4 pt-4 border-t border-outline-variant">
            <span class="text-xs text-on-surface-variant italic">Surat akan dibuka di tab baru siap cetak (Ctrl+P / Simpan PDF).</span>
            <div class="flex items-center gap-3 w-full sm:w-auto">
                <button type="submit" class="w-full sm:w-auto bg-primary hover:bg-primary-container text-white px-8 py-3 rounded-xl font-bold text-sm shadow-md transition-all flex items-center justify-center gap-2">
                    <span class="material-symbols-outlined text-lg">print</span> Pratinjau & Cetak Surat
                </button>
            </div>
        </div>
    </form>
</div>

<script>
function applyResidentData() {
    const sel = document.getElementById('select-resident');
    if (!sel.value) return;
    try {
        const data = JSON.parse(sel.value);
        document.getElementById('f_nik').value = data.nik || '';
        document.getElementById('f_nama').value = data.nama_lengkap || '';
        document.getElementById('f_jk').value = data.jenis_kelamin || 'Laki-laki';
        document.getElementById('f_tempat_lahir').value = data.tempat_lahir || '';
        document.getElementById('f_tanggal_lahir').value = data.tanggal_lahir || '';
        document.getElementById('f_agama').value = data.agama || 'Islam';
        document.getElementById('f_pekerjaan').value = data.pekerjaan || '';
        document.getElementById('f_alamat').value = (data.alamat || '') + (data.rt ? ' RT ' + data.rt + '/RW ' + data.rw : '');
    } catch(e) {
        console.error(e);
    }
}

function handleJenisSuratChange(select) {
    const opt = select.options[select.selectedIndex];
    const kode = opt.getAttribute('data-kode');
    const nama = opt.getAttribute('data-nama');

    // Auto populate default keperluan
    const defaultTexts = {
        'SKU': 'Surat Keterangan Usaha ini diberikan untuk keperluan mengajukan permodalan usaha / legalitas usaha mikro di lingkungan Desa Pendua.',
        'SKD': 'Surat Keterangan Domisili ini diberikan untuk menerangkan bahwa yang bersangkutan benar berdomisili di Desa Pendua.',
        'SKTM': 'Surat Keterangan Tidak Mampu ini diberikan untuk keperluan permohonan beasiswa / bantuan sosial / keringanan biaya pengobatan.',
        'SKBM': 'Surat Keterangan ini menerangkan bahwa yang bersangkutan sampai saat ini berstatus Belum Pernah Menikah.',
        'SKCK': 'Surat Pengantar ini diberikan sebagai kelengkapan berkas penerbitan Surat Keterangan Catatan Kepolisian (SKCK) di Polsek/Polres.',
        'SKM': 'Surat Keterangan Kematian ini diterbitkan sebagai bukti sah pelaporan kematian warga di lingkungan desa.',
        'SKKB': 'Surat Keterangan Kelakuan Baik ini diberikan untuk keperluan kelengkapan administrasi pekerjaan/pendidikan.',
        'SKP': 'Surat Keterangan Pindah ini diberikan sebagai pengantar pindah domisili penduduk antar daerah.'
    };

    if (kode && defaultTexts[kode] && !document.getElementById('f_keperluan').value) {
        document.getElementById('f_keperluan').value = defaultTexts[kode];
    }
}
</script>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
