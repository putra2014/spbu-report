<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="container">
    <h1 class="mb-4">Initial Stok Awal</h1>

    <form method="post">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Tangki</th>
                    <th>Produk</th>
                    <th>Stok Awal (Liter)</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($tangki as $t): ?>
                <tr>
                    <td><?= $t['kode_tangki'] ?></td>
                    <td><?= $t['kode_produk'] ?></td>
                    <td>
                        <input type="number" step="0.01" name="stok_<?= $t['id'] ?>" 
                               class="form-control" required>
                    </td>
                </tr>
                <?php endforeach ?>
            </tbody>
        </table>
        <button type="submit" class="btn btn-primary">Simpan Stok Awal</button>
    </form>
</div>
<?= $this->endSection() ?>