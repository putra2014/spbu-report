<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="container-fluid">
    <h4>Tambah User</h4>
    <form action="<?= site_url('user/store') ?>" method="post" class="form-global">
        <div class="form-group">
            <label>Username</label>
            <input type="text" name="username" class="form-control" required>
        </div>
        <div class="form-group">
            <label>Password</label>
            <input type="password" name="password" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="role">Role</label>
                <select name="role" class="form-control" required>
                 <option value="admin_spbu">Admin SPBU</option>
                 <option value="admin_area">Admin Area</option>
                 <option value="admin_region">Admin Region</option>
        </select>

<label for="kode_spbu">Kode SPBU</label>
<input type="number" name="kode_spbu" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-success">Simpan</button>
        <a href="<?= site_url('user') ?>" class="btn btn-secondary">Kembali</a>
    </form>
</div>

<?= $this->endSection() ?>
