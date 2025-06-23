<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<h3>Edit Wilayah</h3>
<form action="<?= base_url('wilayah/update/'.$wilayah['id']) ?>" method="post">
    <div class="form-group">
        <label>Nama Wilayah</label>
        <input type="text" name="nama_wilayah" class="form-control" value="<?= esc($wilayah['nama_wilayah']) ?>" required>
    </div>
    <button class="btn btn-success">Update</button>
</form>
<?= $this->endSection() ?>
