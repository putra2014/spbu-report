<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<h4>Edit Kode Dispenser</h4>
<form action="<?= base_url('/kode-dispenser/update/' . $item['id']) ?>" method="post">
  <div class="form-group">
    <label>Kode Dispenser</label>
    <input type="text" name="kode_dispenser" value="<?= $item['kode_dispenser'] ?>" class="form-control" required>
  </div>
  <div class="form-group">
    <label>Keterangan</label>
    <input type="text" name="keterangan" value="<?= $item['keterangan'] ?>" class="form-control">
  </div>
  <button type="submit" class="btn btn-primary">Update</button>
</form>
<?= $this->endSection() ?>
