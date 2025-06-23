<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<h4>Data Provinsi</h4>
<a href="<?= base_url('provinsi/create') ?>" class="btn btn-primary mb-3">+ Tambah Provinsi</a>

<table class="table table-bordered">
    <thead>
        <tr>
            <th>Nama Provinsi</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($provinsi as $row): ?>
        <tr>
            <td><?= $row['nama'] ?></td>
            <td>
                <a href="<?= base_url('provinsi/edit/'.$row['id']) ?>" class="btn btn-warning btn-sm">Edit</a>
                <a href="<?= base_url('provinsi/delete/'.$row['id']) ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin?')">Hapus</a>
            </td>
        </tr>
        <?php endforeach ?>
    </tbody>
</table>

<?= $this->endSection() ?>
