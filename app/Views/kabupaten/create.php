<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<h4>Tambah Kabupaten</h4>
<form action="<?= base_url('kabupaten/store') ?>" method="post">
    <div class="form-group">
        <label>Provinsi</label>
        <select name="provinsi_id" class="form-control" required>
            <option value="">Pilih Provinsi</option>
            <?php foreach ($provinsiList as $p): ?>
                <option value="<?= $p['id'] ?>"><?= $p['nama'] ?></option>
            <?php endforeach ?>
        </select>
    </div>
    <div class="form-group">
        <label>Nama Kabupaten</label>
        <input type="text" name="nama_kabupaten" class="form-control" required>
    </div>
    <button type="submit" class="btn btn-success mt-2">Simpan</button>
</form>

<?= $this->endSection() ?>
