<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Riwayat Test Nozzle</h5>
        <a href="<?= base_url('nozzle-test/create') ?>"  class="btn btn-primary btn-sm">
            
            <i class="fas fa-plus"></i> Tambah Test
        </a>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Shift</th>
                        <th>Nozzle</th>
                        <th>Produk</th>
                        <th>Volume Penjualan (L)</th>
                        <th>Volume Test (L)</th>
                        <th>Volume Bersih (L)</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($tests as $test): ?>
                    <tr>
                        <td><?= date('d/m/Y', strtotime($test['tanggal'])) ?></td>
                        <td><?= $test['shift'] ?></td>
                        <td><?= $test['kode_nozzle'] ?></td>
                        <td><?= $test['nama_produk'] ?></td>
                        <td class="text-right"><?= number_format($test['volume_penjualan'], 2) ?></td>
                        <td class="text-right"><?= number_format($test['volume_test'], 2) ?></td>
                        <td class="text-right"><?= number_format($test['volume_penjualan'] - $test['volume_test'], 2) ?></td>
                        <td>
                            <a href="<?= route_to('nozzle-test.edit', $test['id']) ?>" class="btn btn-warning btn-sm">
                                <i class="fas fa-edit"></i>
                            </a>
                            <a href="<?= route_to('nozzle-test.delete', $test['id']) ?>" class="btn btn-danger btn-sm" onclick="return confirm('Hapus data ini?')">
                                <i class="fas fa-trash"></i>
                            </a>
                        </td>
                    </tr>
                    <?php endforeach ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?= $this->endSection() ?>