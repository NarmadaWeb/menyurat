<?php
$page_title = "Pengaturan Desa";
require_once __DIR__ . '/../../includes/auth.php';
require_role('Admin');

$village = get_village_profile();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitize($_POST['name']);
    $address = sanitize($_POST['address']);
    $phone = sanitize($_POST['phone']);
    $email = sanitize($_POST['email']);
    $kades_name = sanitize($_POST['kades_name']);
    $kades_nip = sanitize($_POST['kades_nip']);

    $logo = $village['logo'];
    if (isset($_FILES['logo']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = __DIR__ . '/../../assets/img/';
        if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);

        $filename = 'logo_' . time() . '_' . basename($_FILES['logo']['name']);
        $target_file = $upload_dir . $filename;

        if (move_uploaded_file($_FILES['logo']['tmp_name'], $target_file)) {
            $logo = 'assets/img/' . $filename;
        }
    }

    try {
        $stmt = $pdo->prepare("UPDATE profil_desa SET nama = ?, alamat = ?, telepon = ?, email = ?, nama_kades = ?, nip_kades = ?, logo = ? WHERE id = 1");
        $stmt->execute([$name, $address, $phone, $email, $kades_name, $kades_nip, $logo]);

        redirect(base_url('admin/settings/desa.php'), 'Profil desa berhasil diperbarui.');
    } catch (PDOException $e) {
        $error = "Gagal memperbarui profil: " . $e->getMessage();
    }
}

require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../includes/sidebar.php';
?>

<div class="max-w-4xl">
    <form action="" method="POST" enctype="multipart/form-data" class="space-y-8">
        <?php if (isset($error)): ?>
            <div class="p-4 bg-red-100 text-red-800 rounded-xl text-sm"><?= $error ?></div>
        <?php endif; ?>

        <div class="bg-white rounded-2xl shadow-sm border border-outline-variant p-8 space-y-6">
            <h3 class="text-lg font-bold flex items-center gap-2">
                <span class="material-symbols-outlined text-primary">account_balance</span>
                Identitas Desa
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="md:col-span-2">
                    <label for="name" class="block text-sm font-semibold mb-2">Nama Desa</label>
                    <input type="text" name="name" id="name" required value="<?= $village['nama'] ?>"
                        class="w-full px-4 py-3 border border-outline-variant rounded-xl focus:ring-2 focus:ring-primary outline-none">
                </div>
                <div class="md:col-span-2">
                    <label for="address" class="block text-sm font-semibold mb-2">Alamat Kantor Desa</label>
                    <textarea name="address" id="address" rows="3" required
                        class="w-full px-4 py-3 border border-outline-variant rounded-xl focus:ring-2 focus:ring-primary outline-none"><?= $village['alamat'] ?></textarea>
                </div>
                <div>
                    <label for="phone" class="block text-sm font-semibold mb-2">WhatsApp Layanan</label>
                    <input type="text" name="phone" id="phone" value="<?= $village['telepon'] ?>"
                        class="w-full px-4 py-3 border border-outline-variant rounded-xl focus:ring-2 focus:ring-primary outline-none">
                </div>
                <div>
                    <label for="email" class="block text-sm font-semibold mb-2">Email Resmi</label>
                    <input type="email" name="email" id="email" value="<?= $village['email'] ?>"
                        class="w-full px-4 py-3 border border-outline-variant rounded-xl focus:ring-2 focus:ring-primary outline-none">
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-outline-variant p-8 space-y-6">
            <h3 class="text-lg font-bold flex items-center gap-2">
                <span class="material-symbols-outlined text-primary">person</span>
                Pejabat (Kepala Desa)
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="kades_name" class="block text-sm font-semibold mb-2">Nama Kepala Desa</label>
                    <input type="text" name="kades_name" id="kades_name" value="<?= $village['nama_kades'] ?>"
                        class="w-full px-4 py-3 border border-outline-variant rounded-xl focus:ring-2 focus:ring-primary outline-none">
                </div>
                <div>
                    <label for="kades_nip" class="block text-sm font-semibold mb-2">NIP (Jika Ada)</label>
                    <input type="text" name="kades_nip" id="kades_nip" value="<?= $village['nip_kades'] ?>"
                        class="w-full px-4 py-3 border border-outline-variant rounded-xl focus:ring-2 focus:ring-primary outline-none">
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-outline-variant p-8 space-y-6">
            <h3 class="text-lg font-bold flex items-center gap-2">
                <span class="material-symbols-outlined text-primary">image</span>
                Logo Instansi
            </h3>

            <div class="flex items-center gap-8">
                <div class="w-32 h-32 rounded-full border border-outline-variant overflow-hidden flex items-center justify-center bg-surface-container-low">
                    <?php if ($village['logo']): ?>
                        <img src="<?= base_url($village['logo']) ?>" class="w-full h-full object-contain">
                    <?php else: ?>
                        <span class="material-symbols-outlined text-4xl text-on-surface-variant">image</span>
                    <?php endif; ?>
                </div>
                <div class="flex-1">
                    <label class="block text-sm font-semibold mb-2">Ubah Logo</label>
                    <input type="file" name="logo" accept="image/*"
                        class="w-full text-sm text-on-surface-variant file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-primary file:text-white hover:file:bg-primary-container cursor-pointer">
                    <p class="text-xs text-on-surface-variant mt-2">Format: PNG, JPG (Rekomendasi Transparan, 512x512px)</p>
                </div>
            </div>
        </div>

        <div class="flex justify-end gap-4">
            <button type="submit" class="bg-primary text-white px-10 py-4 rounded-2xl font-bold hover:bg-primary-container transition-all shadow-lg active:scale-95 flex items-center gap-2">
                <span class="material-symbols-outlined">save</span>
                Simpan Perubahan
            </button>
        </div>
    </form>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
