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
            <form action="<?= base_url('tangki/store') ?>" method="post">

                <?php if (session()->get('role') === 'admin_spbu'): ?>
                    <input type="hidden" name="kode_spbu" value="<?= session()->get('kode_spbu') ?>">
                    <p><strong>SPBU <?= session()->get('kode_spbu') ?></strong></p>
                <?php else: ?>
                    <div class="form-group">
                        <label>SPBU</label>
                        <select name="kode_spbu" class="form-control" required>
                            <option value="">Pilih SPBU</option>
                            <?php foreach ($spbuList as $s): ?>
                                <option value="<?= $s['kode_spbu'] ?>"><?= $s['kode_spbu'] ?> - <?= $s['nama_spbu'] ?></option>
                            <?php endforeach ?>
                        </select>
                    </div>
                <?php endif ?>
                <div class="form-group">
                  <label for="kode_tangki">Kode Tangki</label>
                  <select name="kode_tangki" class="form-control" required>
                    <option value="">Pilih Kode Tangki</option>
                    <?php foreach (range(1, 10) as $i): ?>
                      <option value="T<?= $i ?>">T<?= $i ?></option>
                    <?php endforeach; ?>
                  </select>
                </div>
                <div class="form-group">
                    <label for="jenis_tangki">Jenis Tangki</label>
                    <select name="jenis_tangki" class="form-control" required>
                        <option value="">-- Pilih Jenis --</option>
                        <option value="underground">1. Underground</option>
                        <option value="upper">2. Upper</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Jenis BBM</label>
                    <select name="kode_produk" class="form-control" required>
                        <option value="">Pilih Produk</option>
                        <?php foreach ($produkList as $p): ?>
                            <option value="<?= $p['kode_produk'] ?>" <?= old('kode_produk', $tangki['kode_produk'] ?? '') == $p['kode_produk'] ? 'selected' : '' ?>>
                                <?= $p['nama_produk'] ?>
                            </option>
                        <?php endforeach ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Kapasitas (Liter)</label>
                    <input type="number" name="kapasitas" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Dead Stock (Liter)</label>
                    <input type="number" name="dead_stock" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-success mt-2">Simpan</button>
            </form>
    </div>
    <div class="col-md-6">
      <div style="border: 1px solid transparant; padding: 10px; height: 100%; display: flex; align-items: center; justify-content: center;">
        <!-- Ganti path dengan lokasi gambar Anda -->
        <img src="<?= base_url('images/tanki pendam.png') ?>" alt="Gambar Produk" style="max-width: 100%; max-height: 300px;">
      </div>
    </div>
  </div>
</div>
      </div>
</div>

<?= $this->endSection() ?>