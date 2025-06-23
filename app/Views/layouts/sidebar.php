<?php
$role = session()->get('role');
$spbu_incomplete = session()->get('spbu_incomplete');
$kode_spbu = session()->get('kode_spbu');
?>

<!-- Sidebar -->
<style>
  /* Style untuk brand link (SPBU Report) */
  .brand-link {
    background-color: #dc3545 !important; /* Warna merah Bootstrap */
  }
  
  /* Style untuk teks */
  .brand-link .brand-text {
    color: white !important;
    font-weight: bold !important;
  }
  
  /* Style untuk hover */
  .brand-link:hover {
    background-color: #c82333 !important; /* Warna merah lebih gelap saat hover */
  }
</style>

<aside class="main-sidebar sidebar-white-primary elevation-4">
  <!-- Bagian yang diubah -->
  <a href="<?= base_url('dashboard') ?>" class="brand-link" style="background-color: #dc3545;">
    <span class="brand-text font-weight-light ">SPBU Report</span>
  </a>

  <div class="sidebar">
    <nav class="mt-2">
      <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview">
        <?php if ($role === 'admin_spbu' && $spbu_incomplete): ?>
          <li class="nav-item">
            <a href="<?= base_url('spbu/edit/' . session()->get('kode_spbu')) ?>" class="nav-link text-warning">
              Lengkapi Data SPBU
            </a>
          </li>
        <?php endif ?>

        <?php if (!$spbu_incomplete): ?>

          <?php if ($role === 'admin_region'): ?>
            <li class="nav-item"><a href="<?= base_url('dashboard') ?>" class="nav-link"><i class="nav-icon fas fa-tachometer-alt"></i>Dashboard</a></li>
            <li class="nav-item"><a href="<?= base_url('spbu') ?>" class="nav-link"><i class="nav-icon fas fa-id-card"></i>Profil SPBU</a></li>
            <li class="nav-item"><a href="<?= base_url('penerimaan') ?>" class="nav-link">Penerimaan BBM</a></li>
            <li class="nav-item"><a href="<?= base_url('penjualan') ?>" class="nav-link">Penjualan Totalisator</a></li>
            <li class="nav-item"><a href="<?= base_url('user') ?>" class="nav-link">User Manage</a></li>
            <li class="nav-item highlight-red">
              <a href="#" class="nav-link">
                <i class="nav-icon fas fa-map"></i>
                <p>
                  Setting Area
                  <i class="fas fa-angle-left right"></i>
                </p>
              </a>
              <ul class="nav nav-treeview">
                <li class="nav-item"><a href="<?= base_url('wilayah') ?>" class="nav-link">Sales Area</a></li>
                <li class="nav-item"><a href="<?= base_url('area') ?>" class="nav-link">SBM</a></li>
                <li class="nav-item"><a href="<?= base_url('provinsi') ?>" class="nav-link">Provinsi</a></li>
                <li class="nav-item"><a href="<?= base_url('kabupaten') ?>" class="nav-link">Kabupaten</a></li>
              </ul>
            </li>
            <li class="nav-item highlight-red">
              <a href="#" class="nav-link">
                <i class="nav-icon fas fa-map"></i>
                <p>
                  Setting Sarfas
                  <i class="fas fa-angle-left right"></i>
                </p>
              </a>
              <ul class="nav nav-treeview">
                <li class="nav-item"><a href="<?= base_url('tangki') ?>" class="nav-link">Data Tangki</a></li>
                <li class="nav-item"><a href="<?= base_url('dispenser') ?>" class="nav-link">Data Dispenser</a></li>
                <li class="nav-item"><a href="<?= base_url('nozzle') ?>" class="nav-link">Cek Nozzle</a></li>
              </ul>
            </li>
            <li class="nav-item highlight-red">
              <a href="#" class="nav-link">
                <i class="nav-icon fas fa-map"></i>
                <p>
                  Setting Code
                  <i class="fas fa-angle-left right"></i>
                </p>
              </a>
              <ul class="nav nav-treeview">
                <li class="nav-item"><a href="<?= base_url('kode-dispenser') ?>" class="nav-link">Dispenser</a></li>
                <li class="nav-item"><a href="<?= base_url('kode-nozzle') ?>" class="nav-link">Nozzle</a></li>
              </ul>
            <li class="nav-item">
                <a class="nav-link" href="<?= base_url('admin/approvals') ?>">
                    <i class="fas fa-sync-alt"></i> Reset Meter
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="<?= base_url('penjualan/reset-requests') ?>">
                    <i class="fas fa-sync-alt"></i> Daftar Permintaan Reset
                </a>
            </li>

            <li class="nav-item">
              <a href="#" class="nav-link">
                <i class="nav-icon fas fa-book"></i>
                <p>
                  Report
                  <i class="fas fa-angle-left right"></i>
                </p>
              </a>
              <ul class="nav nav-treeview">
                <li class="nav-item"><a href="<?= base_url('dashboard') ?>" class="nav-link">Stock</a></li>
                <li class="nav-item"><a href="<?= base_url('dashboard') ?>" class="nav-link">Received</a></li>
                <li class="nav-item"><a href="<?= base_url('dashboard') ?>" class="nav-link">Sales</a></li>
                <li class="nav-item"><a href="<?= base_url('dashboard') ?>" class="nav-link">Nozzle Cek</a></li>
                <li class="nav-item"><a href="<?= base_url('dashboard') ?>" class="nav-link">Form BPK RI</a></li>
              </ul>
            </li>
          <?php elseif ($role === 'admin_area'): ?>
            <li class="nav-item"><a href="<?= base_url('dashboard') ?>" class="nav-link">Dashboard</a></li>
            <li class="nav-item"><a href="<?= base_url('spbu') ?>" class="nav-link">SPBU Area</a></li>
            <li class="nav-item"><a href="<?= base_url('dashboard') ?>" class="nav-link">Form BPK RI</a></li>
            <li class="nav-item">
              <a href="#" class="nav-link">
                <i class="nav-icon fas fa-book"></i>
                <p>
                  Report
                  <i class="fas fa-angle-left right"></i>
                </p>
              </a>
              <ul class="nav nav-treeview">
                <li class="nav-item"><a href="<?= base_url('dashboard') ?>" class="nav-link">Stock</a></li>
                <li class="nav-item"><a href="<?= base_url('dashboard') ?>" class="nav-link">Received</a></li>
                <li class="nav-item"><a href="<?= base_url('dashboard') ?>" class="nav-link">Sales</a></li>
                <li class="nav-item"><a href="<?= base_url('dashboard') ?>" class="nav-link">Nozzle Cek</a></li>
                <li class="nav-item"><a href="<?= base_url('dashboard') ?>" class="nav-link">Reserve</a></li>
              </ul>
            </li>
          
            <?php elseif ($role === 'admin_spbu'): ?>
            <li class="nav-item"><a href="<?= base_url('dashboard') ?>" class="nav-link">Dashboard</a></li>
            <li class="nav-item">
              <a href="#" class="nav-link">
                <i class="nav-icon fas fa-book"></i>
                <p>
                  Informasi SPBU
                  <i class="fas fa-angle-left right"></i>
                </p>
              </a>
              <ul class="nav nav-treeview">
                <li class="nav-item"><a href="<?= base_url('perusahaan') ?>" class="nav-link">Informasi Perusahaan</a></li>
                <li class="nav-item"><a href="<?= base_url('spbu') ?>" class="nav-link">Informasi SPBU</a></li>
              
              </ul>
            </li>
            <li class="nav-item">
              <a href="<?= base_url('spbu/detail/' . session()->get('kode_spbu')) ?>" class="nav-link">Profil SPBU</a>
            </li>
            <li class="nav-item"><a href="<?= base_url('tangki') ?>" class="nav-link">Data Tangki</a></li>
            <li class="nav-item"><a href="<?= base_url('dispenser') ?>" class="nav-link">Data Dispenser</a></li>
            <li class="nav-item"><a href="<?= base_url('penerimaan') ?>" class="nav-link">Penerimaan BBM</a></li>
            <li class="nav-item"><a href="<?= base_url('nozzle') ?>" class="nav-link">Cek Nozzle</a></li>
            <li class="nav-item"><a href="<?= base_url('penjualan') ?>" class="nav-link">Penjualan</a></li>
            <li class="nav-item">
                <a class="nav-link" href="<?= base_url('penjualan/setup-initial-meters') ?>">
                    <i class="fas fa-tachometer-alt"></i> Initial Meter
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="<?= base_url('penjualan/reset-form') ?>">
                    <i class="fas fa-sync-alt"></i> Reset Meter
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="<?= base_url('penjualan/reset-requests') ?>">
                    <i class="fas fa-sync-alt"></i> Daftar Permintaan Reset
                </a>
            </li>
            <li class="nav-item"><a href="<?= base_url('operator') ?>" class="nav-link">Operator</a></li>
          <?php endif ?>
          
          

        <?php endif ?>

        <li class="nav-item mt-2">
          <a href="<?= base_url('logout') ?>" class="nav-link text-danger">
            <i class="fas fa-sign-out-alt mr-2"></i> Logout
          </a>
        </li>

      </ul>
    </nav>
  </div>
</aside>