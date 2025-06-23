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
              <form action="<?= base_url('penerimaan/store') ?>" method="post">

                    <?php if (session('role') != 'admin_spbu'): ?>
                      <div class="form-group">
                        <label>SPBU</label>
                        <select name="kode_spbu" class="form-control" id="spbuSelect" required>
                          <option value="">Pilih SPBU</option>
                          <?php foreach ($spbuList as $s): ?>
                            <option value="<?= $s['kode_spbu'] ?>"><?= $s['kode_spbu'] ?> - <?= $s['nama_spbu'] ?></option>
                          <?php endforeach ?>
                        </select>
                      </div>
                    <?php else: ?>
                      <input type="hidden" name="kode_spbu" value="<?= session('kode_spbu') ?>">
                    <?php endif ?>
                    
                    <div class="form-group">
                      <label>Nomor DO</label>
                      <input type="text" name="nomor_do" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                      <label>Tanggal</label>
                      <input type="date" name="tanggal" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                      <label>Tangki</label>
                      <select name="tangki_id" id="tangkiSelect" class="form-control" required>
                        <option value="">Pilih Tangki</option>
                        <?php foreach ($tangkiList as $t): ?>
                          <option value="<?= $t['id'] ?>" data-produk="<?= $t['kode_produk'] ?>">
                            <?= $t['kode_tangki'] ?> - <?= $t['jenis_tangki'] ?>
                          </option>
                        <?php endforeach ?>
                      </select>
                    </div>
                        
                    <div class="form-group">
                      <label>Jenis BBM (Produk)</label>
                      <input type="text" id="produkInput" class="form-control" readonly>
                      <input type="hidden" id="produkHidden" name="kode_produk">
                    </div>
                        
                    <div class="form-group">
                      <label>Volume (DO)</label>
                      <input type="number" name="volume_do" step="0.01" class="form-control" required>
                    </div>
                        
                    <div class="form-group">
                      <label>Volume Diterima</label>
                      <input type="number" name="volume_diterima" step="0.01" class="form-control" required>
                    </div>
                        
                    <div class="form-group">
                      <label>Harga Beli (per Liter)</label>
                      <input type="number" name="harga_beli" step="0.01" class="form-control">
                    </div>
                        
                    <div class="form-group">
                      <label>Nama Supir</label>
                      <input type="text" name="supir" class="form-control">
                    </div>
                        
                    <div class="form-group">
                      <label>Catatan</label>
                      <textarea name="catatan" class="form-control"></textarea>
                    </div>
                    <button class="btn btn-primary">Simpan</button>
              </form>
              </div>
              <div class="col-md-6">
                <div style="border: 1px solid transparant; padding: 10px; height: 100%; display: flex; align-items: center; justify-content: center;">
                  <!-- Ganti path dengan lokasi gambar Anda -->
                  <img src="<?= base_url('images/isi_bbm.png') ?>" alt="Gambar Produk" style="max-width: 100%; max-height: 300px;">
                </div>
              </div>
            </div>
          </div>
      </div>
</div>
<script>
  const produkInput = document.getElementById('produkInput');
  const produkHidden = document.getElementById('produkHidden');
  const tangkiSelect = document.getElementById('tangkiSelect');

  tangkiSelect.addEventListener('change', function () {
    let selectedOption = tangkiSelect.options[tangkiSelect.selectedIndex];
    let kodeProduk = selectedOption.dataset.produk || '';
    produkInput.value = kodeProduk;
    produkHidden.value = kodeProduk;
  });
</script>



<?= $this->endSection() ?>
