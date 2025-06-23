<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<h4>Edit Penerimaan BBM</h4>

<form action="<?= base_url('penerimaan/update/' . $penerimaan['id']) ?>" method="post">
  <div class="row">
    <div class="col-md-6">

      <input type="hidden" name="kode_spbu" value="<?= esc($penerimaan['kode_spbu']) ?>">

      <div class="form-group">
        <label>Nomor DO</label>
        <input type="text" name="nomor_do" value="<?= esc($penerimaan['nomor_do']) ?>" class="form-control" required>
      </div>

      <div class="form-group">
        <label>Tanggal</label>
        <input type="date" name="tanggal" value="<?= esc($penerimaan['tanggal']) ?>" class="form-control" required>
      </div>

      <div class="form-group">
        <label>Tangki</label>
        <select name="tangki_id" id="tangkiSelect" class="form-control" required>
          <option value="">Pilih Tangki</option>
          <?php foreach ($tangkiList as $t): ?>
            <option value="<?= $t['id'] ?>" data-produk="<?= $t['kode_produk'] ?>"
              <?= $t['id'] == $penerimaan['tangki_id'] ? 'selected' : '' ?>>
              <?= $t['kode_tangki'] ?> - <?= $t['jenis_tangki'] ?>
            </option>
          <?php endforeach ?>
        </select>
      </div>

      <div class="form-group">
        <label>Jenis BBM (Produk)</label>
        <input type="text" id="produkInput" value="<?= esc($penerimaan['kode_produk']) ?>" class="form-control" readonly>
        <input type="hidden" id="produkHidden" name="kode_produk" value="<?= esc($penerimaan['kode_produk']) ?>">
      </div>

      <div class="form-group">
        <label>Volume DO</label>
        <input type="number" name="volume_do" value="<?= esc($penerimaan['volume_do']) ?>" step="0.01" class="form-control" required>
      </div>

      <div class="form-group">
        <label>Volume Diterima</label>
        <input type="number" name="volume_diterima" value="<?= esc($penerimaan['volume_diterima']) ?>" step="0.01" class="form-control" required>
      </div>

      <div class="form-group">
        <label>Harga Beli (per Liter)</label>
        <input type="number" name="harga_beli" value="<?= esc($penerimaan['harga_beli']) ?>" step="0.01" class="form-control">
      </div>

      <div class="form-group">
        <label>Nama Supir</label>
        <input type="text" name="supir" value="<?= esc($penerimaan['supir']) ?>" class="form-control">
      </div>

      <div class="form-group">
        <label>Catatan</label>
        <textarea name="catatan" class="form-control"><?= esc($penerimaan['catatan']) ?></textarea>
      </div>

      <button class="btn btn-success">Update</button>
    </div>
  </div>
</form>

<script>
  const produkInput = document.getElementById('produkInput');
  const produkHidden = document.getElementById('produkHidden');
  const tangkiSelect = document.getElementById('tangkiSelect');

  tangkiSelect.addEventListener('change', function () {
    let selected = tangkiSelect.options[tangkiSelect.selectedIndex];
    let kodeProduk = selected.dataset.produk || '';
    produkInput.value = kodeProduk;
    produkHidden.value = kodeProduk;
  });
</script>

<?= $this->endSection() ?>
