<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="container">
    <h1 class="mb-4">Laporan Stok BBM</h1>
    
    <form class="mb-4 row">
        <div class="col-md-3">
            <input type="date" name="start_date" class="form-control" value="<?= $startDate ?>">
        </div>
        <div class="col-md-3">
            <input type="date" name="end_date" class="form-control" value="<?= $endDate ?>">
        </div>
        <div class="col-md-3">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="group_by_day" id="groupByDay" value="1" <?= $groupByDay ? 'checked' : '' ?>>
                <label class="form-check-label" for="groupByDay">
                    Tampilkan Closing Harian
                </label>
            </div>
        </div>
        <div class="col-md-3">
            <button type="submit" class="btn btn-primary">Filter</button>
        </div>
    </form>
    
    <table class="table table-bordered">
        <thead>
            <tr>
                <?php if (!$groupByDay): ?>
                    <th>Shift</th>
                <?php endif; ?>
                <th>Tanggal</th>
                <th>Tangki</th>
                <th>Produk</th>
                <th>Stok Awal</th>
                <th>Penerimaan</th>
                <th>Penjualan</th>
                <th>Stok Teoritis</th>
                <th>Stok Real</th>
                <th>Selisih</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($stok as $s): ?>
                <tr>
                    <?php if (!$groupByDay): ?>
                        <td><?= $s['is_closing'] ? 'Closing' : $s['shift'] ?></td>
                    <?php endif; ?>
                    <td><?= date('d/m/Y', strtotime($s['tanggal'])) ?></td>
                    <td><?= $s['kode_tangki'] ?></td>
                    <td><?= $s['nama_produk'] ?></td>
                    <td><?= number_format($s['stok_awal'], 2) ?></td>
                    <td><?= number_format($s['penerimaan'], 2) ?></td>
                    <td><?= number_format($s['penjualan'], 2) ?></td>
                    <td><?= number_format($s['stok_awal'] + $s['penerimaan'] - $s['penjualan'], 2) ?></td>
                    <td><?= number_format($s['stok_real'], 2) ?></td>
                    <td class="<?= ($s['stok_awal'] + $s['penerimaan'] - $s['penjualan'] - $s['stok_real']) > 0 ? 'text-danger' : 'text-success' ?>">
                        <?= number_format($s['stok_awal'] + $s['penerimaan'] - $s['penjualan'] - $s['stok_real'], 2) ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?= $this->endSection() ?>