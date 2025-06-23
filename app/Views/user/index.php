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
                    <li class="breadcrumb-item"><a href="<?= base_url('dashboard') ?>">Home</a></li>
                    <li class="breadcrumb-item active">User Management</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <?php if(session()->getFlashdata('message')): ?>
            <div class="alert alert-success alert-dismissible fade show">
                <?= session()->getFlashdata('message') ?>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        <?php endif; ?>

        <div class="card">
            <div class="card-header">
                <div class="row">
                    <div class="col-md-6">
                        <div class="search-box">
                            <i class="fas fa-search"></i>
                            <input type="text" class="form-control" placeholder="Search users..." id="searchInput">
                        </div>
                    </div>
                    <div class="col-md-6 text-right">
                        <a href="<?= base_url('user/create') ?>" class="btn btn-primary">
                            <i class="fas fa-plus mr-2"></i>Tambah User
                        </a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="table-container">
                    <table class="table" id="usersTable">
                        <thead>
                            <tr><th>ID</th>
                                <th>Username</th>
                                <th>Role</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($users as $user): ?>
                            <tr>
                                <td><?= $user['id'] ?></td>
                                <td><?= $user['username'] ?></td>
                                <td><?= $user['role'] ?></td>
                                
                                
                                <td>
                                    <div class="btn-group">
                                        <a href="<?= base_url('user/edit/'.$user['id']) ?>" class="btn btn-sm btn-warning" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="<?= base_url('user/delete/'.$user['id']) ?>" method="post" class="d-inline">
                                            <?= csrf_field() ?>
                                            
                                            <button type="submit" class="btn btn-sm btn-danger" title="Hapus" onclick="return confirm('Apakah Anda yakin?')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                
            </div>
        </div>
    </div>
</section>


<?= $this->endSection() ?>