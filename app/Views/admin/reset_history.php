<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="container">
    <h2 class="mb-4">Riwayat Reset Meter</h2>
    
    <div class="card shadow">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="thead-dark">
                        <tr>
                            <th>#</th>
                            <th>SPBU</th>
                            <th>Nozzle</th>
                            <th>Status</th>
                            <th>Meter Lama</th>
                            <th>Meter Baru</th>
                            <th>Disetujui Oleh</th>
                            <th>Waktu</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($requests as $index => $req): ?>
                        <tr class="<?= $req['status'] === 'approved' ? 'table-success' : 'table-danger' ?>">
                            <td><?= $index + 1 ?></td>
                            <td><?= $req['nama_spbu'] ?? $req['kode_spbu'] ?></td>
                            <td><?= $req['nozzle_id'] ?></td>
                            <td>
                                <span class="badge bg-<?= $req['status'] === 'approved' ? 'success' : 'danger' ?>">
                                    <?= ucfirst($req['status']) ?>
                                </span>
                            </td>
                            <td><?= number_format($req['meter_awal_lama'], 2) ?> L</td>
                            <td><?= number_format($req['meter_awal_baru'], 2) ?> L</td>
                            <td><?= $req['nama_admin_region'] ?? 'System' ?></td>
                            <td><?= date('d/m/Y H:i', strtotime($req['approved_at'])) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>