<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="container">
  <h4>Tambah Type SPBU</h4>
  <form action="<?= base_url('type-spbu/store') ?>" method="post">
    <div class="form-group">
      <label>Nama Type</label>
      <input type="text" name="nama_type" class="form-control" placeholder="Contoh: COCO, DODO, CODO">
    </div>
    <button type="submit" class="btn btn-primary">Simpan</button>
  </form>
</div>

<?= $this->endSection() ?>
