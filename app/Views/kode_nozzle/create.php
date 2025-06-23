<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<h4>Tambah Kode Nozzle</h4>
<form action="<?= base_url('/kode-nozzle/store') ?>" method="post">
  <div class="form-group">
    <label>Kode Nozzle</label>
    <input type="text" name="kode_nozzle" class="form-control" required>
  </div>
  <div class="form-group">
    <label>Keterangan</label>
    <input type="text" name="keterangan" class="form-control">
  </div>
  <button type="submit" class="btn btn-success">Simpan</button>
</form>
<?= $this->endSection() ?>