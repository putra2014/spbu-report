<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<h4>Edit Dispenser</h4>

<form action="<?= base_url('/dispenser/update/' . $dispenser['id']) ?>" method="post">
  <div class="form-group">
    <label>Kode Dispenser</label>
    <select name="kode_dispenser" class="form-control" required>
      <?php foreach ($kodeDispenserList as $k): ?>
        <option value="<?= $k['kode_dispenser'] ?>" <?= $dispenser['kode_dispenser'] == $k['kode_dispenser'] ? 'selected' : '' ?>>
          <?= $k['kode_dispenser'] ?>
        </option>
      <?php endforeach ?>
    </select>
  </div>

  <div class="form-group">
    <label>Merek Dispenser</label>
    <input type="text" name="merek_dispenser" class="form-control" value="<?= esc($dispenser['merek_dispenser']) ?>" required>
  </div>

  <div class="form-group">
    <label>Jumlah Nozzle</label>
    <input type="number" name="jumlah_nozzle" class="form-control" value="<?= esc($dispenser['jumlah_nozzle']) ?>" required>
  </div>

  <div class="form-group">
    <label>Type Dispenser</label>
    <input type="text" name="type_dispenser" class="form-control" value="<?= esc($dispenser['type_dispenser']) ?>" required>
  </div>

  <div class="form-group">
    <label>Tgl Kalibrasi Berakhir</label>
    <input type="date" name="tgl_kalibrasi_berakhir" class="form-control" value="<?= esc($dispenser['tgl_kalibrasi_berakhir']) ?>" required>
  </div>

  <button type="submit" class="btn btn-success">Update</button>
</form>

<?= $this->endSection() ?>
