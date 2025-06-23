<div class="modal fade" id="resetModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Ajukan Reset Meter</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="/penjualan/request-reset" method="post">
        <?= csrf_field() ?> <!-- Tambahkan ini -->
        
        <div class="modal-body">
          <input type="hidden" name="nozzle_id" value="<?= $nozzle_id ?>">
          <input type="hidden" name="penjualan_id" value="<?= $penjualan_id ?? '' ?>">
          <input type="hidden" name="meter_awal_lama" value="<?= $meter_awal ?>">
          
          <div class="mb-3">
            <label class="form-label">Meter Awal Saat Ini</label>
            <input type="text" class="form-control" value="<?= number_format($meter_awal, 2) ?> L" readonly>
          </div>
          
          <div class="mb-3">
            <label class="form-label">Meter Awal Baru</label>
            <input type="number" class="form-control" name="meter_awal_baru" 
                   step="0.01" min="<?= $meter_awal ?>" 
                   value="<?= old('meter_awal_baru', $meter_awal + 1) ?>" required>
            <small class="text-danger"><?= session('errors.meter_awal_baru') ?? '' ?></small>
          </div>
          
          <div class="mb-3">
            <label class="form-label">Alasan Reset</label>
            <select class="form-select" name="alasan" required>
                <option value="">Pilih Alasan...</option>
                <option value="kalibrasi">Kalibrasi Nozzle</option>
                <option value="ganti_nozzle">Ganti Nozzle</option>
                <option value="maintenance">Maintenance Mesin</option>
                <option value="reset_total">Reset Total (Kembali ke 0)</option> <!-- Tambahkan opsi ini -->
                <option value="kerusakan_fisik">Kerusakan Fisik Meter</option>
                <option value="lainnya">Lainnya</option>
            </select>
            <small class="text-danger"><?= session('errors.alasan') ?? '' ?></small>
          </div>
          
          <?php if(session('errors')): ?>
            <div class="alert alert-danger">
              <?php foreach(session('errors') as $error): ?>
                <p><?= $error ?></p>
              <?php endforeach ?>
            </div>
          <?php endif; ?>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-primary">Ajukan Reset</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
document.querySelector('[name="alasan"]').addEventListener('change', function() {
  document.getElementById('notesField').style.display = 
    this.value === 'lainnya' ? 'block' : 'none';
});
</script>