<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<h4><?= $title ?></h4>

<form action="<?= base_url('spbu/import') ?>" method="post" enctype="multipart/form-data">
    <div class="form-group">
        <label>File Excel (.xlsx)</label>
        <input type="file" name="file" accept=".xlsx" class="form-control" required>
    </div>
    <button type="submit" class="btn btn-success mt-2">Import</button>
</form>

<?= $this->endSection() ?>
