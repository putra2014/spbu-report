<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="card">
    <div class="card-header bg-danger">
        <h4 class="m-0 font-weight-bold text-white"><?= $title ?></h4>
        <div class="card-tools">
            <a href="<?= base_url('spbu') ?>" class="btn btn-light btn-sm">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>
    </div>
    <div class="card-body">
        <?php if (session()->has('errors')) : ?>
            <div class="alert alert-danger">
                <ul>
                    <?php foreach (session('errors') as $error) : ?>
                        <li><?= $error ?></li>
                    <?php endforeach ?>
                </ul>
            </div>
        <?php endif ?>

        <form action="<?= base_url('spbu/store') ?>" method="post">
            <?= csrf_field() ?>
            
            <!-- Informasi Dasar SPBU -->
            <div class="row mb-3">
                <div class="col-md-6">
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
                </div>
                
                <div class="col-md-6">
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
                </div>
            </div>
            
            <!-- Lokasi SPBU -->
            <div class="card mb-3">
                <div class="card-header bg-info">
                    <h3 class="card-title">Lokasi SPBU</h3>
                </div>
                <div class="card-body">
                    <div class="row">
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
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group">
                              <label for="provinsi_id">Provinsi</label>
                                <select name="provinsi_id" id="provinsi_id" class="form-control" required >
                                    <option value="">Pilih Area</option>
                                    <?php foreach ($provinsiList as $provinsi): ?>
                                    <option value="<?= $provinsi['id'] ?>"><?= $provinsi['nama'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <div class="col-md-6">
                              <div class="form-group">
                                  <label class="font-weight-bold">15. Kabupaten/Kota</label>
                                  <select name="kabupaten_id" class="form-control" required >
                                    <option value="">Pilih Kabupaten</option>
                                      <?php foreach ($kabupatenList as $kab): ?>
                                          <option value="<?= $kab['id'] ?>" <?= $kab['nama_kabupaten']?></option>
                                      <?php endforeach ?>
                                  </select>
                              </div>
                        </div>
                    </div>
                    
                    <div class="row mt-3">
                        <div class="col-md-6">
                            <div class="form-group">
                            <label for="address">Alamat Lengkap</label>
                            <textarea name="address" id="address" class="form-control" required></textarea>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="latitude">Latitude</label>
                                        
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="longitude">Longitude</label>
                                        
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Informasi Tambahan -->
            <div class="card mb-3">
                <div class="card-header bg-info">
                    <h3 class="card-title">Informasi Tambahan</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="tank_count">Jumlah Tangki</label>
                                
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="dispenser_count">Jumlah Dispenser</label>
                                
                            </div>
                        </div>
                        <div class="col-md-4">
                            
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="text-center">
                <button type="submit" class="btn btn-warning btn-lg">
                    <i class="fas fa-save"></i> Update SPBU
                </button>
                <a href="<?= base_url('spbu') ?>" class="btn btn-secondary btn-lg">
                    <i class="fas fa-times"></i> Batal
                </a>
            </div>
        </form>
    </div>
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