<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<h4>Tambah Kode Dispenser</h4>
<form action="<?= base_url('/kode-dispenser/store') ?>" method="post">
  <div class="form-group">
    <label>Kode Dispenser</label>
    <input type="text" name="kode_dispenser" class="form-control" required>
  </div>
  <div class="form-group">
    <label>Keterangan</label>
    <input type="text" name="keterangan" class="form-control">
  </div>
  <button type="submit" class="btn btn-success">Simpan</button>
</form>
<?= $this->endSection() ?>
