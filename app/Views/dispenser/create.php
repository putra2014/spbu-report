<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<h4>Tambah Dispenser</h4>
<div class="container">
      <div class="card shadow mb-4">
          <div class="card-header py-3 bg-dark">
              <h4 class="m-0 font-weight-bold text-white"><?= $title ?></h4>
          </div>
          <div class="card-body">
            <div class="row">
              <div class="col-md-6">
                <form action="<?= base_url('/dispenser/store') ?>" method="post">
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
                          <label>Kode Dispenser</label>
                          <select name="kode_dispenser" class="form-control" required>
                            <option value="">Pilih Kode</option>
                            <?php foreach ($kodeDispenserList as $k): ?>
                              <option value="<?= $k['kode_dispenser'] ?>"><?= $k['kode_dispenser'] ?></option>
                            <?php endforeach ?>
                          </select>
                        </div>
                            
                        <div class="form-group">
                          <label>Merek Dispenser</label>
                          <input type="text" name="merek_dispenser" class="form-control" required>
                        </div>
                            
                        <div class="form-group">
                          <label>Jumlah Nozzle</label>
                          <input type="number" name="jumlah_nozzle" class="form-control" required>
                        </div>
                            
                        <div class="form-group">
                          <label>Type Dispenser</label>
                          <input type="text" name="type_dispenser" class="form-control" required>
                        </div>
                            
                        <div class="form-group">
                          <label>Tgl Kalibrasi Berakhir</label>
                          <input type="date" name="tgl_kalibrasi_berakhir" class="form-control" required>
                        </div>
                      <button type="submit" class="btn btn-primary">Simpan</button>
                </form>
              </div>
              <div class="col-md-6">
                <div style="border: 1px solid transparant; padding: 10px; height: 100%; display: flex; align-items: center; justify-content: center;">
                  <!-- Ganti path dengan lokasi gambar Anda -->
                  <img src="<?= base_url('images/dispenser.png') ?>" alt="Gambar Produk" style="max-width: 100%; max-height: 300px;">
                </div>
              </div>
            </div>
          </div>
      </div>
</div>

<?= $this->endSection() ?>
