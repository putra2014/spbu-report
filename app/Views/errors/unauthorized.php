<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="container mt-4 text-center">
    <h3 class="text-danger">Akses Ditolak</h3>
    <p>Anda tidak memiliki izin untuk mengakses halaman ini.</p>
    <a href="<?= base_url('dashboard') ?>" class="btn btn-primary">Kembali ke Dashboard</a>

</div>

<?= $this->endSection() ?>
