<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="container">
  <h4>Edit Type SPBU</h4>
  <form action="<?= base_url('type-spbu/update/' . $type['id']) ?>" method="post">
    <div class="form-group">
      <label>Nama Type</label>
      <input type="text" name="nama_type" class="form-control" value="<?= $type['nama_type'] ?>">
    </div>
    <button type="submit" class="btn btn-primary">Update</button>
  </form>
</div>

<?= $this->endSection() ?>
