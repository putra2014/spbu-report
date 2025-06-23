<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0"><?= $title ?></h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="<?= site_url('dashboard') ?>">Home</a></li>
                    <li class="breadcrumb-item"><a href="<?= site_url('user') ?>">User Management</a></li>
                    <li class="breadcrumb-item active">Edit User</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Form Edit User</h3>
            </div>
            <form action="<?= site_url("user/update/{$user['id']}") ?>" method="post">
                <div class="card-body">
                    <?= csrf_field() ?>
                    
                    <div class="form-group">
                        <label for="username">Username</label>
                        <input type="text" class="form-control <?= validation_show_error('username') ? 'is-invalid' : '' ?>" 
                            id="username" name="username" value="<?= old('username', $user['username']) ?>" required>
                        <div class="invalid-feedback">
                            <?= validation_show_error('username') ?>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="password">Password (Kosongkan jika tidak diubah)</label>
                        <input type="password" class="form-control <?= validation_show_error('password') ? 'is-invalid' : '' ?>" 
                            id="password" name="password">
                        <div class="invalid-feedback">
                            <?= validation_show_error('password') ?>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="role">Role</label>
                            <select name="role" class="form-control" required>
                             <option value="admin_spbu">Admin SPBU</option>
                             <option value="admin_area">Admin Area</option>
                             <option value="admin_region">Admin Region</option>
                            </select>
                    <div class="form-group">
                        <label for="description">Kode SPBU</label>
                        <input class="form-control" id="kode_spbu" name="kode_spbu"></input>
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save mr-1"></i> Update
                    </button>
                    <a href="<?= site_url('user') ?>" class="btn btn-secondary ml-1">
                        <i class="fas fa-times mr-1"></i> Batal
                    </a>
                </div>
            </form>
        </div>
    </div>
</section>
<?= $this->endSection() ?>