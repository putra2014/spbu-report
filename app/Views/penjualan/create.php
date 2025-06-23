<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<h1>Tambah Penjualan Harian</h1>

<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
<?php endif; ?>

<form action="<?= base_url('penjualan/store') ?>" method="post">
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
        <label>Nozzle</label>
        <select name="nozzle_id" id="nozzle_select" class="form-control" required>
            <option value="">Pilih Nozzle</option>
            <?php foreach ($nozzleList as $n): ?>
                <option value="<?= $n['id'] ?>" 
                    data-last-meter="<?= $lastMeters[$n['id']] ?? $n['initial_meter'] ?? 0 ?>">
                    <?= $n['kode_nozzle'] ?> 
                    (Meter: <?= number_format($lastMeters[$n['id']] ?? $n['initial_meter'] ?? 0, 2) ?> L)
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="form-group">
        <label>Meter Awal</label>
        <input type="number" step="0.01" name="meter_awal" id="meter_awal_input"
               class="form-control" required>
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
    const nozzleSelect = document.getElementById('nozzle_select');
    const meterAwalInput = document.getElementById('meter_awal_input');
    
    // Set nilai awal saat pertama kali load
    if (nozzleSelect.value) {
        const selectedOption = nozzleSelect.options[nozzleSelect.selectedIndex];
        meterAwalInput.value = selectedOption.getAttribute('data-last-meter');
    }
    
    // Update saat nozzle berubah
    nozzleSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        meterAwalInput.value = selectedOption.getAttribute('data-last-meter');
    });
});

</script>

<?= $this->endSection() ?>