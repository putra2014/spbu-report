<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="container">
    <h2 class="mb-4">Permintaan Reset Meter</h2>
    
    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
    <?php endif; ?>
    
    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
    <?php endif; ?>
<ul class="nav nav-tabs mb-4">
    <li class="nav-item">
        <a class="nav-link active" href="<?= base_url('admin/approvals') ?>">Pending</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="<?= base_url('admin/approvals/history') ?>">History</a>
    </li>
</ul>
    <div class="card shadow">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="thead-dark">
                        <tr>
                            <th>#</th>
                            <th>SPBU</th>
                            <th>Nozzle</th>
                            <th>Produk</th>
                            <th>Meter Lama</th>
                            <th>Meter Baru</th>
                            <th>Diajukan Oleh</th>
                            <th>Alasan</th>
                            <th>Waktu Ajuan</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($requests as $index => $req): ?>
                        <tr>
                            <td><?= $index + 1 ?></td>
                            <td><?= $req['nama_spbu'] ?? 'SPBU '.$req['kode_spbu'] ?></td>
                            <td><?= $req['kode_nozzle'] ?></td>
                            <td><?= $req['nama_produk'] ?></td>
                            <td><?= number_format($req['meter_awal_lama'], 2) ?> L</td>
                            <td><?= number_format($req['meter_awal_baru'], 2) ?> L</td>
                            <td><?= $operatorNames[$req['requested_by']] ?? 'Unknown' ?></td>
                            <td><?= $req['reset_type'] ?></td>
                            <td><?= date('d/m/Y H:i', strtotime($req['created_at'])) ?></td>
                            <td>
                                <div class="btn-group">
                                    <a href="<?= base_url('admin/approve-reset/'. $req['id']) ?>" class="btn btn-sm btn-success">Setujui</a>
                                    <a href="/admin/reject-reset/<?= $req['id'] ?>" class="btn btn-sm btn-danger">Tolak</a>
                                </div>
                            </td>
                            
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>