<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="container">
  <h4>Edit Jenis SPBU</h4>
  <form action="<?= base_url('jenis-spbu/update/' . $jenis['id']) ?>" method="post">
    <div class="form-group">
      <label>Nama Jenis</label>
      <input type="text" name="nama_jenis" class="form-control" value="<?= $jenis['nama_jenis'] ?>">
    </div>
    <button type="submit" class="btn btn-primary">Update</button>
  </form>
</div>

<?= $this->endSection() ?>
