<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="card">
    <div class="card-header bg-primary text-white">
        <h3 class="card-title">Daftar Permintaan Reset Meter</h3>
        <?php if (in_array(session()->get('role'), ['admin_region'])): ?>
            <div class="float-right">
                <a href="<?= base_url('admin/approvals') ?>" class="btn btn-light btn-sm">
                    Lihat Permintaan Pending
                </a>
            </div>
        <?php endif; ?>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <?php if('admin_region'): ?>
                            <th>SPBU</th>
                        <?php endif; ?>
                        <th>Nozzle</th>
                        <th>Produk</th>
                        <th>Meter Lama</th>
                        <th>Meter Baru</th>
                        <th>Alasan</th>
                        <th>Status</th>
                        <th>Diajukan Pada</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($requests as $req): ?>
                    <tr>
                        <?php if('admin_region'): ?>
                            <td><?= $req['kode_spbu'] ?? $req['kode_spbu'] ?></td>
                        <?php endif; ?>
                        <td><?= $req['kode_nozzle'] ?></td>
                        <td><?= $req['nama_produk'] ?? '-' ?></td>
                        <td><?= number_format($req['meter_awal_lama'], 2) ?> L</td>
                        <td><?= number_format($req['meter_awal_baru'], 2) ?> L</td>
                        <td><?= $req['alasan'] ?></td>
                        <td>
                            <span class="badge bg-<?= 
                                $req['status'] == 'approved' ? 'success' : 
                                ($req['status'] == 'rejected' ? 'danger' : 'warning') ?>">
                                <?= ucfirst($req['status']) ?>
                            </span>
                        </td>
                        <td><?= date('d/m/Y H:i', strtotime($req['created_at'])) ?></td>
                        <td>
                            <?php if (in_array(session()->get('role'), ['admin_region'])): ?>
                                <a href="<?= base_url('admin/approve-reset/'.$req['id']) ?>" 
                                   class="btn btn-sm btn-success"
                                   onclick="return confirm('Approve reset meter ini?')">
                                    Approve
                                </a>
                            <?php endif; ?>
                            
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?= $this->endSection() ?>