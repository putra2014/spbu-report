<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<section class="content-header bg-dark text-white">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>Profil SPBU</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">General Form</li>
            </ol>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>
<div class="card-body">
    
    <form action="<?= base_url('spbu/store') ?>" method="post">
        
            <div class="card card-warning">
              <div class="card-header">
                <h3 class="card-title">General Elements</h3>
              </div>
              <div class="row">
                  <div class="col-sm-6">
                    <div class="form-group">
                      <label for="spbu_code">Kode SPBU</label>
                        <input type="text" name="spbu_code" id="spbu_code" class="form-control" required>
                    </div>
                    <div class="form-group">
                      <label for="spbu_name">Nama SPBU</label>
                        <input type="text" name="spbu_name" id="spbu_name" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="nama_perusahaan">Nama Perusahaan</label>
                        <textarea name="nama_perusahaan" id="nama_perusahaan" class="form-control" required></textarea>
                    </div>
                    <div class="form-group">
                      <label for="spbu_name">Nama Direktur</label>
                        <input type="text" name="spbu_name" id="spbu_name" class="form-control" required>
                    </div>
                </div>
              </div>
            </div>
            <div class="container-fluid">
              <div class="card shadow mb-4">
                <div class="card-header bg-dark text-white py-2">
                  <div class="d-flex justify-content-between align-items-center">
                    <h6>2. Informasi Wilayah</h6>
                  </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                    <label for="region_id">Wilayah</label>
                    <select name="wilayah_id" id="wilayah_id" class="form-control" required>
                        <option value="">Pilih Wilayah</option>
                        <?php foreach ($wilayahList as $wilayah): ?>
                        <option value="<?= $wilayah['id'] ?>"><?= $wilayah['nama_wilayah'] ?></option>
                        <?php endforeach; ?>
                    </select>
                    </div>
                    <div class="form-group">
                    <label for="area_id">Area</label>
                    <select name="area_id" id="area_id" class="form-control" required>
                        <option value="">Pilih Area</option>
                        <?php foreach ($areaList as $area): ?>
                        <option value="<?= $area['id'] ?>"><?= $area['nama_area'] ?></option>
                        <?php endforeach; ?>
                    </select>
                    </div>
                    <div class="form-group">
                    <label for="provinsi_id">Provinsi</label>
                    <select name="provinsi_id" id="provinsi_id" class="form-control" required >
                        <option value="">Pilih Area</option>
                        <?php foreach ($provinsiList as $provinsi): ?>
                        <option value="<?= $provinsi['id'] ?>"><?= $provinsi['nama'] ?></option>
                        <?php endforeach; ?>
                    </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                    <label for="regency_id">Kabupaten</label>
                    <select name="regency_id" id="regency_id" class="form-control" required>
                        <option value="">Pilih Kabupaten</option>
                        <?php foreach ($kabupatenList as $kabupaten): ?>
                        <option value="<?= $kabupaten['id'] ?>"><?= $kabupaten['nama'] ?></option>
                        <?php endforeach; ?>
                    </select>
                    </div>
                    <div class="form-group">
                    <label for="address">Alamat Lengkap</label>
                    <textarea name="address" id="address" class="form-control" required></textarea>
                    </div>
                </div>
                </div>
              </div>
            </div>
                <div class="form-group">
                  <label for="type_spbu_id">Type SPBU</label>
                    <select name="type_spbu_id" id="type_spbu_id" class="form-control">
                      <option value="">Pilih Type SPBU</option>
                        <?php foreach ($typeSpbuList as $t): ?>
                            <option value="<?= $t['id'] ?>"><?= $t['nama_type'] ?></option>
                        <?php endforeach ?>
                    </select>
                </div>
                <div class="form-group">
                  <label for="kelas_spbu_id">Kelas SPBU</label>
                    <select name="kelas_spbu_id" id="kelas_spbu_id" class="form-control">
                      <option value="">Pilih Kelas SPBU</option>
                        <?php foreach ($kelasSpbuList as $k): ?>
                            <option value="<?= $k['id'] ?>" <?= isset($spbu['kelas_spbu_id']) && $spbu['kelas_spbu_id'] == $k['id'] ? 'selected' : '' ?>>
                          <?= $k['nama_kelas'] ?>
                        </option>
                      <?php endforeach ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="jenis_spbu_id">Jenis SPBU</label>
                    <select name="jenis_spbu_id" id="jenis_spbu_id" class="form-control">
                      <option value="">Pilih Jenis SPBU</option>
                      <?php foreach ($jenisSpbuList as $j): ?>
                        <option value="<?= $j['id'] ?>"><?= $j['nama_jenis'] ?></option>
                      <?php endforeach ?>
                    </select>
                </div>
                <div class="form-group text-right">
                    <button type="submit" class="btn btn-primary">Simpan</button>
                    <a href="<?= base_url('spbu') ?>" class="btn btn-secondary">Batal</a>
                </div>
            </div>
        </div>
    </form>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
$(document).ready(function() {
    // Dropdown dependen wilayah -> area
    $('#region_id').change(function() {
        var region_id = $(this).val();
        if (region_id) {
            $.ajax({
                url: '<?= base_url('spbu/getAreasByRegion') ?>/' + region_id,
                type: 'GET',
                dataType: 'json',
                success: function(data) {
                    $('#area_id').empty().append('<option value="">Pilih Area</option>');
                    $.each(data, function(key, value) {
                        $('#area_id').append('<option value="'+ value.id +'">'+ value.area_name +'</option>');
                    });
                    $('#area_id').prop('disabled', false);
                    $('#province_id, #regency_id').prop('disabled', true).empty().append('<option value="">Pilih Provinsi/Kabupaten</option>');
                }
            });
        } else {
            $('#area_id, #province_id, #regency_id').prop('disabled', true).empty().append('<option value="">Pilih Wilayah terlebih dahulu</option>');
        }
    });
    
    // Dropdown dependen area -> provinsi
    $('#area_id').change(function() {
        var area_id = $(this).val();
        if (area_id) {
            $.ajax({
                url: '<?= base_url('spbu/getProvincesByArea') ?>/' + area_id,
                type: 'GET',
                dataType: 'json',
                success: function(data) {
                    $('#province_id').empty().append('<option value="">Pilih Provinsi</option>');
                    $.each(data, function(key, value) {
                        $('#province_id').append('<option value="'+ value.id +'">'+ value.province_name +'</option>');
                    });
                    $('#province_id').prop('disabled', false);
                    $('#regency_id').prop('disabled', true).empty().append('<option value="">Pilih Kabupaten</option>');
                }
            });
        }
    });
    
    // Dropdown dependen provinsi -> kabupaten
    $('#province_id').change(function() {
        var province_id = $(this).val();
        if (province_id) {
            $.ajax({
                url: '<?= base_url('spbu/getRegenciesByProvince') ?>/' + province_id,
                type: 'GET',
                dataType: 'json',
                success: function(data) {
                    $('#regency_id').empty().append('<option value="">Pilih Kabupaten</option>');
                    $.each(data, function(key, value) {
                        $('#regency_id').append('<option value="'+ value.id +'">'+ value.regency_name +'</option>');
                    });
                    $('#regency_id').prop('disabled', false);
                }
            });
        }
    });
});
</script>
<?= $this->endSection() ?>