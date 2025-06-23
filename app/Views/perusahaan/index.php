<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="card">
    <div class="card-header bg-primary text-white">
        <h3 class="card-title">Daftar Perusahaan</h3>
        <div class="card-tools">
            <a href="<?= base_url('perusahaan/create')?>" class="btn btn-sm btn-light">
            
                <i class="fas fa-plus"></i> Tambah Data
            </a>
        </div>
    </div>
    <div class="card-body">
        <?php if (session()->getFlashdata('success')) : ?>
            <div class="alert alert-success">
                <?= session()->getFlashdata('success') ?>
            </div>
        <?php endif; ?>

        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Perusahaan</th>
                    <th>Nama Pengusaha</th>
                    <th>Jabatan</th>
                    <th>No Handphone</th>
                    <th>Kota/Kab</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php $no = 1; ?>
                <?php foreach ($perusahaan as $p) : ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td><?= $p['nama_perusahaan'] ?></td>
                        <td><?= $p['nama_pengusaha'] ?></td>
                        <td><?= $p['jabatan'] ?></td>
                        <td><?= $p['no_handphone'] ?></td>
                        <td><?= $p['kabupaten_kota'] ?></td>
                        <td>
                            <a href="/perusahaan/edit/<?= $p['id'] ?>" class="btn btn-sm btn-warning">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="/perusahaan/delete/<?= $p['id'] ?>" method="post" class="d-inline">
                                <?= csrf_field() ?>
                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Apakah Anda yakin?')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?= $this->endSection() ?>