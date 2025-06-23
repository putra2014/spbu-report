<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="container-fluid">
  <div class="card shadow mb-4">
    <div class="card-header bg-dark text-white py-3">
      <div class="d-flex justify-content-between align-items-center">
        <h4 class="m-0"><i class="fas fa-gas-pump mr-2"></i>PROFIL SPBU</h4>
        <?php if (in_array(session()->get('role'), ['admin_region'])): ?>
          <a href="<?= base_url('spbu/create') ?>" class="btn btn-danger"><i class="fas fa-plus mr-2"></i>Tambah SPBU</a>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>
<div class="container-fluid">
  <div class="card shadow mb-4">
    <div class="card-header bg-dark text-white py-2">
      <div class="d-flex justify-content-between align-items-center">
        <h6 class="m-0"><i class="fas fa-gas-pump mr-2"></i>DAFTAR SPBU</h6>
              </div>
    </div>
    <div class="card-body">
      <div class="row mb-4">
        <div class="col-md-4">
          <div class="input-group">
            <input type="text" id="searchInput" class="form-control" placeholder="Cari berdasarkan No SPBU..." value="<?= $search ?? '' ?>">
            <div class="input-group-append">
              <button class="btn btn-primary" id="searchButton"><i class="fas fa-search"></i></button>
            </div>
          </div>
        </div>
        <div class="col-md-3">
          <select class="form-control" id="filterWilayah">
            <option value="">Semua Wilayah</option>
            <?php if (isset($wilayahList) && is_array($wilayahList)): ?>
              <?php foreach ($wilayahList as $wilayah): ?>
                <option value="<?= $wilayah['id'] ?>" <?= ($filterWilayah ?? '') == $wilayah['id'] ? 'selected' : '' ?>>
                  <?= $wilayah['nama_wilayah'] ?>
                </option>
              <?php endforeach; ?>
            <?php endif; ?>
          </select>
        </div>
        <div class="col-md-3">
          <select class="form-control" id="filterArea">
            <option value="">Semua Area</option>
            <?php if (isset($areaList) && is_array($areaList)): ?>
              <?php foreach ($areaList as $area): ?>
                <option value="<?= $area['id'] ?>" <?= ($filterArea ?? '') == $area['id'] ? 'selected' : '' ?>>
                  <?= $area['nama_area'] ?>
                </option>
              <?php endforeach; ?>
            <?php endif; ?>
          </select>
        </div>
        <div class="col-md-2">
          <button id="resetFilter" class="btn btn-secondary btn-block"><i class="fas fa-sync-alt mr-2"></i>Reset</button>
        </div>
      </div>

      <div class="table-responsive">
        <table class="table table-bordered table-hover">
          <thead class="thead-light">
            <tr>
              <th>No</th>
              <th>No SPBU</th>
              <th>Nama SPBU</th>
              <th>Wilayah</th>
              <th>Area</th>
              <th>Alamat</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody>
            <?php if (isset($spbu) && is_array($spbu)): ?>
              <?php foreach ($spbu as $index => $item): ?>
                <tr>
                  <td><?= $index + 1 + (($currentPage - 1) * 10) ?></td>
                  <td><?= $item['kode_spbu'] ?></td>
                  <td><?= $item['nama_spbu'] ?></td>
                  <td><?= $item['nama_wilayah'] ?? '-' ?></td>
                  <td><?= $item['nama_area'] ?? '-' ?></td>
                  <td><?= $item['alamat_spbu'] ?></td>
                  <td class="text-center">
                    <div class="btn-group" role="group">
                      <a href="<?= base_url('spbu/detail/' . $item['kode_spbu']) ?>" class="btn btn-sm btn-info" title="Detail">
                        <i class="fas fa-eye"></i>
                      </a>
                      <a href="<?= base_url('spbu/edit/' . $item['kode_spbu']) ?>" class="btn btn-sm btn-warning" title="Edit">
                        <i class="fas fa-edit"></i>
                      </a>
                    </div>
                  </td>
                </tr>
              <?php endforeach ?>
            <?php else: ?>
              <tr>
                <td colspan="7" class="text-center">Tidak ada data SPBU</td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
<!-- In your table footer section, replace the pager line with: -->
<div class="row mt-3">
    <div class="col-md-6">
        <div class="dataTables_info">
            Showing <?= (($currentPage - 1) * 10) + 1 ?> to <?= min($currentPage * 10, $pager->getTotal()) ?> of <?= $pager->getTotal() ?> entries
        </div>
    </div>
    <div class="col-md-6">
        <?= $pager->links('default', 'bootstrap_pager') ?>
    </div>
</div>
    </div>
  </div>
</div>

<script>
  $(document).ready(function() {
    // Search functionality
    $('#searchButton').click(function() {
      applyFilters();
    });

    $('#searchInput').keypress(function(e) {
      if (e.which == 13) {
        applyFilters();
      }
    });

    // Filter functionality
    $('#filterWilayah, #filterArea').change(function() {
      applyFilters();
    });

    // Reset filters
    $('#resetFilter').click(function() {
      $('#searchInput').val('');
      $('#filterWilayah, #filterArea').val('');
      applyFilters();
    });

    function applyFilters() {
      const search = $('#searchInput').val();
      const wilayah = $('#filterWilayah').val();
      const area = $('#filterArea').val();
      
      let url = '<?= base_url('spbu') ?>?';
      if (search) url += `search=${search}&`;
      if (wilayah) url += `wilayah=${wilayah}&`;
      if (area) url += `area=${area}&`;
      
      window.location.href = url.slice(0, -1); // Remove last & or ?
    }
  });
</script>

<?= $this->endSection() ?>