<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="container">
    <h1>Input Hasil Dipping</h1>
    
    <div class="card mb-4">
        <div class="card-header">
            <h5>Detail Penerimaan</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4">
                    <p><strong>Tanggal:</strong> <?= date('d/m/Y', strtotime($penerimaan['tanggal'])) ?></p>
                </div>
                <div class="col-md-4">
                    <p><strong>No. DO:</strong> <?= $penerimaan['nomor_do'] ?></p>
                </div>
                <div class="col-md-4">
                    <p><strong>Volume DO:</strong> <?= number_format($penerimaan['volume_do'], 2) ?> Liter</p>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <p><strong>Tangki:</strong> <?= $tangki['kode_tangki'] ?> (<?= $tangki['kode_produk'] ?>)</p>
                </div>
                <div class="col-md-6">
                    <p><strong>Kapasitas:</strong> <?= number_format($tangki['kapasitas'], 2) ?> Liter</p>
                </div>
            </div>
        </div>
    </div>

    <form action="<?= base_url('/penerimaan/process-dipping/'.$penerimaan['id']) ?>" method="post">
        <div class="form-group">
            <label for="volume_diterima">Volume Diterima (Liter)</label>
            <input type="number" step="0.01" class="form-control" id="volume_diterima" 
                   name="volume_diterima" required>
            <small class="form-text text-muted">Masukkan hasil pengukuran dipping</small>
        </div>

        <button type="submit" class="btn btn-primary">Simpan Hasil Dipping</button>
        <a href="<?= base_url('/penerimaan') ?>" class="btn btn-secondary">Kembali</a>
    </form>
</div>

<script>
// Validasi client-side
document.querySelector('form').addEventListener('submit', function(e) {
    const volume = parseFloat(document.getElementById('volume_diterima').value);
    const kapasitas = parseFloat(<?= $tangki['kapasitas'] ?>);
    
    if (volume > kapasitas) {
        e.preventDefault();
        alert('Volume melebihi kapasitas tangki!');
    }
});
</script>

<?= $this->endSection() ?>