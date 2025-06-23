<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="container">
  <h4>Data Type SPBU</h4>
  <a href="<?= base_url('type-spbu/create') ?>" class="btn btn-primary mb-2">Tambah Type</a>
  <table class="table table-bordered">
    <thead>
      <tr>
        <th>No</th>
        <th>Nama Type</th>
        <th>Aksi</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($type as $i => $t): ?>
      <tr>
        <td><?= $i + 1 ?></td>
        <td><?= $t['nama_type'] ?></td>
        <td>
          <a href="<?= base_url('type-spbu/edit/' . $t['id']) ?>" class="btn btn-warning btn-sm">Edit</a>
          <a href="<?= base_url('type-spbu/delete/' . $t['id']) ?>" onclick="return confirm('Hapus?')" class="btn btn-danger btn-sm">Hapus</a>
        </td>
      </tr>
      <?php endforeach ?>
    </tbody>
  </table>
</div>

<?= $this->endSection() ?>
