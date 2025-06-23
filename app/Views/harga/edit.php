<?= $this->extend('layout/main') ?>
<?= $this->section('content') ?>

<h4><?= $title ?></h4>
<form action="<?= base_url('harga/update/'.$harga['id']) ?>" method="post">
    <div class="form-group">
        <label>Produk</label>
        <input type="text" class="form-control" value="<?= $produk['nama_produk'] ?>" disabled>
    </div>
    <div class="form-group">
        <label>Harga Beli</label>
        <input type="number" step="0.01" name="harga_beli" value="<?= $harga['harga_beli'] ?>" class="form-control" required>
    </div>
    <div class="form-group">
        <label>Harga Jual</label>
        <input type="number" step="0.01" name="harga_jual" value="<?= $harga['harga_jual'] ?>" class="form-control" required>
    </div>
    <button type="submit" class="btn btn-primary">Simpan</button>
</form>

<?= $this->endSection() ?>
