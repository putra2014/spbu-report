<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="container-fluid">
  <div class="card shadow mb-4">
    <div class="card-header bg-dark text-white py-3">
      <div class="d-flex justify-content-between align-items-center">
        <h4 class="m-0"><i class="fas fa-gas-pump mr-2"></i>Data Tangki</h4>
          <a href="<?= base_url('tangki/create') ?>" class="btn btn-danger mb-2">+ Tambah Tangki</a>
      </div>
    </div>
    <div class="card-body">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Kode</th>
                    <th>Jenis Tangki</th>
                    <th>Nama Product</th>
                    <th>Kapasitas (L)</th>
                    <th>SPBU</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($tangki as $row): ?>
                    <tr>
                        <td><?= esc($row['kode_tangki']) ?></td>
                        <td><?= esc($row['jenis_tangki']) ?></td>
                        <td><?= esc($row['nama_produk']) ?></td>
                        <td><?= esc($row['kapasitas']) ?></td>
                        <td><?= esc($row['kode_spbu']) ?></td>
                        <td>
                            <a href="<?= base_url('tangki/edit/'.$row['id']) ?>" class="btn btn-sm btn-warning">Edit</a>
                            <a href="<?= base_url('tangki/delete/'.$row['id']) ?>" class="btn btn-sm btn-danger"
                               onclick="return confirm('Yakin hapus data?')">Hapus</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
  </div>
</div>
<?= $this->endSection() ?>
