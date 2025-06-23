<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<h4>Riwayat Perubahan Dispenser</h4>
<a href="<?= base_url('/dispenser') ?>" class="btn btn-secondary mb-3">Kembali</a>

<table class="table table-bordered">
  <thead>
    <tr>
      <th>Tanggal</th>
      <th>Kode</th>
      <th>Merek</th>
      <th>Jumlah Nozzle</th>
      <th>Type</th>
      <th>Tgl Kalibrasi</th>
      <th>Diubah Oleh</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($logList as $log): ?>
      <tr>
        <td><?= $log['updated_at'] ?></td>
        <td><?= $log['kode_dispenser'] ?></td>
        <td><?= $log['merek_dispenser'] ?></td>
        <td><?= $log['jumlah_nozzle'] ?></td>
        <td><?= $log['type_dispenser'] ?></td>
        <td><?= $log['tgl_kalibrasi_berakhir'] ?></td>
        <td>User ID: <?= $log['updated_by'] ?></td>
      </tr>
    <?php endforeach ?>
  </tbody>
</table>

<?= $this->endSection() ?>
