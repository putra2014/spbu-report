<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<h3>Tambah Area</h3>
<form action="<?= base_url('area/store') ?>" method="post">
    <div class="form-group">
        <label>Wilayah</label>
        <select name="wilayah_id" class="form-control" required>
            <option value="">-- Pilih Wilayah --</option>
            <?php foreach ($wilayah as $w): ?>
                <option value="<?= $w['id'] ?>"><?= esc($w['nama_wilayah']) ?></option>
            <?php endforeach ?>
        </select>
    </div>
    <div class="form-group">
        <label>Nama Area</label>
        <input type="text" name="nama_area" class="form-control" required>
    </div>
    <button class="btn btn-success">Simpan</button>
</form>
<?= $this->endSection() ?>
