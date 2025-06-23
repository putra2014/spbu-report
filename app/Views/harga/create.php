<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<h4><?= $title ?></h4>
<form action="<?= base_url('harga/store') ?>" method="post">
    <div class="form-group">
        <label>Produk BBM</label>
        <select name="kode_produk" class="form-control" required>
            <?php foreach ($produkList as $p): ?>
                <option value="<?= $p['id'] ?>"><?= $p['nama_produk'] ?></option>
            <?php endforeach ?>
        </select>
    </div>
    <div class="form-group">
        <label>Harga Beli</label>
        <input type="number" step="0.01" name="harga_beli" class="form-control" required>
    </div>
    <div class="form-group">
        <label>Harga Jual</label>
        <input type="number" step="0.01" name="harga_jual" class="form-control" required>
    </div>
    <button type="submit" class="btn btn-success">Simpan</button>
</form>

<?= $this->endSection() ?>
