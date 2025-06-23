<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="container">
  <h4>Tambah Kategory</h4>
  <form action="<?= base_url('kelas-spbu/store') ?>" method="post">
    <div class="form-group">
      <label>KAtegory SPBU</label>
      <input type="text" name="nama_kelas" class="form-control" placeholder="Contoh: Pasti Pas, Basic">
    </div>
    <button type="submit" class="btn btn-primary">Simpan</button>
  </form>
</div>

<?= $this->endSection() ?>
