<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="container">
    <div class="card shadow mb-4">
        <div class="card-header py-3 bg-dark">
            <h4 class="m-0 font-weight-bold text-white">Edit Profil SPBU</h4>
        </div>
        <div class="card-body">
            <?= session()->getFlashdata('errors') ? view_cell('App\Libraries\Alert::error', ['errors' => session()->getFlashdata('errors')]) : '' ?>
            
            <form action="<?= base_url('spbu/update/' . $spbu['kode_spbu']) ?>" method="post">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="font-weight-bold">1. No SPBU</label>
                            <input type="text" name="kode_spbu" class="form-control form-control-lg" value="<?= $spbu['kode_spbu'] ?>" 
                        </div>

                        <div class="form-group">
                            <label class="font-weight-bold">2. Nama SPBU</label>
                            <input type="text" name="nama_spbu" class="form-control" placeholder="Contoh: SPBU Bontosunggu" value="<?= $spbu['nama_spbu'] ?>">
                        </div>

                        <div class="form-group">readonly>
                            <label class="font-weight-bold">3. Nama Perusahaan</label>
                            <input type="text" name="nama_perusahaan" class="form-control"placeholder="Contoh: PT. Arbars Sukses" value="<?= $spbu['nama_perusahaan'] ?>">
                        </div>

                        <div class="form-group">
                            <label class="font-weight-bold">4. Alamat SPBU</label>
                            <input type="text" name="alamat_spbu" class="form-control" value="<?= $spbu['alamat_spbu'] ?>">
                        </div>

                        <div class="form-group">
                            <label class="font-weight-bold">5. No Telp</label>
                            <input type="text" name="telp_spbu" class="form-control" value="<?= $spbu['telp_spbu'] ?>">
                        </div>

                        <div class="form-group">
                            <label class="font-weight-bold">6. Nama Direktur</label>
                            <input type="text" name="nama_direktur" class="form-control" value="<?= $spbu['nama_direktur'] ?>">
                        </div>

                        <div class="form-group">
                            <label class="font-weight-bold">7. No Telp Direktur</label>
                            <input type="text" name="telp_direktur" class="form-control" value="<?= $spbu['telp_direktur'] ?>">
                        </div>

                        <div class="form-group">
                            <label class="font-weight-bold">8. Nama Manager SPBU</label>
                            <input type="text" name="nama_manager" class="form-control" value="<?= $spbu['nama_manager'] ?>">
                        </div>

                        <div class="form-group">
                            <label class="font-weight-bold">9. No Telp Manager</label>
                            <input type="text" name="telp_manager" class="form-control" value="<?= $spbu['telp_manager'] ?>">
                        </div>

                        <div class="form-group">
                            <label class="font-weight-bold">10. Sold to Party</label>
                            <input type="text" name="sold_to_party" class="form-control" value="<?= $spbu['sold_to_party'] ?>">
                        </div>

                        <div class="form-group">
                            <label class="font-weight-bold">11. Ship to Party</label>
                            <input type="text" name="ship_to_party" class="form-control" value="<?= $spbu['ship_to_party'] ?>">
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="jenis_spbu_id">Jenis SPBU <span class="text-danger">*</span></label>
                            <select name="jenis_spbu_id" id="jenis_spbu_id" class="form-control" required>
                                <option value="">Pilih Jenis SPBU</option>
                                <?php foreach ($jenisSpbuList as $j): ?>
                                    <option value="<?= $j['id'] ?>" <?= ($j['id'] == $spbu['jenis_spbu_id'] || old('jenis_spbu_id') == $j['id']) ? 'selected' : '' ?>>
                                        <?= $j['nama_jenis'] ?>
                                    </option>
                                <?php endforeach ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="type_spbu_id">Type SPBU <span class="text-danger">*</span></label>
                            <select name="type_spbu_id" id="type_spbu_id" class="form-control" required>
                                <option value="">Pilih Type SPBU</option>
                                <?php foreach ($typeSpbuList as $t): ?>
                                    <option value="<?= $t['id'] ?>" <?= ($t['id'] == $spbu['type_spbu_id'] || old('type_spbu_id') == $t['id']) ? 'selected' : '' ?>>
                                        <?= $t['nama_type'] ?>
                                    </option>
                                <?php endforeach ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="kelas_spbu_id">Kelas SPBU <span class="text-danger">*</span></label>
                            <select name="kelas_spbu_id" id="kelas_spbu_id" class="form-control" required>
                                <option value="">Pilih Kelas SPBU</option>
                                <?php foreach ($kelasSpbuList as $k): ?>
                                    <option value="<?= $k['id'] ?>" <?= ($k['id'] == $spbu['kelas_spbu_id'] || old('kelas_spbu_id') == $k['id']) ? 'selected' : '' ?>>
                                        <?= $k['nama_kelas'] ?>
                                    </option>
                                <?php endforeach ?>
                            </select>
                        </div>
                      <div class="form-group">
                        <label class="font-weight-bold">12. Sales Area</label>
                        <select name="wilayah_id" id="wilayah" class="form-control select2">
                          <option value="">Pilih Wilayah</option>
                          <?php foreach ($wilayahList as $w): ?>
                            <option value="<?= $w['id'] ?>" <?= $w['id'] == $spbu['wilayah_id'] ? 'selected' : '' ?>>
                              <?= $w['nama_wilayah'] ?>
                            </option>
                          <?php endforeach ?>
                        </select>
                      </div>
                        
                      <div class="form-group">
                        <label class="font-weight-bold">13. SBM</label>
                        <select name="area_id" id="area" class="form-control select2">
                          <option value="">Pilih Area</option>
                          <?php foreach ($areaList as $a): ?>
                            <option value="<?= $a['id'] ?>" <?= $a['id'] == $spbu['area_id'] ? 'selected' : '' ?>>
                              <?= $a['nama_area'] ?>
                            </option>
                          <?php endforeach ?>
                        </select>
                      </div>
                    

                        <div class="form-group">
                            <label class="font-weight-bold">14. Provinsi</label>
                            <select name="provinsi_id" class="form-control select2" id="provinsi">
                                <?php foreach ($provinsiList as $prov): ?>
                                    <option value="<?= $prov['id'] ?>" <?= $prov['id'] == $spbu['provinsi_id'] ? 'selected' : '' ?>>
                                        <?= $prov['nama'] ?>
                                    </option>
                                <?php endforeach ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="font-weight-bold">15. Kabupaten/Kota</label>
                            <select name="kabupaten_id" class="form-control select2" id="kabupaten">
                                <?php foreach ($kabupatenList as $kab): ?>
                                    <option value="<?= $kab['id'] ?>" <?= $kab['id'] == $spbu['kabupaten_id'] ? 'selected' : '' ?>>
                                        <?= $kab['nama_kabupaten'] ?>
                                    </option>
                                <?php endforeach ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="font-weight-bold">16. Jumlah Tangki</label>
                            <input type="number" name="jumlah_tangki" class="form-control" value="<?= $spbu['jumlah_tangki'] ?>">
                        </div>

                        <div class="form-group">
                            <label class="font-weight-bold">17. Jumlah Dispenser</label>
                            <input type="number" name="jumlah_dispenser" class="form-control" value="<?= $spbu['jumlah_dispenser'] ?>">
                        </div>

                        <div class="form-group">
                            <label class="font-weight-bold">18. Latitude</label>
                            <input type="text" name="latitude" class="form-control" value="<?= $spbu['latitude'] ?>">
                        </div>

                        <div class="form-group">
                            <label class="font-weight-bold">19. Longitude</label>
                            <input type="text" name="longitude" class="form-control" value="<?= $spbu['longitude'] ?>">
                        </div>

                        <div class="form-group">
                            <label class="font-weight-bold">20. Keterangan</label>
                            <textarea name="keterangan" class="form-control" rows="3"><?= $spbu['keterangan'] ?></textarea>
                        </div>
                    </div>
                </div>

                <div class="text-center mt-4">
                    <button type="submit" class="btn btn-primary btn-lg px-5">
                        <i class="fas fa-save mr-2"></i> Simpan Perubahan
                    </button>
                    <a href="<?= base_url('spbu') ?>" class="btn btn-secondary btn-lg px-5 ml-2">
                        <i class="fas fa-arrow-left mr-2"></i> Kembali
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>


<script>
  $('#wilayah').on('change', function () {
    var wilayahID = $(this).val();
    $.get("<?= base_url('/ajax/provinsi-by-wilayah/') ?>" + wilayahID, function (data) {
      $('#provinsi').html(data);
    });
    $.get("<?= base_url('/ajax/area-by-wilayah/') ?>" + wilayahID, function (data) {
      $('#area').html(data);
    });
  });

  $('#provinsi').on('change', function () {
    var provinsiID = $(this).val();
    $.get("<?= base_url('/ajax/kabupaten-by-provinsi/') ?>" + provinsiID, function (data) {
      $('#kabupaten').html(data);
    });
  });

  $('#area').on('change', function () {
    var areaID = $(this).val();
    $.get("<?= base_url('/ajax/kabupaten-by-area/') ?>" + areaID, function (data) {
      $('#kabupaten').html(data);
    });
  });
</script>

<?= $this->endSection() ?>