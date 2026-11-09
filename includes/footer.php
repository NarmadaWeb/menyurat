<?php if (is_logged_in()): ?>
    </div>
    <footer class="mt-auto border-t border-outline-variant bg-white py-4 sm:py-6 px-4 sm:px-8">
        <div class="flex flex-col sm:flex-row justify-between items-center gap-2 text-xs text-on-surface-variant">
            <p>&copy; <?= date('Y') ?> <?= htmlspecialchars(get_village_profile()['nama']) ?>. Hak cipta dilindungi.</p>
            <p class="font-medium text-primary">Sistem Informasi Persuratan & Kependudukan v2.0</p>
        </div>
    </footer>
</main>
<?php else: ?>
    <footer class="bg-white border-t border-outline-variant py-10 sm:py-14 mt-auto">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 grid grid-cols-1 md:grid-cols-3 gap-8">
            <div>
                <div class="flex items-center gap-2 mb-3">
                    <span class="material-symbols-outlined text-primary text-2xl">account_balance</span>
                    <h4 class="font-bold text-base sm:text-lg text-primary">E-Office <?= htmlspecialchars(get_village_profile()['nama']) ?></h4>
                </div>
                <p class="text-on-surface-variant text-xs sm:text-sm leading-relaxed"><?= htmlspecialchars(get_village_profile()['alamat']) ?></p>
            </div>
            <div>
                <h4 class="font-bold text-sm sm:text-base text-on-surface mb-3 uppercase tracking-wider text-xs">Layanan Publik</h4>
                <ul class="text-xs sm:text-sm text-on-surface-variant space-y-2">
                    <li><a href="<?= base_url() ?>" class="hover:text-primary transition-colors flex items-center gap-1.5"><span class="material-symbols-outlined text-sm">home</span> Beranda</a></li>
                    <li><a href="<?= base_url('warga/pengajuan.php') ?>" class="hover:text-primary transition-colors flex items-center gap-1.5"><span class="material-symbols-outlined text-sm">edit_note</span> Pengajuan Surat Online</a></li>
                    <li><a href="<?= base_url('warga/status.php') ?>" class="hover:text-primary transition-colors flex items-center gap-1.5"><span class="material-symbols-outlined text-sm">search</span> Lacak Berkas</a></li>
                </ul>
            </div>
            <div>
                <h4 class="font-bold text-sm sm:text-base text-on-surface mb-3 uppercase tracking-wider text-xs">Kontak Kantor Desa</h4>
                <div class="text-xs sm:text-sm text-on-surface-variant space-y-2">
                    <p class="flex items-center gap-2"><span class="material-symbols-outlined text-sm text-primary">call</span> <?= htmlspecialchars(get_village_profile()['telepon']) ?></p>
                    <p class="flex items-center gap-2"><span class="material-symbols-outlined text-sm text-primary">mail</span> <?= htmlspecialchars(get_village_profile()['email']) ?></p>
                    <p class="flex items-center gap-2"><span class="material-symbols-outlined text-sm text-primary">schedule</span> Senin - Jumat: 08.00 - 15.00 WITA</p>
                </div>
            </div>
        </div>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 mt-8 pt-6 border-t border-outline-variant text-center text-xs text-on-surface-variant">
            <p>&copy; <?= date('Y') ?> Pemerintah Desa <?= htmlspecialchars(get_village_profile()['nama']) ?>. Semua hak dilindungi undang-undang.</p>
        </div>
    </footer>
<?php endif; ?>
</body>
</html>
