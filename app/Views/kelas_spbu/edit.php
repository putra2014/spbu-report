<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="container">
  <h4>Edit Kategory SPBU</h4>
  <form action="<?= base_url('kelas-spbu/update/' . $kelas['id']) ?>" method="post">
    <div class="form-group">
      <label>Kategory SPBU</label>
      <input type="text" name="nama_kelas" class="form-control" value="<?= $kelas['nama_kelas'] ?>">
    </div>
    <button type="submit" class="btn btn-primary">Update</button>
  </form>
</div>

<?= $this->endSection() ?>
