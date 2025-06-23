<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<h3>Data Area</h3>
<a href="<?= base_url('area/create') ?>" class="btn btn-primary mb-2">Tambah Area</a>
<table class="table table-bordered">
    <tr>
        <th>No</th><th>Wilayah</th><th>Nama Area</th><th>Aksi</th>
    </tr>
    <?php foreach ($area as $key => $row): ?>
    <tr>
        <td><?= $key+1 ?></td>
        <td><?= esc($row['nama_wilayah']) ?></td>
        <td><?= esc($row['nama_area']) ?></td>
        <td>
            <a href="<?= base_url('area/edit/'.$row['id']) ?>" class="btn btn-warning btn-sm">Edit</a>
            <a href="<?= base_url('area/delete/'.$row['id']) ?>" class="btn btn-danger btn-sm" onclick="return confirm('Hapus data ini?')">Hapus</a>
        </td>
    </tr>
    <?php endforeach ?>
</table>
<?= $this->endSection() ?>
