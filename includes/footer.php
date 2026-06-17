<?php if (is_logged_in()): ?>
    </div>
    <footer class="mt-auto border-t border-outline-variant bg-white py-6 px-6">
        <div class="flex justify-between items-center text-sm text-on-surface-variant">
            <p>&copy; <?= date('Y') ?> <?= get_village_profile()['name'] ?>. All rights reserved.</p>
            <p>E-Office Desa Pendua v1.0</p>
        </div>
    </footer>
</main>
<?php else: ?>
    <footer class="bg-white border-t border-outline-variant py-12 mt-12">
        <div class="max-w-7xl mx-auto px-6 grid grid-cols-1 md:grid-cols-3 gap-8">
            <div>
                <h4 class="font-bold text-lg mb-4">E-Office <?= get_village_profile()['name'] ?></h4>
                <p class="text-on-surface-variant text-sm"><?= get_village_profile()['address'] ?></p>
            </div>
            <div>
                <h4 class="font-bold text-lg mb-4">Tautan Cepat</h4>
                <ul class="text-sm text-on-surface-variant space-y-2">
                    <li><a href="<?= base_url() ?>" class="hover:text-primary">Home</a></li>
                    <li><a href="<?= base_url('warga/pengajuan.php') ?>" class="hover:text-primary">Layanan Mandiri</a></li>
                    <li><a href="<?= base_url('warga/status.php') ?>" class="hover:text-primary">Cek Status</a></li>
                </ul>
            </div>
            <div>
                <h4 class="font-bold text-lg mb-4">Kontak</h4>
                <p class="text-sm text-on-surface-variant">WA: <?= get_village_profile()['phone'] ?></p>
                <p class="text-sm text-on-surface-variant">Email: <?= get_village_profile()['email'] ?></p>
            </div>
        </div>
        <div class="max-w-7xl mx-auto px-6 mt-8 pt-8 border-t border-outline-variant text-center text-sm text-on-surface-variant">
            <p>&copy; <?= date('Y') ?> <?= get_village_profile()['name'] ?>. All rights reserved.</p>
        </div>
    </footer>
<?php endif; ?>
</body>
</html>
