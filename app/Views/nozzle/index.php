<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<h4>Data Nozzle</h4>
<a href="<?= base_url('/nozzle/create') ?>" class="btn btn-primary mb-3">Tambah Nozzle</a>

<table class="table table-bordered">
    <thead>
        <tr>
            <th>#</th>
            <th>Kode Nozzle</th>
            <th>Dispenser</th>
            <th>Tangki</th>
            <th>Status</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($nozzleList as $i => $n): ?>
        <tr>
            <td><?= $i+1 ?></td>
            <td><?= esc($n['kode_nozzle']) ?></td>
            <td><?= esc($n['dispenser_id']) ?></td>
            <td><?= esc($n['kode_tangki']) ?></td>
            <td><?= esc($n['status']) ?></td>
            <td>
                <a href="<?= base_url('nozzle/edit/' . $n['id']) ?>" class="btn btn-warning btn-sm">Edit</a>
                <a href="<?= base_url('nozzle/delete/'.$n['id']) ?>" class="btn btn-sm btn-danger"
                               onclick="return confirm('Yakin hapus data?')">Hapus</a>
            </td>
        </tr>
        <?php endforeach ?>
    </tbody>
</table>
<?= $this->endSection() ?>
