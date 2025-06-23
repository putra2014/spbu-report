<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<h4>Laporan Penjualan Harian</h4>

<form method="get" class="row g-2 mb-3">
  <input type="date" name="tanggal" class="form-control col-md-3" value="<?= esc($_GET['tanggal'] ?? '') ?>">
  <select name="shift" class="form-control col-md-2">
    <option value="">Semua Shift</option>
    <option value="1">1</option>
    <option value="2">2</option>
    <option value="3">3</option>
  </select>
  <input type="text" name="nozzle_id" class="form-control col-md-2" placeholder="Nozzle ID" value="<?= esc($_GET['nozzle_id'] ?? '') ?>">
  <button class="btn btn-primary col-md-2">Filter</button>
</form>

<table class="table table-bordered">
  <thead>
    <tr>
      <th>Tanggal</th>
      <th>Nozzle</th>
      <th>Shift</th>
      <th>Meter Awal</th>
      <th>Meter Akhir</th>
      <th>Volume</th>
      <th>Harga</th>
      <th>Total</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($penjualan as $p): ?>
    <tr>
      <td><?= $p['tanggal'] ?></td>
      <td><?= $p['nozzle_id'] ?></td>
      <td><?= $p['shift'] ?></td>
      <td><?= $p['meter_awal'] ?></td>
      <td><?= $p['meter_akhir'] ?></td>
      <td><?= $p['meter_akhir'] - $p['meter_awal'] ?></td>
      <td><?= number_format($p['harga_jual']) ?></td>
      <td><?= number_format($p['total_penjualan']) ?></td>
    </tr>
    <?php endforeach ?>
  </tbody>
</table>

<?= $this->endSection() ?>
