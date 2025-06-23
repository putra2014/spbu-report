<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Daftar Penjualan Harian</h3>
        <div class="card-tools">
            <a href="<?= base_url('penjualan/create') ?>" class="btn btn-primary">
                <i class="fas fa-plus"></i> Tambah Penjualan
            </a>
        </div>
    </div>
    <div class="card-body">
        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
        <?php endif; ?>
        
        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
        <?php endif; ?>

        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Shift</th>
                    <th>Nozzle</th>
                    <th>Produk</th>
                    <th>Operator</th>
                    <th>Meter Awal</th>
                    <th>Meter Akhir</th>
                    <th>Volume (L)</th>
                    <th>Harga/L</th>
                    <th>Total</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($penjualan as $p): ?>
                    <tr>
                        <td><?= date('d/m/Y', strtotime($p['tanggal'])) ?></td>
                        <td><?= $p['shift'] ?></td>
                        <td><?= $p['kode_nozzle'] ?></td>
                        <td><?= $p['nama_produk'] ?? '-' ?></td>
                        <td><?= $p['nama_operator'] ?? '-' ?></td>
                        <td><?= number_format($p['meter_awal'], 2) ?></td>
                        <td><?= number_format($p['meter_akhir'], 2) ?></td>
                        <td><?= number_format($p['volume'], 2) ?></td>
                        <td><?= number_format($p['harga_jual'], 0) ?></td>
                        <td><?= number_format($p['total_penjualan'], 0) ?></td>
                        <td>
                            <a href="<?= base_url('penjualan/delete/' . $p['id']) ?>" 
                               class="btn btn-sm btn-danger" 
                               onclick="return confirm('Hapus data penjualan?')">
                                <i class="fas fa-trash"></i>
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?= $this->endSection() ?>