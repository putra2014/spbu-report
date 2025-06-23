<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<table class="table table-bordered">
    <thead>
        <tr>
            <a href="<?= base_url('tangki') ?>" class="btn btn-danger mb-2">Edit Tangki</a>
        </tr>
    </thead>
    <tbody>
</table>

<div class="card card-body">
  <div class="row">
    <div class="col-md-6">
<form action="<?= base_url('tangki/update/'.$tangki['id']) ?>" method="post">
    <div class="form-group">
        <label>SPBU</label>
        <select name="kode_spbu" class="form-control" required>
            <?php foreach ($spbuList as $s): ?>
                <option value="<?= $s['kode_spbu'] ?>" <?= $s['kode_spbu'] == $tangki['kode_spbu'] ? 'selected' : '' ?>>
                    <?= esc($s['nama_spbu']) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="form-group">
      <label for="kode_tangki">Kode Tangki</label>
      <select name="kode_tangki" class="form-control" required>
        <option value="">Pilih Kode Tangki</option>
        <?php foreach (range(1, 10) as $i): ?>
          <option value="T<?= $i ?>" <?= isset($tangki['kode_tangki']) && $tangki['kode_tangki'] == 'T' . $i ? 'selected' : '' ?>>T<?= $i ?></option>
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
            <option value="">Pilih Product</option>
            <?php foreach ($produkList as $p): ?>
                <option value="<?= $p['kode_produk'] ?>" <?= $p['kode_produk'] == $tangki['kode_produk'] ? 'selected' : '' ?>>
                    <?= esc($p['nama_produk']) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="form-group">
        <label>Kapasitas (Liter)</label>
        <input type="number" name="kapasitas" class="form-control" value="<?= esc($tangki['kapasitas']) ?>" required>
    </div>
    <button type="submit" class="btn btn-success mt-2">Update</button>
</form>

<?= $this->endSection() ?>