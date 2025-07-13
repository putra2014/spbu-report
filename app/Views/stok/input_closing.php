<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="container">
    <h1>Input Stok Closing - <?= date('d/m/Y', strtotime($tanggal)) ?></h1>

    <form action="<?= base_url('/stok/simpan-closing') ?>" method="post">
        <input type="hidden" name="tanggal" value="<?= $tanggal ?>">
        
        <table class="table table-bordered">
            <thead class="thead-dark">
                <tr>
                    <th>Tangki</th>
                    <th>Produk</th>
                    <th>Stok Awal</th>
                    <th>Penerimaan</th>
                    <th>Penjualan</th>
                    <th>Stok Teoritis</th>
                    <th>Stok Real</th>
                    <th>Catatan</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($stok as $s): ?>
                    <tr>
                        <td><?= $s['kode_tangki'] ?>
                            <input type="hidden" name="tangki_id[]" value="<?= $s['id'] ?>">
                        </td>
                        <td><?= $s['kode_produk'] ?></td>
                        <td><?= number_format($s['stok_awal'], 2) ?></td>
                        <td><?= number_format($s['penerimaan'], 2) ?></td>
                        <td><?= number_format($s['penjualan'], 2) ?></td>
                        <td>
                            <?= number_format($s['stok_awal'] + $s['penerimaan'] - $s['penjualan'], 2) ?>
                        </td>
                        <td>
                            <input type="number" step="0.01" 
                                   name="stok_real[]" 
                                   class="form-control" 
                                   value="<?= number_format($s['stok_awal'] + $s['penerimaan'] - $s['penjualan'], 2) ?>" 
                                   required>
                        </td>
                        <td>
                            <input type="text" name="catatan[]" class="form-control">
                        </td>
                    </tr>
                <?php endforeach ?>
            </tbody>
        </table>
        <tfoot>
            <tr class="table-active">
                <th colspan="5">TOTAL</th>
                <th><?= number_format($total['stok_awal'], 2) ?></th>
                <th><?= number_format($total['penerimaan'], 2) ?></th>
                <th><?= number_format($total['penjualan'], 2) ?></th>
                <th><?= number_format($total['stok_teoritis'], 2) ?></th>
                <th><?= number_format($total['stok_real'], 2) ?></th>
                <th class="<?= ($total['selisih'] > 0) ? 'text-danger' : 'text-success' ?>">
                    <?= number_format($total['selisih'], 2) ?>
                </th>
                <th></th>
            </tr>
        </tfoot>

        <button type="submit" class="btn btn-primary">
            <i class="fas fa-save"></i> Simpan Closing
        </button>
    </form>
</div>

<script>
// Validasi stok real
document.querySelector('form').addEventListener('submit', function(e) {
    const inputs = document.querySelectorAll('input[name="stok_real[]"]');
    let isValid = true;
    
    inputs.forEach(input => {
        if (parseFloat(input.value) < 0) {
            alert('Stok real tidak boleh negatif');
            isValid = false;
        }
    });
    
    if (!isValid) {
        e.preventDefault();
    }
});

document.querySelector('form').addEventListener('submit', function(e) {
    const inputs = document.querySelectorAll('input[name="stok_real[]"]');
    let isValid = true;
    
    inputs.forEach(input => {
        const teoritis = parseFloat(input.closest('tr').querySelector('td:nth-child(6)').textContent.replace(',',''));
        const real = parseFloat(input.value);
        const selisih = teoritis - real;
        const toleransi = teoritis * 0.005; // 0.5%
        
        if (Math.abs(selisih) > toleransi) {
            alert(`Selisih untuk Tangki ${input.closest('tr').querySelector('td:first-child').textContent} melebihi toleransi!`);
            isValid = false;
        }
    });
    
    if (!isValid) {
        e.preventDefault();
        alert('Terdapat selisih yang melebihi toleransi. Harap periksa kembali!');
    }
});
</script>

<?= $this->endSection() ?>