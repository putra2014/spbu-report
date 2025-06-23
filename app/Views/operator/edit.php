<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<h4><?= $title ?></h4>
<form action="<?= isset($operator) ? base_url('operator/update/' . $operator['id']) : base_url('operator/store') ?>" method="post">

  <div class="form-group">
    <label>SPBU</label>
    <select name="kode_spbu" class="form-control" <?= session('role') == 'admin_spbu' ? 'readonly' : '' ?>>
      <?php foreach ($spbuList as $s): ?>
        <option value="<?= $s['kode_spbu'] ?>"
          <?= isset($operator) && $operator['kode_spbu'] == $s['kode_spbu'] ? 'selected' : '' ?>>
          <?= $s['kode_spbu'] ?>
        </option>
      <?php endforeach ?>
    </select>
  </div>

  <div class="form-group">
    <label>Nama Operator</label>
    <input type="text" name="nama_operator" class="form-control" required
      value="<?= esc($operator['nama_operator']) ?>">
  </div>

  <div class="form-group">
    <label>No HP</label>
    <input type="text" name="no_hp" class="form-control"
      value="<?= esc($operator['no_hp'])  ?>">
  </div>

  <div class="form-group">
    <label>Shift</label>
    <select name="shift" class="form-control">
      <option value="1" <?= isset($operator) && $operator['shift'] == '1' ? 'selected' : '' ?>>1</option>
      <option value="2" <?= isset($operator) && $operator['shift'] == '2' ? 'selected' : '' ?>>2</option>
      <option value="3" <?= isset($operator) && $operator['shift'] == '3' ? 'selected' : '' ?>>3</option>
    </select>
  </div>

  <button class="btn btn-success">Simpan</button>
</form>

<?= $this->endSection() ?>
