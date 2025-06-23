<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<h4>Tambah Provinsi</h4>
<form action="<?= base_url('provinsi/store') ?>" method="post">
    <div class="form-group">
        <label>Nama Provinsi</label>
        <input type="text" name="nama" class="form-control" required>
    </div>
    <button type="submit" class="btn btn-success mt-2">Simpan</button>
</form>

<?= $this->endSection() ?>
