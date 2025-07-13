<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<!-- Form Input -->
<div class="card">
    <div class="card-body">
        <form action="<?= base_url('nozzle-test/store') ?>" method="post">
            <!-- Input Tanggal -->
            <div class="form-group">
                <label>Tanggal Penjualan</label>
                <input type="date" name="tanggal_penjualan" id="tanggal-penjualan" class="form-control" required>
            </div>

            <!-- Dropdown Dispenser -->
            <div class="form-group">
                <label>Dispenser</label>
                <select name="dispenser_id" id="dispenser-select" class="form-control" required>
                    <option value="">Pilih Dispenser</option>
                    <?php foreach ($dispensers as $d): ?>
                        <option value="<?= $d['id'] ?>">
                            <?= $d['kode_dispenser'] ?> - <?= $d['merek_dispenser'] ?>
                        </option>
                    <?php endforeach ?>
                </select>
            </div>

            <!-- Dropdown Nozzle -->
            <div class="form-group">
                <label>Nozzle</label>
                <select name="nozzle_id" id="nozzle-select" class="form-control" required disabled>
                    <option value="">Pilih Dispenser terlebih dahulu</option>
                </select>
            </div>

            <!-- Dropdown Data Penjualan -->
            <div class="form-group">
                <label>Data Penjualan</label>
                <select name="penjualan_id" id="penjualan-select" class="form-control" required disabled>
                    <option value="">Isi filter di atas terlebih dahulu</option>
                </select>
            </div>

            <!-- Input Volume Test -->
            <div class="form-group">
                <label>Volume Test (Liter)</label>
                <input type="number" step="0.01" name="volume_test" class="form-control" required>
            </div>

            <button type="submit" class="btn btn-primary">Simpan</button>
        </form>
    </div>
</div>

<?= $this->section('scripts') ?>
<script>
if (typeof jQuery == 'undefined') {
    console.error('jQuery not loaded!');
} else {
    console.log('jQuery version:', jQuery.fn.jquery);
}

$(document).ready(function() {
    console.log('Document ready - nozzle test form initialized');
    
    // 1. HANDLER DISPENSER -> NOZZLE
    $('#dispenser-select').on('change', function() {
        console.log('Dispenser changed:', this.value);
        const dispenserId = $(this).val();
        const $nozzleSelect = $('#nozzle-select');
        
        console.log('Dispenser changed:', dispenserId);
        
        $nozzleSelect.html('<option value="">Memuat nozzle...</option>').prop('disabled', true);
        
        if (!dispenserId) {
            $nozzleSelect.html('<option value="">Pilih Dispenser</option>');
            return;
        }
        
        // AJAX dengan error handling eksplisit
        $.ajax({
            url: `<?= base_url('nozzle-test/get-nozzles') ?>?dispenser_id=${dispenserId}`,
            type: 'GET',
            dataType: 'json',
            cache: false, // Pastikan tidak pakai cache
            success: function(data) {
                console.log('Nozzle data received:', data);
                
                if (data && data.length > 0) {
                    let options = '<option value="">Pilih Nozzle</option>';
                    $.each(data, function(i, nozzle) {
                        options += `<option value="${nozzle.id}">${nozzle.kode_nozzle}</option>`;
                    });
                    $nozzleSelect.html(options).prop('disabled', false);
                } else {
                    $nozzleSelect.html('<option value="">Tidak ada nozzle</option>');
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', status, error);
                $nozzleSelect.html('<option value="">Error memuat nozzle</option>');
            }
        });
    });

    // 2. HANDLER PENJUALAN
    function loadPenjualan() {
        const tanggal = $('#tanggal-penjualan').val();
        const dispenserId = $('#dispenser-select').val();
        const nozzleId = $('#nozzle-select').val();
        const $penjualanSelect = $('#penjualan-select');
        
        if (!tanggal || !dispenserId || !nozzleId) {
            $penjualanSelect.html('<option value="">Isi semua filter</option>').prop('disabled', true);
            return;
        }
        
        $penjualanSelect.html('<option value="">Memuat penjualan...</option>');
        
        $.ajax({
            url: `<?= base_url('nozzle-test/get-penjualan') ?>`,
            data: {
                tanggal: tanggal,
                dispenser_id: dispenserId,
                nozzle_id: nozzleId
            },
            success: function(data) {
                console.log('Penjualan data:', data);
                
                if (data && data.length > 0) {
                    let options = '<option value="">Pilih Penjualan</option>';
                    $.each(data, function(i, p) {
                        const date = new Date(p.tanggal);
                        options += `<option value="${p.id}">${date.toLocaleDateString('id-ID')} - Shift ${p.shift}</option>`;
                    });
                    $penjualanSelect.html(options).prop('disabled', false);
                } else {
                    $penjualanSelect.html('<option value="">Tidak ada penjualan</option>');
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', status, error);
                $penjualanSelect.html('<option value="">Error memuat data</option>');
            }
        });
    }

    // Trigger handlers
    $('#tanggal-penjualan, #nozzle-select').change(loadPenjualan);
    
    // Force refresh handler (untuk kasus cache)
    $('#dispenser-select').trigger('change');
});
</script>
<?= $this->endSection() ?>
<?= $this->endSection() ?>