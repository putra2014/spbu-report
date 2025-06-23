<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<h3>Edit Area</h3>
<form action="<?= base_url('area/update/'.$area['id']) ?>" method="post">
    <div class="form-group">
        <label>Wilayah</label>
        <select name="wilayah_id" class="form-control" required>
            <?php foreach ($wilayah as $w): ?>
                <option value="<?= $w['id'] ?>" <?= $w['id'] == $area['wilayah_id'] ? 'selected' : '' ?>>
                    <?= esc($w['nama_wilayah']) ?>
                </option>
            <?php endforeach ?>
        </select>
    </div>
    <div class="form-group">
        <label>Nama Area</label>
        <input type="text" name="nama_area" class="form-control" value="<?= esc($area['nama_area']) ?>" required>
    </div>
    <button class="btn btn-success">Update</button>
</form>
<?= $this->endSection() ?>
