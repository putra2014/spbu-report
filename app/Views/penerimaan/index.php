<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<h4>Data Penerimaan BBM</h4>

<a href="<?= base_url('penerimaan/create') ?>" class="btn btn-primary mb-2">Tambah</a>

<table class="table table-bordered table-sm">
  <thead>
    <tr>
      <th>No</th>
      <th>Tanggal</th>
      <th>SPBU</th>
      <th>DO</th>
      <th>Tangki</th>
      <th>Produk</th>
      <th>Volume DO</th>
      <th>Volume Diterima</th>
      <th>Selisih</th>
      <th>Status</th>
      <th>Harga Beli</th>
      <th>Supir</th>
      <th>Aksi</th>
    </tr>
  </thead>
  <tbody>
    <?php $no=1; foreach ($penerimaan as $p): ?>
      <tr>
        <td><?= $no++ ?></td>
        <td><?= esc($p['tanggal']) ?></td>
        <td><?= esc($p['kode_spbu']) ?></td>
        <td><?= esc($p['nomor_do']) ?></td>
        <td><?= esc($p['tangki_id']) ?></td>
        <td><?= esc($p['nama_produk'] ?? $p['kode_produk']) ?></td>
        <td><?= esc($p['volume_do']) ?></td>
        <td><?= esc($p['volume_diterima']) ?></td>
        <td><?= esc($p['selisih']) ?></td>
        <td><?= esc($p['status']) ?></td>
        <td><?= number_format($p['harga_beli'], 0, ',', '.') ?></td>
        <td><?= esc($p['supir']) ?></td>
        <td>
          <a href="<?= base_url('penerimaan/edit/' . $p['id']) ?>" class="btn btn-sm btn-info">Edit</a>
          <a href="<?= base_url('penerimaan/delete/' . $p['id']) ?>" class="btn btn-sm btn-danger" onclick="return confirm('Yakin?')">Hapus</a>
        </td>
      </tr>
    <?php endforeach ?>
  </tbody>
</table>

<?= $this->endSection() ?>
