<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="container">
  <h4>Tambah Jenis SPBU</h4>
  <form action="<?= base_url('jenis-spbu/store') ?>" method="post">
    <div class="form-group">
      <label>Nama Jenis</label>
      <input type="text" name="nama_jenis" class="form-control" placeholder="Contoh: Reguler, Modular">
    </div>
    <button type="submit" class="btn btn-primary">Simpan</button>
  </form>
</div>

<?= $this->endSection() ?>
