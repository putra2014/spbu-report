<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<h3>Tambah Wilayah</h3>
<form action="<?= base_url('wilayah/store') ?>" method="post">
    <div class="form-group">
        <label>Nama Wilayah</label>
        <input type="text" name="nama_wilayah" class="form-control" required>
    </div>
    <button class="btn btn-success">Simpan</button>
</form>
<?= $this->endSection() ?>
