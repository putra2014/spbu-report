<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<h4>Edit Nozzle</h4>
<form action="<?= base_url('/nozzle/update/' . $nozzle['id']) ?>" method="post">

    <div class="form-group">
        <label>Kode Nozzle</label>
        <select name="kode_nozzle" class="form-control" required>
            <option value="">Pilih Kode</option>
            <?php foreach ($kodeNozzleList as $k): ?>
                <option value="<?= $k['kode_nozzle'] ?>" <?= $k['kode_nozzle'] == $nozzle['kode_nozzle'] ? 'selected' : '' ?>>
                    <?= $k['kode_nozzle'] ?>
                </option>
            <?php endforeach ?>
        </select>
    </div>

    <div class="form-group">
        <label>Dispenser</label>
        <select name="dispenser_id" class="form-control" required>
            <?php foreach ($dispenserList as $d): ?>
                <option value="<?= $d['id'] ?>" <?= $d['id'] == $nozzle['dispenser_id'] ? 'selected' : '' ?>>
                    <?= $d['kode_dispenser'] ?>
                </option>
            <?php endforeach ?>
        </select>
    </div>

    <div class="form-group">
        <label>Tangki</label>
        <select name="tangki_id" class="form-control" required>
            <option value="">Pilih Tangki</option>
            <?php foreach ($tangkiList as $t): ?>
                <option value="<?= $t['id'] ?>" <?= $t['kode_tangki'] == $nozzle['kode_tangki'] ? 'selected' : '' ?>>
                    <?= $t['kode_tangki'] ?> (<?= $t['kode_produk'] ?>)
                </option>
            <?php endforeach ?>
        </select>
    </div>

    <div class="form-group">
        <label>Status</label>
        <select name="status" class="form-control">
            <option value="Aktif" <?= $nozzle['status'] === 'Aktif' ? 'selected' : '' ?>>Aktif</option>
            <option value="Nonaktif" <?= $nozzle['status'] === 'Nonaktif' ? 'selected' : '' ?>>Nonaktif</option>
        </select>
    </div>

    

    <button type="submit" class="btn btn-success">Perbarui</button>
</form>

<?= $this->endSection() ?>
