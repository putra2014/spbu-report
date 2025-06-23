<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<h4>Data Operator SPBU</h4>
<a href="<?= base_url('operator/create') ?>" class="btn btn-primary mb-2">Tambah</a>

<table class="table table-bordered">
  <thead>
    <tr>
      <th>No</th>
      <th>SPBU</th>
      <th>Nama</th>
      <th>No HP</th>
      <th>Shift</th>
      <th>Aksi</th>
    </tr>
  </thead>
  <tbody>
    <?php $no=1; foreach ($operator as $o): ?>
      <tr>
        <td><?= $no++ ?></td>
        <td><?= esc($o['kode_spbu']) ?></td>
        <td><?= esc($o['nama_operator']) ?></td>
        <td><?= esc($o['no_hp']) ?></td>
        <td><?= esc($o['shift']) ?></td>
        <td>
          <a href="<?= base_url('operator/edit/'.$o['id']) ?>" class="btn btn-sm btn-info">Edit</a>
          <a href="<?= base_url('operator/delete/'.$o['id']) ?>" class="btn btn-sm btn-danger" onclick="return confirm('Hapus?')">Hapus</a>
        </td>
      </tr>
    <?php endforeach ?>
  </tbody>
</table>

<?= $this->endSection() ?>
