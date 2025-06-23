<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<h3>Log Penjualan Harian</h3>

<table class="table table-bordered">
    <thead>
    <tr>
        <th>Tanggal</th>
        <th>Nozzle</th>
        <th>Shift</th>
        <th>Meter Awal</th>
        <th>Meter Akhir</th>
        <th>Volume</th>
        <th>Harga Jual</th>
        <th>Total Penjualan</th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($log as $row): ?>
        <tr>
            <td><?= $row['tanggal'] ?></td>
            <td><?= $row['nozzle_id'] ?></td>
            <td><?= $row['shift'] ?></td>
            <td><?= $row['meter_awal'] ?></td>
            <td><?= $row['meter_akhir'] ?></td>
            <td><?= $row['volume'] ?></td>
            <td><?= number_format($row['harga_jual']) ?></td>
            <td><?= number_format($row['total_penjualan']) ?></td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>

<?= $this->endSection() ?>