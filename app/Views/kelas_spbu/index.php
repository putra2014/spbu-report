<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="container">
  <h4>Data Jenis SPBU</h4>
  <a href="<?= base_url('kelas-spbu/create') ?>" class="btn btn-primary mb-2">Tambah Kategori</a>
  <table class="table table-bordered">
    <thead>
      <tr>
        <th>No</th>
        <th>Kategory SPBU</th>
        <th>Aksi</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($kelas as $i => $j): ?>
      <tr>
        <td><?= $i + 1 ?></td>
        <td><?= $j['nama_kelas'] ?></td>
        <td>
          <a href="<?= base_url('kelas-spbu/edit/' . $j['id']) ?>" class="btn btn-warning btn-sm">Edit</a>
          <a href="<?= base_url('kelas-spbu/delete/' . $j['id']) ?>" onclick="return confirm('Hapus?')" class="btn btn-danger btn-sm">Hapus</a>
        </td>
      </tr>
      <?php endforeach ?>
    </tbody>
  </table>
</div>

<?= $this->endSection() ?>
