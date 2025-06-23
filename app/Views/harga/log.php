<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<h4><?= $title ?></h4>

<table class="table table-bordered">
    <thead>
        <tr>
            <th>Harga Beli</th>
            <th>Harga Jual</th>
            <th>Tanggal Perubahan</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($log as $row): ?>
        <tr>
            <td>Rp <?= number_format($row['harga_beli'], 2, ',', '.') ?></td>
            <td>Rp <?= number_format($row['harga_jual'], 2, ',', '.') ?></td>
            <td><?= $row['changed_at'] ?></td>
        </tr>
        <?php endforeach ?>
    </tbody>
</table>

<a href="<?= base_url('harga') ?>" class="btn btn-secondary">Kembali</a>

<?= $this->endSection() ?>
