<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title><?= $title ?? 'SPBU Report System' ?></title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css">
  <link rel="stylesheet" href="<?= base_url('adminlte/plugins/fontawesome-free/css/all.min.css') ?>">
  <link rel="stylesheet" href="<?= base_url('adminlte/dist/css/adminlte.min.css') ?>">
  
  <!-- Load jQuery di HEAD -->
  <script src="<?= base_url('adminlte/plugins/jquery/jquery.min.js') ?>"></script>
</head>
<body class="hold-transition sidebar-mini">
<div class="wrapper">
  <!-- Navbar -->
  <?= $this->include('layouts/navbar') ?>

  <!-- Sidebar -->
  <?= $this->include('layouts/sidebar') ?>

  <!-- Content Wrapper -->
  <div class="content-wrapper p-3">

    <?php if (session()->getFlashdata('warning')): ?>
      <div class="alert alert-warning alert-dismissible fade show" role="alert">
        <?= session()->getFlashdata('warning') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
      </div>
    <?php endif ?>

    <?= $this->renderSection('content') ?>
  </div>
<?= $this->include('layouts/footer') ?>
</div>



<script src="<?= base_url('adminlte/plugins/bootstrap/js/bootstrap.bundle.min.js') ?>"></script>
<script src="<?= base_url('adminlte/dist/js/adminlte.min.js') ?>"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>

<script>
  $('#provinsi').on('change', function () {
    let id = $(this).val();
    $.get("<?= base_url('/ajax/kabupaten-by-provinsi/') ?>" + id, function (data) {
      $('#kabupaten').html(data);
    });
  });
</script>
<?= $this->renderSection('scripts') ?>
</body>
</html>
