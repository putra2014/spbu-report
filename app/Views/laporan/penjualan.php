<table class="table">
    <thead>
        <tr>
            <th>Tanggal</th>
            <th>Shift</th>
            <th>Nozzle</th>
            <th>Volume</th>
            <th>Keterangan</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($sales as $sale): ?>
        <tr class="<?= $sale['is_adjusted'] ? 'table-warning' : '' ?>">
            <td><?= $sale['tanggal'] ?></td>
            <td><?= $sale['shift'] ?></td>
            <td><?= $sale['kode_nozzle'] ?></td>
            <td><?= number_format($sale['volume'], 2) ?> L</td>
            <td>
                <?php if ($sale['is_adjusted']): ?>
                <span class="badge bg-info">Telah Disesuaikan</span>
                <?php endif; ?>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>