<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="card">
    <div class="card-header bg-primary text-white">
        <h3>Setup Awal Meter Nozzle</h3>
        <p class="mb-0">SPBU <?= $kode_spbu ?></p>
    </div>
    <div class="card-body">
        <form method="post" action="<?= base_url('penjualan/save-initial-meters') ?>">
            <input type="hidden" name="is_initial_setup" value="1">
            <?= csrf_field() ?>
            
            <div class="alert alert-warning">
                <i class="fas fa-exclamation-triangle"></i> 
                Isi nilai meter aktual untuk semua nozzle.
            </div>
            
            <table class="table table-bordered table-hover">
                <thead class="thead-dark">
                    <tr>
                        <th>Nozzle</th>
                        <th>Produk</th>
                        <th>Meter Saat Ini</th>
                        <th>Input Baru</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($nozzles as $nozzle): ?>
                    <tr>
                        <td><?= $nozzle['kode_nozzle'] ?></td>
                        <td><?= $nozzle['kode_produk'] ?></td>
                        <td><?= $nozzle['initial_meter'] ?? '<span class="text-danger">Belum di-set</span>' ?></td>
                        <td>
                            <input type="hidden" name="is_initial_setup" value="1">
                            <input type="number" step="0.01" name="meters[<?= $nozzle['id'] ?>]" 
                                    value="<?= $nozzle['initial_meter'] ?? 0 ?>" class="form-control" required>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            
            <div class="text-center mt-3">
                <button type="submit" class="btn btn-primary btn-lg">
                    <i class="fas fa-save"></i> Simpan Semua Meter
                </button>
            </div>
        </form>
    </div>
</div>
<?= $this->endSection() ?>