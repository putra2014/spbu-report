<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<h4><?= $title ?></h4>
<a href="<?= base_url('harga/create') ?>" class="btn btn-primary mb-3">+ Tambah Harga BBM</a>
<table class="table table-bordered">
    <thead>
        <tr>
            <th>Produk</th>
            <th>Harga Beli</th>
            <th>Harga Jual</th>
            <th>Terakhir Update</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($harga as $h): ?>
        <tr>
            <td><?= $h['nama_produk'] ?></td>
            <td>Rp <?= number_format($h['harga_beli'], 2, ',', '.') ?></td>
            <td>Rp <?= number_format($h['harga_jual'], 2, ',', '.') ?></td>
            <td><?= $h['updated_at'] ?></td>
            <td>
                <a href="<?= base_url('harga/edit/'.$h['id']) ?>" class="btn btn-sm btn-warning">Update</a>
                <a href="<?= base_url('harga/delete/'.$h['id']) ?>" class="btn btn-sm btn-danger"
                   onclick="return confirm('Hapus harga ini?')">Hapus</a>
                <a href="<?= base_url('harga/log/'.$h['kode_produk']) ?>" class="btn btn-sm btn-info">Riwayat</a>
</td>

        </tr>
        <?php endforeach ?>
    </tbody>
</table>

<?= $this->endSection() ?>
