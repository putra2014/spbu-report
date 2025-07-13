<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="container">
    <h1 class="mb-4">Input Stok Real (Tank Dipping)</h1>

    <div class="alert alert-info">
    <strong>Petunjuk Initial Stock:</strong> 
    Untuk stok awal SPBU baru, input nilai stok real sesuai penerimaan BBM pertama.
</div>

        <form action="<?= base_url('/stok/simpan') ?>" method="post">
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label>Tangki</label>
                    <select name="tangki_id" class="form-control" required>
                        <option value="">Pilih Tangki</option>
                        <?php foreach ($tangki as $t): ?>
                            <option value="<?= $t['id'] ?>">
                                <?= $t['kode_tangki'] ?> (<?= $t['kode_produk'] ?>)
                            </option>
                        <?php endforeach ?>
                    </select>
                </div>

                <div class="form-group">
                    <label>Stok Awal</label>
                    <input type="text" class="form-control" id="stok_awal" readonly>
                </div>

                <div class="form-group">
                    <label>Stok Real (Liter)</label>
                    <input type="number" step="0.01" name="stok_real" class="form-control" required>
                </div>
            </div>

            <div class="col-md-6">
                <div class="form-group">
                    <label>Evaporation Loss (Liter)</label>
                    <input type="number" step="0.01" name="evaporation_loss" class="form-control" value="0">
                </div>

                <div class="form-group">
                    <label>Keterangan Loss</label>
                    <select name="keterangan_loss" class="form-control">
                        <option value="">Tidak ada</option>
                        <option value="kebocoran">Kebocoran</option>
                        <option value="salah_ukur">Salah Ukur</option>
                        <option value="evaporasi">Evaporasi</option>
                        <option value="lainnya">Lainnya</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Stok Teoritis</label>
                    <div class="form-control-plaintext" id="preview-teoritis">0.00</div>
                </div>
                <div class="form-group">
                    <label>Selisih</label>
                    <div class="form-control-plaintext" id="preview-selisih">0.00</div>
                </div>

                <div class="form-group">
                    <label>Catatan</label>
                    <textarea name="catatan" class="form-control"></textarea>
                </div>
            </div>
        </div>

        <button type="submit" class="btn btn-primary">Simpan</button>
    </form>
</div>

<script>
document.querySelector('[name="tangki_id"]').addEventListener('change', function() {
    const tangkiId = this.value;
    const lastStokData = <?= json_encode($lastStok) ?>;
    const isInitial = lastStokData[this.value] == null;
    if (isInitial) {
        alert('Ini adalah input stok awal. Pastikan nilai sesuai fisik di tangki!');
    }    
    // Default ke 0 jika tidak ada data
    document.getElementById('stok_awal').value = lastStokData[tangkiId] || 0;
});
document.querySelector('[name="stok_real"]').addEventListener('change', function() {
    const stokReal = parseFloat(this.value);
    const stokAwal = parseFloat(document.getElementById('stok_awal').value);
    const penerimaan = 0; // Bisa diambil dari data jika ada
    const penjualan = 0;  // Bisa diambil dari data jika ada
    
    const teoritis = stokAwal + penerimaan - penjualan;
    const selisih = teoritis - stokReal;
    
    // Tampilkan preview
    document.getElementById('preview-teoritis').textContent = teoritis.toFixed(2);
    document.getElementById('preview-selisih').textContent = selisih.toFixed(2);
    document.getElementById('preview-selisih').className = 
        (selisih > 0) ? 'text-danger' : 'text-success';
    
    // Validasi kapasitas
    const kapasitas = parseFloat(document.querySelector('[name="tangki_id"] option:checked').dataset.kapasitas);
    if (stokReal > kapasitas) {
        alert('Stok real melebihi kapasitas tangki!');
        this.value = '';
    }
});

</script>
<?= $this->endSection() ?>