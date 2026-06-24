<?php
$page_title = "Dashboard";
require_once __DIR__ . '/../includes/auth.php';
require_login();

// Fetch statistics
$total_surat_masuk = $pdo->query("SELECT COUNT(*) FROM surat_masuk")->fetchColumn();
$total_surat_keluar = $pdo->query("SELECT COUNT(*) FROM surat_keluar")->fetchColumn();
$total_pengajuan = $pdo->query("SELECT COUNT(*) FROM pengajuan_warga")->fetchColumn();
$total_pengajuan_baru = $pdo->query("SELECT COUNT(*) FROM pengajuan_warga WHERE status = 'baru'")->fetchColumn();

// Fetch recent 5 citizen requests
$recent_requests = $pdo->query("SELECT r.*, mt.nama as mail_type_name FROM pengajuan_warga r LEFT JOIN jenis_surat mt ON r.jenis_surat_id = mt.id ORDER BY r.dibuat_pada DESC LIMIT 5")->fetchAll();

// Fetch recent 5 activity logs
$activity_logs = $pdo->query("SELECT l.*, u.nama_lengkap FROM log_aktivitas l LEFT JOIN pengguna u ON l.pengguna_id = u.id ORDER BY l.dibuat_pada DESC LIMIT 5")->fetchAll();

require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/sidebar.php';
?>

<!-- Statistics Cards -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <!-- Card 1 -->
    <div class="bg-white p-6 rounded-2xl border border-outline-variant shadow-sm flex items-center gap-4">
        <div class="w-12 h-12 rounded-xl bg-primary/10 flex items-center justify-center text-primary">
            <span class="material-symbols-outlined text-2xl">mail</span>
        </div>
        <div>
            <p class="text-xs text-on-surface-variant font-bold uppercase tracking-wider">Surat Masuk</p>
            <h3 class="text-2xl font-bold text-on-surface mt-1"><?= $total_surat_masuk ?></h3>
        </div>
    </div>
    
    <!-- Card 2 -->
    <div class="bg-white p-6 rounded-2xl border border-outline-variant shadow-sm flex items-center gap-4">
        <div class="w-12 h-12 rounded-xl bg-secondary/10 flex items-center justify-center text-secondary">
            <span class="material-symbols-outlined text-2xl">send</span>
        </div>
        <div>
            <p class="text-xs text-on-surface-variant font-bold uppercase tracking-wider">Surat Keluar</p>
            <h3 class="text-2xl font-bold text-on-surface mt-1"><?= $total_surat_keluar ?></h3>
        </div>
    </div>

    <!-- Card 3 -->
    <div class="bg-white p-6 rounded-2xl border border-outline-variant shadow-sm flex items-center gap-4">
        <div class="w-12 h-12 rounded-xl bg-blue-50 flex items-center justify-center text-blue-700">
            <span class="material-symbols-outlined text-2xl">groups</span>
        </div>
        <div>
            <p class="text-xs text-on-surface-variant font-bold uppercase tracking-wider">Pengajuan Warga</p>
            <h3 class="text-2xl font-bold text-on-surface mt-1"><?= $total_pengajuan ?></h3>
        </div>
    </div>

    <!-- Card 4 -->
    <div class="bg-white p-6 rounded-2xl border border-outline-variant shadow-sm flex items-center gap-4">
        <div class="w-12 h-12 rounded-xl bg-orange-50 flex items-center justify-center text-orange-700">
            <span class="material-symbols-outlined text-2xl">pending_actions</span>
        </div>
        <div>
            <p class="text-xs text-on-surface-variant font-bold uppercase tracking-wider">Pengajuan Pending</p>
            <h3 class="text-2xl font-bold text-orange-700 mt-1"><?= $total_pengajuan_baru ?></h3>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    <!-- Recent Requests -->
    <div class="lg:col-span-2 space-y-6">
        <div class="bg-white rounded-2xl border border-outline-variant shadow-sm overflow-hidden">
            <div class="p-6 border-b border-outline-variant flex justify-between items-center">
                <h3 class="font-bold text-on-surface">Pengajuan Warga Terbaru</h3>
                <a href="<?= base_url('admin/pengajuan/index.php') ?>" class="text-xs font-bold text-primary hover:underline">Lihat Semua</a>
            </div>
            <div class="divide-y divide-outline-variant">
                <?php if (empty($recent_requests)): ?>
                    <p class="p-6 text-sm text-on-surface-variant text-center">Belum ada pengajuan masuk.</p>
                <?php endif; ?>
                <?php foreach ($recent_requests as $req): ?>
                    <div class="p-6 flex items-center justify-between hover:bg-surface-container-low transition-colors">
                        <div>
                            <p class="font-bold text-sm text-on-surface"><?= $req['nama_lengkap'] ?></p>
                            <p class="text-xs text-on-surface-variant mt-1"><?= $req['mail_type_name'] ?> &bull; <?= format_date($req['dibuat_pada']) ?></p>
                        </div>
                        <div class="flex items-center gap-3">
                            <?php
                            $classes = [
                                'baru' => 'bg-blue-100 text-blue-700',
                                'diproses' => 'bg-yellow-100 text-yellow-800',
                                'selesai' => 'bg-green-100 text-green-800',
                                'ditolak' => 'bg-red-100 text-red-800'
                            ];
                            $labels = ['baru' => 'Baru', 'diproses' => 'Proses', 'selesai' => 'Selesai', 'ditolak' => 'Ditolak'];
                            ?>
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase <?= $classes[$req['status']] ?>">
                                <?= $labels[$req['status']] ?>
                            </span>
                            <a href="<?= base_url('admin/pengajuan/detail.php?id=' . $req['id']) ?>" class="p-2 text-primary hover:bg-primary/5 rounded-lg">
                                <span class="material-symbols-outlined text-lg">arrow_forward</span>
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- Recent Activities -->
    <div class="lg:col-span-1">
        <div class="bg-white rounded-2xl border border-outline-variant shadow-sm p-6">
            <h3 class="font-bold text-on-surface mb-6">Log Aktivitas Terbaru</h3>
            <div class="space-y-6">
                <?php if (empty($activity_logs)): ?>
                    <p class="text-sm text-on-surface-variant text-center py-4">Belum ada aktivitas tercatat.</p>
                <?php endif; ?>
                <?php foreach ($activity_logs as $log): ?>
                    <div class="flex gap-4">
                        <div class="w-8 h-8 rounded-full bg-primary/10 flex items-center justify-center text-primary shrink-0">
                            <span class="material-symbols-outlined text-sm">history</span>
                        </div>
                        <div class="overflow-hidden">
                            <p class="text-sm text-on-surface font-semibold truncate"><?= $log['nama_lengkap'] ?: 'Sistem' ?></p>
                            <p class="text-xs text-on-surface-variant mt-0.5 capitalize"><?= $log['aksi'] ?></p>
                            <p class="text-[10px] text-on-surface-variant/70 mt-1"><?= format_date($log['dibuat_pada']) ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
