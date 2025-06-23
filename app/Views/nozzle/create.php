<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<h4>Tambah Nozzle</h4>
<?php if (session()->getFlashdata('error')): ?>
<div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
<?php endif ?>

<form action="<?= base_url('/nozzle/store') ?>" method="post">
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
                    <label>Kode Nozzle</label>
                    <select name="kode_nozzle" class="form-control" required>
                        <option value="">Pilih Kode</option>
                        <?php foreach ($kodeNozzleList as $k): ?>
                            <option value="<?= $k['kode_nozzle'] ?>"><?= $k['kode_nozzle'] ?></option>
                        <?php endforeach ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Dispenser</label>
                    <select name="dispenser_id" class="form-control" required>
                        <?php foreach ($dispenserList as $d): ?>
                            <option value="<?= $d['id'] ?>"><?= $d['kode_dispenser'] ?></option>
                        <?php endforeach ?>
                    </select>
                    <script>
                        document.addEventListener('DOMContentLoaded', function () {
                            const dispenserSelect = document.querySelector('select[name="dispenser_id"]');
                            const infoArea = document.getElementById('info-jumlah-nozzle');
                        
                            dispenserSelect.addEventListener('change', function () {
                                const id = this.value;
                                if (!id) return;
                            
                                fetch(`<?= base_url('/ajax/jumlah-nozzle/') ?>${id}`)
                                    .then(res => res.json())
                                    .then(data => {
                                        infoArea.innerHTML = `Nozzle saat ini: <strong>${data.jumlah}</strong> dari maksimal <strong>${data.maksimal}</strong>`;
                                    });
                            });
                        });
                    </script>

                    <small id="info-jumlah-nozzle" class="text-muted d-block mt-1"></small>
                </div>
                <div class="form-group">
                    <label>Tangki</label>
                    <select name="tangki_id" class="form-control" required>
                        <option value="">Pilih Tangki</option>
                        <?php foreach ($tangkiList as $t): ?>
                            <option value="<?= $t['id'] ?>">
                                <?= $t['kode_tangki'] ?> (<?= $t['kode_produk'] ?>)
                            </option>
                        <?php endforeach ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Status</label>
                    <select name="status" class="form-control">
                        <option value="Aktif">Aktif</option>
                        <option value="Nonaktif">Nonaktif</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Catatan</label>
                    <textarea name="catatan" class="form-control"></textarea>
                </div>
    <button type="submit" class="btn btn-success">Simpan</button>
</form>

<script>
document.querySelector('select[name="tangki_id"]').addEventListener('change', function() {
    const selectedOption = this.options[this.selectedIndex];
    if (selectedOption.text.includes('(null)') || !selectedOption.text.includes('(')) {
        alert('Pilih tangki yang memiliki produk terkait');
        this.value = '';
    }
});
</script>
<?= $this->endSection() ?>
