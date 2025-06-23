<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Form Reset Meter Nozzle</h3>
    </div>
    <div class="card-body">
        <form action="<?= base_url('penjualan/handle-reset') ?>" method="post" enctype="multipart/form-data">
            <?= csrf_field() ?>
            
            <div class="form-group">
                <label>Pilih Nozzle</label>
                <select name="nozzle_id" class="form-control" required>
                    <?php foreach ($nozzles as $nozzle): ?>
                        <option value="<?= $nozzle['id'] ?>">
                            <?= $nozzle['kode_nozzle'] ?> 
                            (Meter Terakhir: <?= $nozzle['last_meter'] ? number_format($nozzle['last_meter'], 2).' L' : 'Belum ada data' ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label>Jenis Reset*</label>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="reset_type" id="resetPhysical" value="physical" checked required>
                    <label class="form-check-label" for="resetPhysical">
                        Fisik (Kerusakan/Kalibrasi/Reset Periodik)
                    </label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="reset_type" id="resetCorrection" value="correction" required>
                    <label class="form-check-label" for="resetCorrection">
                        Koreksi Data Penjualan
                    </label>
                </div>
            </div>

            <div id="correctionFields" style="display:none;">
                <div class="form-group">
                    <label>Penjualan yang Dikoreksi*</label>
                    <select name="penjualan_id" class="form-control">
                        <?php if (!empty($lastSales)): ?>
                            <?php foreach ($lastSales as $sale): ?>
                                <option value="<?= $sale['id'] ?>" 
                                    data-meter-awal="<?= $sale['meter_awal'] ?>"
                                    data-meter-akhir="<?= $sale['meter_akhir'] ?>">
                                    Shift <?= $sale['shift'] ?> | 
                                    <?= date('d/m/Y', strtotime($sale['tanggal'])) ?> | 
                                    Nozzle <?= $sale['kode_nozzle'] ?> | 
                                    Meter: <?= number_format($sale['meter_awal'], 2) ?> → <?= number_format($sale['meter_akhir'], 2) ?>
                                </option>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <option value="">Tidak ada data penjualan</option>
                        <?php endif; ?>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label>Meter Awal Baru*</label>
                <input type="number" step="0.01" name="meter_awal_baru" class="form-control" required>
            </div>

            <div class="form-group">
                <label>Alasan Reset*</label>
                <select name="alasan" class="form-control" required>
                    <option value="kerusakan">Kerusakan Fisik</option>
                    <option value="kalibrasi">Kalibrasi</option>
                    <option value="salah_input">Salah Input Data</option>
                    <option value="lainnya">Lainnya</option>
                </select>
            </div>

            <div class="form-group">
                <label>Upload Bukti (Opsional)</label>
                <input type="file" name="bukti_reset">
                <small class="text-muted">Format: JPG/PNG (max 2MB)</small>
            </div>

            <div class="form-group">
                <label>Catatan Tambahan</label>
                <textarea name="catatan" class="form-control" rows="3"></textarea>
            </div>

            <button type="submit" class="btn btn-primary">Ajukan Reset</button>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const resetTypeRadios = document.querySelectorAll('input[name="reset_type"]');
    const meterBaruInput = document.querySelector('input[name="meter_awal_baru"]');
    const penjualanSelect = document.querySelector('select[name="penjualan_id"]');
    
    resetTypeRadios.forEach(radio => {
        radio.addEventListener('change', function() {
            if (this.value === 'correction' && penjualanSelect.value) {
                // Dapatkan meter awal dari data penjualan yang dipilih
                const selectedOption = penjualanSelect.options[penjualanSelect.selectedIndex];
                const meterAwal = parseFloat(selectedOption.dataset.meterAwal);
                
                // Set validation attribute
                meterBaruInput.min = meterAwal.toFixed(2);
                meterBaruInput.title = `Nilai harus ≥ ${meterAwal.toFixed(2)} L`;
            } else {
                meterBaruInput.removeAttribute('min');
                meterBaruInput.removeAttribute('title');
            }
        });
    });
    
    // Trigger change event saat select penjualan berubah
    penjualanSelect.addEventListener('change', function() {
        document.querySelector('input[name="reset_type"][value="correction"]').dispatchEvent(new Event('change'));
    });
});
</script>
<?= $this->endSection() ?>