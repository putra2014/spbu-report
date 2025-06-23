<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<h4>Master Kode Nozzle</h4>
<a href="<?= base_url('/kode-nozzle/create') ?>" class="btn btn-primary mb-2">Tambah</a>
<table class="table table-bordered">
  <thead><tr><th>Kode</th><th>Keterangan</th><th>Aksi</th></tr></thead>
  <tbody>
    <?php foreach ($list as $row): ?>
    <tr>
      <td><?= $row['kode_nozzle'] ?></td>
      <td><?= $row['keterangan'] ?></td>
      <td>
        <a href="<?= base_url('/kode-nozzle/edit/' . $row['id']) ?>" class="btn btn-warning btn-sm">Edit</a>
        <a href="<?= base_url('/kode-nozzle/delete/' . $row['id']) ?>" onclick="return confirm('Hapus data?')" class="btn btn-danger btn-sm">Hapus</a>
      </td>
    </tr>
    <?php endforeach ?>
  </tbody>
</table>
<?= $this->endSection() ?>