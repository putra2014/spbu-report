<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="container">
      <div class="card shadow mb-4">
          <div class="card-header py-3 bg-dark">
              <h4 class="m-0 font-weight-bold text-white"><?= $title ?></h4>
          </div>
          
          <div class="card-body">
            <div class="row">
              <div class="col-md-6">
                <form action="<?= base_url('produk/update/'.$produk['id']) ?>" method="post">
                    <div class="form-group">
                      <label>Kode Product</label>
                      <select name="kode_produk" class="form-control" required>
                        <option value="">Pilih Kode Product</option>
                        <option value="PL" <?= ($produk['kode_produk'] ?? '') == 'PL' ? 'selected' : '' ?>>PL</option>
                        <option value="PTX" <?= ($produk['kode_produk'] ?? '') == 'PTX' ? 'selected' : '' ?>>PTX</option>
                        <option value="BS" <?= ($produk['kode_produk'] ?? '') == 'BS' ? 'selected' : '' ?>>BS</option>
                        <option value="PTXturbo" <?= ($produk['kode_produk'] ?? '') == 'PTXturbo' ? 'selected' : '' ?>>PTXturbo</option>
                        <option value="DXlite" <?= ($produk['kode_produk'] ?? '') == 'DXlite' ? 'selected' : '' ?>>DXlite</option>
                        <option value="PDEX" <?= ($produk['kode_produk'] ?? '') == 'PDEX' ? 'selected' : '' ?>>PDEX</option>
                      </select>
                    </div>
                    <div class="form-group">
                        <label>1. Nama Product</label>
                        <input type="text" name="nama_produk" value="<?= $produk['nama_produk'] ?>" class="form-control" required>
                    </div>
                    <div class="form-group">
                      <label>Kategori</label>
                      <select name="kategori" class="form-control" required>
                        <option value="">Pilih Kategori</option>
                        <option value="JBKP" <?= ($produk['kategori'] ?? '') == 'JBKP' ? 'selected' : '' ?>>JBKP</option>
                        <option value="JBT" <?= ($produk['kategori'] ?? '') == 'JBT' ? 'selected' : '' ?>>JBT</option>
                        <option value="JBU" <?= ($produk['kategori'] ?? '') == 'JBU' ? 'selected' : '' ?>>JBU</option>
                      </select>
                    </div>
          
                    <div class="form-group">
                      <label>Jenis</label>
                      <select name="jenis" class="form-control" required>
                        <option value="">Pilih Jenis</option>
                        <option value="Gasoline" <?= ($produk['jenis'] ?? '') == 'Gasoline' ? 'selected' : '' ?>>Gasoline</option>
                        <option value="Gasoil" <?= ($produk['jenis'] ?? '') == 'Gasoil' ? 'selected' : '' ?>>Gasoil</option>
                      </select>
                    </div>
          
                    <button type="submit" class="btn btn-primary">Update</button>
                </form>
            </div>
  <div class="col-md-6">
      <div style="border: 1px solid transparant; padding: 10px; height: 100%; display: flex; align-items: center; justify-content: center;">
        <!-- Ganti path dengan lokasi gambar Anda -->
        <img src="<?= base_url('images/produk.jpg') ?>" alt="Gambar Produk" style="max-width: 100%; max-height: 300px;">
      </div>
    </div>
  </div>
          </div>
      </div>
</div>
<?= $this->endSection() ?>
