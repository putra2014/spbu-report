<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<h4>Edit Provinsi</h4>
<form action="<?= base_url('provinsi/update/'.$provinsi['id']) ?>" method="post">
    <div class="form-group">
        <label>Nama Provinsi</label>
        <input type="text" name="nama" value="<?= $provinsi['nama'] ?>" class="form-control" required>
    </div>
    <button type="submit" class="btn btn-primary mt-2">Update</button>
</form>

<?= $this->endSection() ?>
