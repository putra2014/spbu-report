<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<h1>Tambah Penjualan Harian</h1>

<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
<?php endif; ?>

<form action="<?= base_url('penjualan/store') ?>" method="post">
    <?php if (session()->get('role') === 'admin_spbu'): ?>
                    <input type="hidden" name="kode_spbu" value="<?= session()->get('kode_spbu') ?>">
                    <p><strong>SPBU <?= session()->get('kode_spbu') ?></strong></p>
                <?php else: ?>
                    <div class="form-group">
                        <label>SPBU</label>
                        <select name="kode_spbu" class="form-control" required>
                            <option value="">Pilih SPBU</option>
                            <?php foreach ($spbuList as $s): ?>
                                <option value="<?= $s['kode_spbu'] ?>"><?= $s['kode_spbu'] ?> - <?= $s['nama_spbu'] ?></option>
                            <?php endforeach ?>
                        </select>
                    </div>
                <?php endif ?>
    <div class="form-group">
        <label>Tanggal</label>
        <input type="date" name="tanggal" class="form-control" 
               value="<?= date('Y-m-d') ?>" required>
    </div>

    <div class="form-group">
        <label>Shift</label>
        <select name="shift" class="form-control" required>
            <option value="1">Shift 1</option>
            <option value="2">Shift 2</option>
            <option value="3">Shift 3</option>
        </select>
    </div>

    <div class="form-group">
        <label>Dispenser</label>
        <select name="dispenser_id" class="form-control" id="dispenser-select" required>
            <option value="">Pilih Dispenser</option>
            <?php foreach ($dispensers as $dispenser): ?>
                <option value="<?= $dispenser['id'] ?>">
                    <?= $dispenser['kode_dispenser'] ?> - <?= $dispenser['merek_dispenser'] ?>
                </option>
            <?php endforeach ?>
        </select>
    </div>

    <div class="form-group">
        <label>Nozzle</label>
        <select name="nozzle_id" class="form-control" id="nozzle-select" disabled required>
            <option value="">Pilih Nozzle</option>
        </select>
    </div>

    <div class="form-group">
        <label>Meter Awal (L)</label>
        <input type="number" name="meter_awal" id="meter-awal" 
               class="form-control" readonly required step="0.01">
    </div>

    <div class="form-group">
        <label>Meter Akhir</label>
        <input type="number" step="0.01" name="meter_akhir" class="form-control" required>
    </div>

    <div class="form-group">
        <label>Operator</label>
        <select name="operator_id" class="form-control" required>
            <?php foreach ($operatorList as $o): ?>
                <option value="<?= $o['id'] ?>"><?= $o['nama_operator'] ?></option>
            <?php endforeach; ?>
        </select>
    </div>

    <button type="submit" class="btn btn-success">Simpan</button>
</form>


<script>
document.addEventListener('DOMContentLoaded', function() {
    // Deklarasi semua elemen
    const form = document.querySelector('form');
    const dispenserSelect = document.getElementById('dispenser-select');
    const nozzleSelect = document.getElementById('nozzle-select');
    const meterAwalInput = document.getElementById('meter-awal');
    const meterAkhirInput = document.querySelector('input[name="meter_akhir"]');
    const tanggalInput = document.querySelector('input[name="tanggal"]');
    const operatorSelect = document.querySelector('select[name="operator_id"]');
    const submitBtn = document.querySelector('button[type="submit"]');

    // 1. Handler Dispenser -> Nozzle
    dispenserSelect.addEventListener('change', async function() {
        const dispenserId = this.value;
        
        if (!dispenserId) {
            resetNozzleSelection();
            return;
        }

        try {
            setLoadingState(true);
            nozzleSelect.disabled = true;

            const response = await fetch(`<?= base_url() ?>penjualan/get-nozzles?dispenser_id=${dispenserId}`)

            if (!response.ok) {
                const errorData = await response.json();
                throw new Error(errorData.error || `HTTP error! status: ${response.status}`);
            }

            const nozzles = await response.json();

            if (!Array.isArray(nozzles)) {
                throw new Error('Format data nozzle tidak valid');
            }

            updateNozzleOptions(nozzles);

        } catch (error) {
            console.error('Error loading nozzles:', error);
            showError(nozzleSelect, error.message || 'Gagal memuat nozzle');
            resetNozzleSelection();
        } finally {
            setLoadingState(false);
        }
    });

    // 2. Handler Nozzle -> Meter Awal
    nozzleSelect.addEventListener('change', function() {
        const selectedNozzle = this.options[this.selectedIndex];
        
        if (selectedNozzle.value) {
            meterAwalInput.value = selectedNozzle.dataset.meter || '';
            validateMeter();
        } else {
            meterAwalInput.value = '';
        }
    });

    // 3. Validasi Meter Akhir
    meterAkhirInput.addEventListener('input', validateMeter);

    // 4. Handler Tanggal (opsional)
    tanggalInput.addEventListener('change', function() {
        if (nozzleSelect.value) {
            // Jika perlu update meter berdasarkan tanggal
            updateMeterBasedOnDate();
        }
    });

    // 5. Form Submission
    form.addEventListener('submit', function() {
    const meterAwal = parseFloat(document.getElementById('meter-awal').value);
    const meterAkhir = parseFloat(document.querySelector('input[name="meter_akhir"]').value);
    
    document.getElementById('meter-awal').value = meterAwal;
    document.querySelector('input[name="meter_akhir"]').value = meterAkhir;
    
    console.log('Submitting - Awal:', meterAwal, 'Akhir:', meterAkhir);
});

    // ===== FUNGSI PENDUKUNG =====

    function resetNozzleSelection() {
        nozzleSelect.innerHTML = '<option value="">Pilih Nozzle</option>';
        nozzleSelect.disabled = true;
        meterAwalInput.value = '';
    }

    function updateNozzleOptions(nozzles) {
        nozzleSelect.innerHTML = '<option value="">Pilih Nozzle</option>';
        
        nozzles.forEach(nozzle => {
            const option = document.createElement('option');
            option.value = nozzle.id;
            option.textContent = `${nozzle.kode_nozzle} (${nozzle.current_meter} L)`;
            option.dataset.meter = nozzle.current_meter;
            nozzleSelect.appendChild(option);
        });
        
        nozzleSelect.disabled = false;
    }

    function validateMeter() {
        const meterAwal = parseFloat(meterAwalInput.value) || 0;
        const meterAkhir = parseFloat(meterAkhirInput.value) || 0;
        
        if (meterAkhir <= meterAwal) {
            meterAkhirInput.setCustomValidity('Meter akhir harus lebih besar dari meter awal');
        } else {
            meterAkhirInput.setCustomValidity('');
        }
    }

    function validateForm() {
        let isValid = true;
        
        // Cek semua required field
        form.querySelectorAll('[required]').forEach(field => {
            if (!field.value.trim()) {
                showError(field, 'Field ini wajib diisi');
                isValid = false;
            }
        });

        // Validasi khusus
        if (parseFloat(meterAkhirInput.value) <= parseFloat(meterAwalInput.value)) {
            showError(meterAkhirInput, 'Meter akhir harus lebih besar');
            isValid = false;
        }

        return isValid;
    }

    function showError(element, message) {
        const errorElement = document.createElement('div');
        errorElement.className = 'text-danger small mt-1';
        errorElement.textContent = message;
        
        // Hapus error sebelumnya
        const existingError = element.nextElementSibling;
        if (existingError && existingError.classList.contains('text-danger')) {
            existingError.remove();
        }
        
        element.insertAdjacentElement('afterend', errorElement);
        element.focus();
    }

    function setLoadingState(isLoading) {
        if (isLoading) {
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Memproses...';
        } else {
            submitBtn.disabled = false;
            submitBtn.textContent = 'Simpan';
        }
    }

    async function updateMeterBasedOnDate() {
        try {
            const response = await fetch(`/penjualan/get-last-meter/${nozzleSelect.value}?tanggal=${tanggalInput.value}`);
            const data = await response.json();
            
            if (data.success) {
                meterAwalInput.value = data.lastMeter;
            }
        } catch (error) {
            console.error('Error updating meter:', error);
        }
    }
});
</script>
<?= $this->endSection() ?>