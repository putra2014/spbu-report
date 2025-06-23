<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<h1>Selamat datang</h1>
<p>Anda login sebagai <strong><?= session()->get('role') ?></strong></p>



<?= $this->endSection() ?>
