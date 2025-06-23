<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="container">
    <h2>Edit Profil SPBU</h2>
    <form action="<?= base_url('spbu/update/' . $spbu['kode_spbu']) ?>" method="post">
        <div class="row">
            <div class="col-md-6">
                <label>No SPBU</label>
                <input type="text" name="kode_spbu" class="form-control" value="<?= $spbu['kode_spbu'] ?>" readonly>

                <label>Nama SPBU</label>
                <input type="text" name="nama_spbu" class="form-control" value="<?= $spbu['nama_spbu'] ?>">

                <label>Nama Perusahaan</label>
                <input type="text" name="nama_perusahaan" class="form-control" value="<?= $spbu['nama_perusahaan'] ?>">

                <label>Alamat SPBU</label>
                <input type="text" name="alamat_spbu" class="form-control" value="<?= $spbu['alamat_spbu'] ?>">

                <label>No Telp</label>
                <input type="text" name="telp_spbu" class="form-control" value="<?= $spbu['telp_spbu'] ?>">

                <label>Nama Pemilik</label>
                <input type="text" name="nama_pemilik" class="form-control" value="<?= $spbu['nama_pemilik'] ?>">

                <label>No Telp Direktur</label>
                <input type="text" name="telp_pemilik" class="form-control" value="<?= $spbu['telp_pemilik'] ?>">
            </div>

            <div class="col-md-6">
                <label>Nama Manager SPBU</label>
                <input type="text" name="nama_manager" class="form-control" value="<?= $spbu['nama_manager'] ?>">

                <label>No Telp Manager</label>
                <input type="text" name="telp_manager" class="form-control" value="<?= $spbu['telp_manager'] ?>">

                <label>Sold to Party</label>
                <input type="text" name="sold_to_party" class="form-control" value="<?= $spbu['sold_to_party'] ?>">

                <label>Ship to Party</label>
                <input type="text" name="ship_to_party" class="form-control" value="<?= $spbu['ship_to_party'] ?>">

                <label>Wilayah (Sales Area)</label>
                <select name="wilayah_id" class="form-control">
                    <?php foreach ($wilayahList as $wilayah): ?>
                        <option value="<?= $wilayah['id'] ?>" <?= $wilayah['id'] == $spbu['wilayah_id'] ? 'selected' : '' ?>>
                            <?= $wilayah['nama_wilayah'] ?>
                        </option>
                    <?php endforeach ?>
                </select>

                <label>Area (SBM)</label>
                <select name="area_id" class="form-control">
                    <?php foreach ($areaList as $area): ?>
                        <option value="<?= $area['id'] ?>" <?= $area['id'] == $spbu['area_id'] ? 'selected' : '' ?>>
                            <?= $area['nama_area'] ?>
                        </option>
                    <?php endforeach ?>
                </select>

                <label>Provinsi</label>
                <select name="provinsi_id" class="form-control" id="provinsi">
                    <?php foreach ($provinsiList as $prov): ?>
                        <option value="<?= $prov['id'] ?>" <?= $prov['id'] == $spbu['provinsi_id'] ? 'selected' : '' ?>>
                            <?= $prov['nama'] ?>
                        </option>
                    <?php endforeach ?>
                </select>

                <label>Kabupaten/Kota</label>
                <select name="kabupaten_id" class="form-control" id="kabupaten">
                    <?php foreach ($kabupatenList as $kab): ?>
                        <option value="<?= $kab['id'] ?>" <?= $kab['id'] == $spbu['kabupaten_id'] ? 'selected' : '' ?>>
                            <?= $kab['nama'] ?>
                        </option>
                    <?php endforeach ?>
                </select>

                <label>Jumlah Tangki</label>
                <input type="number" name="jumlah_tangki" class="form-control" value="<?= $spbu['jumlah_tangki'] ?>">

                <label>Jumlah Dispenser</label>
                <input type="number" name="jumlah_dispenser" class="form-control" value="<?= $spbu['jumlah_dispenser'] ?>">

                <label>Latitude</label>
                <input type="text" name="latitude" class="form-control" value="<?= $spbu['latitude'] ?>">

                <label>Longitude</label>
                <input type="text" name="longitude" class="form-control" value="<?= $spbu['longitude'] ?>">

                <label>Keterangan</label>
                <textarea name="keterangan" class="form-control"><?= $spbu['keterangan'] ?></textarea>
            </div>
        </div>

        <div class="mt-4">
            <button type="submit" class="btn btn-primary">Simpan</button>
        </div>
    </form>
</div>

<script>
    // AJAX dependent dropdown kabupaten
    $('#provinsi').on('change', function() {
        var provinsiID = $(this).val();
        $.get("<?= base_url('/ajax/kabupaten/') ?>" + provinsiID, function(data) {
            $('#kabupaten').html(data);
        });
    });
</script>

<?= $this->endSection() ?>