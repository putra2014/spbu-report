<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<h4>Data Dispenser</h4>
<a href="<?= base_url('/dispenser/create') ?>" class="btn btn-primary mb-3">Tambah Dispenser</a>

<table class="table table-bordered">
  <thead>
    <tr>
      <th>#</th>
      <th>SPBU</th>
      <th>Kode</th>
      <th>Merek</th>
      <th>Jumlah Nozzle</th>
      <th>Type</th>
      <th>Tgl Kalibrasi Berakhir</th>
      <th>Aksi</th>
    </tr>
  </thead>
  <tbody>
    <?php $i = 1; foreach ($dispenserList as $d): ?>
    <tr>
      <td><?= $i++ ?></td>
      <td><?= $d['kode_spbu'] ?></td>
      <td><?= $d['kode_dispenser'] ?></td>
      <td><?= $d['merek_dispenser'] ?></td>
      <td><?= $d['jumlah_nozzle'] ?></td>
      <td><?= $d['type_dispenser'] ?></td>
      <td>
        <?= $d['tgl_kalibrasi_berakhir'] ?>
        <?php if (strtotime($d['tgl_kalibrasi_berakhir']) <= strtotime('+30 days')): ?>
          <span class="badge bg-warning text-dark">Segera Kalibrasi!</span>
        <?php endif ?>
      </td>
      <td>
        <a href="<?= base_url('dispenser/edit/' . $d['id']) ?>" class="btn btn-sm btn-warning">Edit</a>
        <a href="<?= base_url('dispenser/log/' . $d['id']) ?>" class="btn btn-sm btn-warning">Riwayat</a>
        <a href="<?= base_url('dispenser/delete/' . $d['id']) ?>" class="btn btn-sm btn-danger" onclick="return confirm('Hapus data ini?')">Hapus</a>
      </td>
    </tr>
    <?php endforeach ?>
  </tbody>
</table>

<?= $this->endSection() ?>
