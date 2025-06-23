<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<h3>Wilayah</h3>
<a href="<?= base_url('wilayah/create') ?>" class="btn btn-primary mb-2">Tambah Wilayah</a>
<table class="table table-bordered">
    <tr>
        <th>No</th><th>Nama Wilayah</th><th>Aksi</th>
    </tr>
    <?php foreach ($wilayah as $key => $row): ?>
    <tr>
        <td><?= $key+1 ?></td>
        <td><?= esc($row['nama_wilayah']) ?></td>
        <td>
            <a href="<?= base_url('wilayah/edit/'.$row['id']) ?>" class="btn btn-warning btn-sm">Edit</a>
            <a href="<?= base_url('wilayah/delete/'.$row['id']) ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin hapus?')">Hapus</a>
        </td>
    </tr>
    <?php endforeach ?>
</table>
<?= $this->endSection() ?>
