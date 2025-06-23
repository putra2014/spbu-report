<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<h4>Edit Kabupaten</h4>
<form action="<?= base_url('kabupaten/update/'.$kabupaten['id']) ?>" method="post">
    <div class="form-group">
        <label>Provinsi</label>
        <select name="provinsi_id" class="form-control" required>
            <?php foreach ($provinsiList as $p): ?>
                <option value="<?= $p['id'] ?>" <?= $kabupaten['provinsi_id'] == $p['id'] ? 'selected' : '' ?>>
                    <?= $p['nama'] ?>
                </option>
            <?php endforeach ?>
        </select>
    </div>
    <div class="form-group">
        <label>Nama Kabupaten</label>
        <input type="text" name="nama" value="<?= $kabupaten['nama_kabupaten'] ?>" class="form-control" required>
    </div>
    <button type="submit" class="btn btn-primary mt-2">Update</button>
</form>

<?= $this->endSection() ?>
