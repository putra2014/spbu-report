<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="container">
    <div class="card shadow mb-4">
        <div class="card-header py-3 bg-dark">
            <h4 class="m-0 font-weight-bold text-white"><?= $title ?></h4>
        </div>
        <div class="card-body">
            <form action="<?= base_url('perusahaan/save') ?>" method="post">
                <?php if (session('role') != 'admin_spbu'): ?>
                    <div class="form-group">
                        <label>SPBU</label>
                        <select name="kode_spbu" class="form-control" id="spbuSelect" required>
                            <option value="">Pilih SPBU</option>
                            <?php foreach ($spbuList as $s): ?>
                                <option value="<?= $s['kode_spbu'] ?>"><?= $s['kode_spbu'] ?> - <?= $s['nama_spbu'] ?></option>
                            <?php endforeach ?>
                        </select>
                    </div>
                <?php else: ?>
                    <input type="hidden" name="kode_spbu" value="<?= session('kode_spbu') ?>">
                <?php endif ?>
                <div class="card">
                    <div class="card-header">
                        <div class="text-center mt-4">
                            <h2 class="font-weight-bold">Data Perusahaan</h2>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <h5>Data Perusahaan </h5>
                                </div>
                                <div class="pl-lg-4">
                                    <div class="form-group">
                                        <div class="row">
                                            <label for="nama_perusahaan" class="col-lg-3 form-label">Nama Perusahaan <span class="text-danger">*</span></label>
                                            <div class="col-lg-8">
                                                <input type="text" class="form-control" name="nama_perusahaan" id="nama_perusahaan">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="row">
                                            <label for="alamat" class="col-lg-3 form-label">Alamat Perusahaan <span class="text-danger">*</span></label>
                                            <div class="col-lg-8">
                                                <input type="text" class="form-control" name="alamat" id="alamat">
                                            </div>
                                        </div>
                                    </div>
                                    <!-- Bagian dropdown provinsi dan kabupaten -->
                                    <div class="form-group">
                                        <div class="row">
                                            <label for="provinsi" class="col-lg-3 form-label">Provinsi <span class="text-danger">*</span></label>
                                            <div class="col-lg-8">
                                                <select class="form-control" name="provinsi" id="provinsi" required>
                                                    <option value="">Pilih Provinsi</option>
                                                    <?php foreach ($provinsi as $p): ?>
                                                        <option value="<?= esc($p['nama']) ?>"><?= esc($p['nama']) ?></option>
                                                    <?php endforeach ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="row">
                                            <label for="kabupaten_kota" class="col-lg-3 form-label">Kota/Kabupaten<span class="text-danger">*</span></label>
                                            <div class="col-lg-8">
                                                <select class="form-control" name="kabupaten_kota" id="kabupaten_kota" required>
                                                    <option value="">Pilih Provinsi terlebih dahulu</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <div class="row">
                                            <label for="CompanyAktaNo" class="col-lg-3 form-label">No. Akta Perusahaan Terakhir <span class="text-danger">*</span></label>
                                            <div class="col-lg-8">
                                                <input type="text" class="form-control" name="CompanyAktaNo" id="CompanyAktaNo">
                                                <div id="CompanyAktaNo-help" name="CompanyAktaNo-help" class="help-block"></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="row">
                                            <label for="CompanyAktaDate" class="col-lg-3 form-label">Tanggal Akta Perusahaan Terakhir <span class="text-danger">*</span></label>
                                            <div class="col-lg-8">
                                                <input type="date" class="form-control" name="CompanyAktaDate" id="CompanyAktaDate" required>
                                                <div id="CompanyAktaDate-help" class="help-block"></div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <h5>Data Pengusaha</h5>
                                </div>
                                <div class="pl-lg-4">
                                    <div class="form-group">
                                        <div class="row">
                                            <label for="nama_pengusaha" class="col-lg-3 form-label">Nama Pengusaha <span class="text-danger">*</span></label>
                                            <div class="col-lg-8">
                                                <input type="text" class="form-control" name="nama_pengusaha" id="nama_pengusaha">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <div class="row">
                                            <label for="jabatan" class="col-lg-3 form-label">Jabatan Pengusaha <span class="text-danger">*</span></label>
                                            <div class="col-lg-8">
                                                <input type="text" class="form-control" name="jabatan" id="jabatan">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <div class="row">
                                            <label for="no_handphone" class="col-lg-3 form-label">No. Handphone <span class="text-danger">*</span></label>
                                            <div class="col-lg-8">
                                                <input type="text" class="form-control" name="no_handphone" id="no_handphone" onkeypress="return isNumberKey(event);">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary">Simpan</button>
                <a href="/perusahaan" class="btn btn-secondary">Kembali</a>
            </form>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        // Inisialisasi dropdown kabupaten
        var kabupatenDropdown = $('#kabupaten_kota');
        kabupatenDropdown.prop('disabled', true);
        
        // Ketika provinsi berubah
        $('#provinsi').change(function() {
        var selectedProvince = $(this).val();
        var kabupatenDropdown = $('#kabupaten_kota');
        
        kabupatenDropdown.empty().prop('disabled', true);
        
        if (!selectedProvince) {
            kabupatenDropdown.append('<option value="">Pilih Provinsi terlebih dahulu</option>');
            return;
        }
    
        kabupatenDropdown.append('<option value="">Memuat data kabupaten...</option>');
        
        $.ajax({
        url: '<?= base_url('perusahaan/getKabupatenByProvinsi') ?>',
        type: 'POST',
        data: { 
            provinsi: selectedProvince 
        },
        dataType: 'json',
        beforeSend: function() {
            kabupatenDropdown.empty().append('<option value="">Loading...</option>');
        },
        success: function(response) {
            kabupatenDropdown.empty();
            
            if (response && response.length > 0) {
                kabupatenDropdown.append('<option value="">Pilih Kabupaten/Kota</option>');
                $.each(response, function(index, kab) {
                    kabupatenDropdown.append(
                        $('<option></option>')
                            .val(kab.nama_kabupaten)
                            .text(kab.nama_kabupaten)
                    );
                });
            } else {
                kabupatenDropdown.append('<option value="">Tidak ada data kabupaten</option>');
            }
            kabupatenDropdown.prop('disabled', false);
        },
        error: function(xhr) {
            console.error('Error:', xhr.responseText);
            kabupatenDropdown.empty().append(
                '<option value="">Error loading data</option>'
            );
        }
    });
});


function isNumberKey(evt) {
    var charCode = (evt.which) ? evt.which : evt.keyCode;
    return !(charCode > 31 && (charCode < 48 || charCode > 57));
}
</script>

<?= $this->endSection() ?>