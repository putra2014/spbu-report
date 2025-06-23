<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="container-fluid">
  <div class="card shadow mb-4">
    <div class="card-header bg-dark text-white py-3">
      <div class="d-flex justify-content-between align-items-center">
        <h4 class="m-0"><i class="fas fa-gas-pump mr-2"></i>Data Product</h4>
        <?php if (in_array(session()->get('role'), ['admin_region'])): ?>
          <a href="<?= base_url('produk/create') ?>" class="btn btn-danger"><i class="fas fa-plus mr-2"></i>Tambah Product</a>
        <?php endif; ?>
      </div>
    </div>
    <div class="card-body">

            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Kode Product</th>
                        <th>Nama Product</th>
                        <th>Kategory</th>
                        <th>Jenis</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($produk as $p): ?>
                    <tr>
                        <td><?= $p['kode_produk'] ?></td>
                        <td><?= $p['nama_produk'] ?></td>
                        <td><?= $p['kategori'] ?></td>
                        <td><?= $p['jenis'] ?></td>
                        <td>
                            <a href="<?= base_url('produk/edit/'.$p['id']) ?>" class="btn btn-sm btn-warning">Edit</a>
                            <a href="<?= base_url('produk/delete/'.$p['id']) ?>" class="btn btn-sm btn-danger" onclick="return confirm('Yakin hapus?')">Hapus</a>
                        </td>
                    </tr>
                    <?php endforeach ?>
                </tbody>
            </table>
    </div>
  </div>
  </div>

<?= $this->endSection() ?>
