<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="container-fluid">
  <div class="card shadow">
    <div class="card-header bg-green text-white">
      <h4 class="mb-0"><i class="fas fa-gas-pump mr-2"></i>Detail Profil SPBU</h4>
    </div>
    <div class="card-body">
      <div class="row">
        <div class="col-md-6">
          <dl class="row border-bottom border-light mb-2 pb-2">
            <dt class="col-sm-5 font-weight-bold">1. No SPBU</dt>
            <dd class="col-sm-7"><?= $spbu['kode_spbu'] ?></dd>
          </dl>

          <dl class="row border-bottom border-light mb-2 pb-2">
            <dt class="col-sm-5 font-weight-bold">2. Nama SPBU</dt>
            <dd class="col-sm-7"><?= $spbu['nama_spbu'] ?></dd>
          </dl>

          <dl class="row border-bottom border-light mb-2 pb-2">
            <dt class="col-sm-5 font-weight-bold">3. Nama Perusahaan</dt>
            <dd class="col-sm-7"><?= $spbu['nama_perusahaan'] ?></dd>
          </dl>

          <dl class="row border-bottom border-light mb-2 pb-2">
            <dt class="col-sm-5 font-weight-bold">4. Alamat</dt>
            <dd class="col-sm-7"><?= $spbu['alamat_spbu'] ?></dd>
          </dl>

          <dl class="row border-bottom border-light mb-2 pb-2">
            <dt class="col-sm-5 font-weight-bold">5. No Telp SPBU</dt>
            <dd class="col-sm-7"><?= $spbu['telp_spbu'] ?></dd>
          </dl>
          <dl class="row border-bottom border-light mb-2 pb-2">
            <dt class="col-sm-5 font-weight-bold">6. Jenis SPBU</dt>
            <dd class="col-sm-7"><?= $spbu['nama_jenis'] ?? '-' ?></dd>
          </dl>

          <dl class="row border-bottom border-light mb-2 pb-2">
            <dt class="col-sm-5 font-weight-bold">7. Type SPBU</dt>
            <dd class="col-sm-7"><?= $spbu['nama_type'] ?? '-' ?></dd>
          </dl>

          <dl class="row border-bottom border-light mb-2 pb-2">
            <dt class="col-sm-5 font-weight-bold">8. Kelas SPBU</dt>
            <dd class="col-sm-7"><?= $spbu['nama_kelas'] ?? '-' ?></dd>
          </dl>

          <dl class="row border-bottom border-light mb-2 pb-2">
            <dt class="col-sm-5 font-weight-bold">9. Jumlah Tangki</dt>
            <dd class="col-sm-7"><?= $spbu['jumlah_tangki'] ?></dd>
          </dl>

          <dl class="row border-bottom border-light mb-2 pb-2">
            <dt class="col-sm-5 font-weight-bold">1o. Jumlah Dispenser</dt>
            <dd class="col-sm-7"><?= $spbu['jumlah_dispenser'] ?></dd>
          </dl>
        </div>

        <div class="col-md-6">
          <dl class="row border-bottom border-light mb-2 pb-2">
            <dt class="col-sm-5 font-weight-bold">11. Nama Direktur</dt>
            <dd class="col-sm-7"><?= $spbu['nama_direktur'] ?></dd>
          </dl>

          <dl class="row border-bottom border-light mb-2 pb-2">
            <dt class="col-sm-5 font-weight-bold">12. Telp Direktur</dt>
            <dd class="col-sm-7"><?= $spbu['telp_direktur'] ?></dd>
          </dl>

          <dl class="row border-bottom border-light mb-2 pb-2">
            <dt class="col-sm-5 font-weight-bold">13. Nama Manager</dt>
            <dd class="col-sm-7"><?= $spbu['nama_manager'] ?></dd>
          </dl>

          <dl class="row border-bottom border-light mb-2 pb-2">
            <dt class="col-sm-5 font-weight-bold">14. Telp Manager</dt>
            <dd class="col-sm-7"><?= $spbu['telp_manager'] ?></dd>
          </dl>

          <dl class="row border-bottom border-light mb-2 pb-2">
            <dt class="col-sm-5 font-weight-bold">15. Sales Area (Wilayah)</dt>
            <dd class="col-sm-7"><?= $spbu['nama_wilayah'] ?? '-' ?></dd>
          </dl>

          <dl class="row border-bottom border-light mb-2 pb-2">
            <dt class="col-sm-5 font-weight-bold">16. SBM (Area)</dt>
            <dd class="col-sm-7"><?= $spbu['nama_area'] ?? '-' ?></dd>
          </dl>

          <dl class="row border-bottom border-light mb-2 pb-2">
            <dt class="col-sm-5 font-weight-bold">17. Provinsi</dt>
            <dd class="col-sm-7"><?= $spbu['nama'] ?? '-' ?></dd>
          </dl>

          <dl class="row border-bottom border-light mb-2 pb-2">
            <dt class="col-sm-5 font-weight-bold">18. Kabupaten</dt>
            <dd class="col-sm-7"><?= $spbu['nama_kabupaten'] ?? '-' ?></dd>
          </dl>
        </div>
      </div>

      <div class="bg-light p-3 rounded mt-4">
        <dl class="row border-bottom border-light mb-2 pb-2">
          <dt class="col-sm-2 font-weight-bold">19. Latitude</dt>
          <dd class="col-sm-10"><?= $spbu['latitude'] ?></dd>
        </dl>

        <dl class="row border-bottom border-light mb-2 pb-2">
          <dt class="col-sm-2 font-weight-bold">20. Longitude</dt>
          <dd class="col-sm-10"><?= $spbu['longitude'] ?></dd>
        </dl>

        <dl class="row">
          <dt class="col-sm-2 font-weight-bold">21. Keterangan</dt>
          <dd class="col-sm-10"><?= $spbu['keterangan'] ?: '-' ?></dd>
        </dl>
      </div>

    </div>
    <div class="card-footer text-right">
      <a href="<?= base_url('spbu') ?>" class="btn btn-secondary"><i class="fas fa-arrow-left mr-2"></i> Kembali</a>
      <a href="<?= base_url('spbu/edit/' . $spbu['kode_spbu']) ?>" class="btn btn-primary ml-2"><i class="fas fa-edit mr-2"></i> Edit</a>
    </div>
  </div>
</div>

<?= $this->endSection() ?>