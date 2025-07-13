<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="container">
    <h1>Laporan Closing Harian</h1>
    
    <div class="row mb-3">
        <div class="col-md-4">
            <input type="date" class="form-control" id="filterTanggal" value="<?= date('Y-m-d') ?>">
        </div>
        <div class="col-md-2">
            <button class="btn btn-primary" onclick="filterLaporan()">Filter</button>
        </div>
    </div>
    
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Tangki</th>
                <th>Produk</th>
                <th>Stok Awal</th>
                <th>Penerimaan</th>
                <th>Penjualan</th>
                <th>Stok Teoritis</th>
                <th>Stok Real</th>
                <th>Selisih</th>
                <th>Closed At</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($stokHarian as $stok): ?>
                <tr>
                    <td><?= date('d/m/Y', strtotime($stok['tanggal'])) ?></td>
                    <td><?= $stok['kode_tangki'] ?></td>
                    <td><?= $stok['nama_produk'] ?></td>
                    <td><?= number_format($stok['stok_awal'], 2) ?></td>
                    <td><?= number_format($stok['penerimaan'], 2) ?></td>
                    <td><?= number_format($stok['penjualan'], 2) ?></td>
                    <td><?= number_format($stok['stok_teoritis'], 2) ?></td>
                    <td><?= number_format($stok['stok_real'], 2) ?></td>
                    <td class="<?= $stok['selisih'] > 0 ? 'text-danger' : 'text-success' ?>">
                        <?= number_format($stok['selisih'], 2) ?>
                    </td>
                    <td><?= date('H:i', strtotime($stok['closed_at'])) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<script>
function filterLaporan() {
    const tanggal = document.getElementById('filterTanggal').value;
    window.location.href = '/stok/laporan-harian?tanggal=' + tanggal;
}
</script>

<?= $this->endSection() ?>