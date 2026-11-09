<?php
require_once __DIR__ . '/../../includes/auth.php';
require_login();

$village = get_village_profile();

if ($_SERVER['REQUEST_METHOD'] !== 'POST' && !isset($_GET['id'])) {
    die("Akses tidak sah.");
}

$jenis_surat_id = (int)($_POST['jenis_surat_id'] ?? 0);
$nomor_surat = sanitize($_POST['nomor_surat'] ?? '470/01/' . date('Y'));
$tanggal_surat = sanitize($_POST['tanggal_surat'] ?? date('Y-m-d'));
$nama_pejabat = sanitize($_POST['nama_pejabat'] ?? $village['nama_kades']);
$jabatan_pejabat = sanitize($_POST['jabatan_pejabat'] ?? 'Kepala Desa Pendua');
$nik = sanitize($_POST['nik'] ?? '');
$nama_lengkap = sanitize($_POST['nama_lengkap'] ?? '');
$jenis_kelamin = sanitize($_POST['jenis_kelamin'] ?? 'Laki-laki');
$tempat_lahir = sanitize($_POST['tempat_lahir'] ?? '');
$tanggal_lahir = sanitize($_POST['tanggal_lahir'] ?? '');
$agama = sanitize($_POST['agama'] ?? 'Islam');
$pekerjaan = sanitize($_POST['pekerjaan'] ?? '-');
$kewarganegaraan = sanitize($_POST['kewarganegaraan'] ?? 'WNI');
$alamat = sanitize($_POST['alamat'] ?? '-');
$keperluan = sanitize($_POST['keperluan'] ?? '-');
$keterangan_tambahan = sanitize($_POST['keterangan_tambahan'] ?? '');

$mail_type = null;
if ($jenis_surat_id) {
    $stmt = $pdo->prepare("SELECT * FROM jenis_surat WHERE id = ?");
    $stmt->execute([$jenis_surat_id]);
    $mail_type = $stmt->fetch();
}

$nama_surat = $mail_type ? strtoupper($mail_type['nama']) : 'SURAT KETERANGAN';

// Save to surat_keluar if requested
if (!empty($_POST['simpan_surat_keluar'])) {
    try {
        $check_sk = $pdo->prepare("SELECT id FROM surat_keluar WHERE nomor = ?");
        $check_sk->execute([$nomor_surat]);
        if (!$check_sk->fetch()) {
            $stmt_sk = $pdo->prepare("INSERT INTO surat_keluar (nomor, tanggal_dikirim, penerima, perihal, deskripsi) VALUES (?, ?, ?, ?, ?)");
            $stmt_sk->execute([
                $nomor_surat,
                $tanggal_surat,
                $nama_lengkap . " (NIK: $nik)",
                $mail_type ? $mail_type['nama'] : 'Surat Keterangan',
                $keperluan
            ]);
        }
    } catch (PDOException $e) {
        // Continue printing even if logging fails
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $nama_surat ?> - <?= htmlspecialchars($nama_lengkap) ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Times+New+Roman&family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 12pt;
            line-height: 1.5;
            color: #000;
            background-color: #f1f5f9;
            padding: 20px 0;
        }
        .page {
            background: #fff;
            width: 210mm;
            min-height: 297mm;
            padding: 20mm 20mm 20mm 25mm;
            margin: 0 auto;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            position: relative;
        }
        .no-print-bar {
            width: 210mm;
            margin: 0 auto 15px auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #006948;
            color: #fff;
            padding: 12px 20px;
            border-radius: 12px;
            font-family: 'Inter', sans-serif;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);
        }
        .btn-print {
            background: #fff;
            color: #006948;
            font-weight: 700;
            border: none;
            padding: 8px 20px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 14px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            text-decoration: none;
        }
        .btn-print:hover {
            background: #e6f4ea;
        }
        .btn-back {
            color: #fff;
            text-decoration: underline;
            font-size: 14px;
        }

        /* Kop Surat */
        .kop-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 2px;
        }
        .kop-logo {
            width: 90px;
            text-align: center;
            vertical-align: middle;
        }
        .kop-logo img {
            width: 80px;
            max-height: 85px;
            object-contain: contain;
        }
        .kop-text {
            text-align: center;
            vertical-align: middle;
        }
        .kop-text h4 {
            font-size: 13pt;
            font-weight: bold;
            margin: 0;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .kop-text h3 {
            font-size: 15pt;
            font-weight: bold;
            margin: 0;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .kop-text p {
            font-size: 10pt;
            margin: 3px 0 0 0;
            font-style: italic;
        }
        .line-double {
            border-top: 3px solid #000;
            border-bottom: 1px solid #000;
            height: 4px;
            margin: 4px 0 20px 0;
        }

        /* Title Surat */
        .surat-header {
            text-align: center;
            margin-bottom: 22px;
        }
        .surat-header h2 {
            font-size: 14pt;
            font-weight: bold;
            text-decoration: underline;
            letter-spacing: 1px;
            margin-bottom: 2px;
        }
        .surat-header p {
            font-size: 11pt;
            margin: 0;
        }

        /* Content */
        .content {
            font-size: 12pt;
            text-align: justify;
            line-height: 1.6;
        }
        .content p {
            margin-bottom: 12px;
            text-indent: 30px;
        }
        .content p.no-indent {
            text-indent: 0;
        }
        .data-table {
            width: 100%;
            margin: 10px 0 16px 20px;
            border-collapse: collapse;
        }
        .data-table td {
            padding: 3px 4px;
            vertical-align: top;
            font-size: 12pt;
        }
        .data-table td.label {
            width: 180px;
        }
        .data-table td.colon {
            width: 15px;
            text-align: center;
        }

        /* Tanda Tangan */
        .ttd-container {
            width: 100%;
            margin-top: 30px;
            display: flex;
            justify-content: flex-end;
        }
        .ttd-box {
            width: 250px;
            text-align: center;
        }
        .ttd-space {
            height: 75px;
        }
        .ttd-name {
            font-weight: bold;
            text-decoration: underline;
        }

        @media print {
            body {
                background: #fff;
                padding: 0;
            }
            .no-print-bar {
                display: none !important;
            }
            .page {
                box-shadow: none;
                margin: 0;
                width: 100%;
                min-height: auto;
                padding: 15mm 15mm 15mm 20mm;
            }
        }
    </style>
</head>
<body>

<div class="no-print-bar">
    <div>
        <strong>Format Cetak Resmi A4</strong> &bull; <?= htmlspecialchars($nama_surat) ?>
    </div>
    <div style="display: flex; align-items: center; gap: 15px;">
        <button class="btn-print" onclick="window.print()">
            🖨️ Cetak Surat Sekarang
        </button>
        <a href="javascript:window.close()" class="btn-back">Tutup</a>
    </div>
</div>

<div class="page">
    <!-- KOP SURAT -->
    <table class="kop-table">
        <tr>
            <td class="kop-logo">
                <?php if ($village['logo']): ?>
                    <img src="<?= base_url($village['logo']) ?>" alt="Logo">
                <?php else: ?>
                    <img src="<?= base_url('assets/img/logo_placeholder.png') ?>" onerror="this.style.display='none'" alt="Logo">
                <?php endif; ?>
            </td>
            <td class="kop-text">
                <h4>PEMERINTAH KABUPATEN LOMBOK UTARA</h4>
                <h4>KECAMATAN KAYANGAN</h4>
                <h3>PEMERINTAH DESA <?= strtoupper($village['nama']) ?></h3>
                <p><?= $village['alamat'] ?> | Telp: <?= $village['telepon'] ?> | Email: <?= $village['email'] ?></p>
            </td>
        </tr>
    </table>
    <div class="line-double"></div>

    <!-- JUDUL SURAT -->
    <div class="surat-header">
        <h2><?= $nama_surat ?></h2>
        <p>Nomor : <?= htmlspecialchars($nomor_surat) ?></p>
    </div>

    <!-- ISI SURAT -->
    <div class="content">
        <p>Yang bertanda tangan di bawah ini Kepala Desa <?= htmlspecialchars($village['nama']) ?>, Kecamatan Kayangan, Kabupaten Lombok Utara, menerangkan dengan sesungguhnya bahwa:</p>

        <table class="data-table">
            <tr>
                <td class="label">Nama Lengkap</td>
                <td class="colon">:</td>
                <td><strong><?= htmlspecialchars($nama_lengkap) ?></strong></td>
            </tr>
            <tr>
                <td class="label">NIK</td>
                <td class="colon">:</td>
                <td><?= htmlspecialchars($nik) ?></td>
            </tr>
            <tr>
                <td class="label">Tempat / Tanggal Lahir</td>
                <td class="colon">:</td>
                <td><?= htmlspecialchars($tempat_lahir) ?><?= $tempat_lahir && $tanggal_lahir ? ', ' : '' ?><?= format_date($tanggal_lahir) ?></td>
            </tr>
            <tr>
                <td class="label">Jenis Kelamin</td>
                <td class="colon">:</td>
                <td><?= htmlspecialchars($jenis_kelamin) ?></td>
            </tr>
            <tr>
                <td class="label">Kewarganegaraan / Agama</td>
                <td class="colon">:</td>
                <td><?= htmlspecialchars($kewarganegaraan) ?> / <?= htmlspecialchars($agama) ?></td>
            </tr>
            <tr>
                <td class="label">Pekerjaan</td>
                <td class="colon">:</td>
                <td><?= htmlspecialchars($pekerjaan) ?></td>
            </tr>
            <tr>
                <td class="label">Alamat / Tempat Tinggal</td>
                <td class="colon">:</td>
                <td><?= htmlspecialchars($alamat) ?></td>
            </tr>
        </table>

        <p>Orang tersebut di atas adalah benar-benar warga yang bertempat tinggal di Desa <?= htmlspecialchars($village['nama']) ?> dan berdasarkan catatan yang ada pada kami memiliki riwayat/keperluan sebagai berikut:</p>

        <p class="no-indent" style="padding-left: 20px; font-style: italic; margin-bottom: 12px;">
            "<?= nl2br(htmlspecialchars($keperluan)) ?>"
        </p>

        <?php if (!empty($keterangan_tambahan)): ?>
            <p><?= nl2br(htmlspecialchars($keterangan_tambahan)) ?></p>
        <?php endif; ?>

        <p>Demikian surat keterangan ini kami buat dengan sebenarnya dan agar dapat dipergunakan sebagaimana mestinya oleh yang berkepentingan.</p>
    </div>

    <!-- TANDA TANGAN -->
    <div class="ttd-container">
        <div class="ttd-box">
            <p>Pendua, <?= format_date($tanggal_surat) ?></p>
            <p><?= htmlspecialchars($jabatan_pejabat) ?>,</p>
            <div class="ttd-space"></div>
            <p class="ttd-name"><?= htmlspecialchars($nama_pejabat) ?></p>
            <?php if (!empty($village['nip_kades'])): ?>
                <p style="font-size: 10pt;">NIP. <?= htmlspecialchars($village['nip_kades']) ?></p>
            <?php endif; ?>
        </div>
    </div>
</div>

</body>
</html>
