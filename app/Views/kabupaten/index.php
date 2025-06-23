<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<h4>Data Kabupaten</h4>
<a href="<?= base_url('kabupaten/create') ?>" class="btn btn-primary mb-3">+ Tambah Kabupaten</a>

<table class="table table-bordered">
    <thead>
        <tr>
            <th>Nama Kabupaten</th>
            <th>Provinsi</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($kabupaten as $row): ?>
        <tr>
            <td><?= $row['nama_kabupaten'] ?></td>
            <td><?= $row['provinsi_nama'] ?></td>
            <td>
                <a href="<?= base_url('kabupaten/edit/'.$row['id']) ?>" class="btn btn-warning btn-sm">Edit</a>
                <a href="<?= base_url('kabupaten/delete/'.$row['id']) ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin?')">Hapus</a>
            </td>
        </tr>
        <?php endforeach ?>
    </tbody>
</table>

<?= $this->endSection() ?>
