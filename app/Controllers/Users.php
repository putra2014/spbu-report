<?php

namespace App\Controllers;
use App\Models\UserModel;

class Users extends BaseController
{
    protected $userModel;
    
    
    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    public function index()
    {
        if (!in_array(session()->get('role'), ['admin_region', 'super_admin'])) {
            return redirect()->to('/unauthorized');
        }

        $data['title'] = 'Manajemen Pengguna';
        $data['users'] = $this->userModel->findAll();
        return view('user/index', $data);
    }
    public function create()
    {
        if (!in_array(session()->get('role'), ['admin_region', 'super_admin'])) {
            return redirect()->to('/unauthorized');
        }

        $data = [
            'title' => 'Tambah User Baru',
            'validation' => \Config\Services::validation()
        ];
        return view('user/create', $data);
    }


    public function store()
    {
        $model = new UserModel();
        $data = [
            'username'   => $this->request->getPost('username'),
            'password'   => password_hash($this->request->getPost('password'), PASSWORD_BCRYPT),
            'role'       => $this->request->getPost('role'),
            'kode_spbu'  => $this->request->getPost('kode_spbu'),
        ];
        $this->userModel->save($data);
        $model->save($data);
        return redirect()->to('/users')->with('success', 'Pengguna berhasil ditambahkan.');
    }

    public function edit($id)
    {
        if (!in_array(session()->get('role'), ['admin_region', 'super_admin'])) {
            return redirect()->to('/unauthorized');
        }

        $model = new UserModel();
        $data['title'] = 'Edit User'; 
        $data['user'] = $model->find($id);
        return view('user/edit', $data);
    }

    public function update($id)
    {
        $model = new UserModel();
        $data = [
            
            'username' => $this->request->getPost('username'),
            'role' => $this->request->getPost('role'),
            'kode_spbu' => $this->request->getPost('kode_spbu'),
            
        ];
        if ($this->request->getPost('password')) {
            $data['password'] = password_hash($this->request->getPost('password'), PASSWORD_DEFAULT);
        }
        $model->update($id, $data);
        return redirect()->to('/user');
    }

    public function hapus($id)
    {
        if (!in_array(session()->get('role'), ['admin_region', 'super_admin'])) {
            return redirect()->to('/unauthorized');
        }

        $model = new UserModel();
        $model->delete($id);
        return redirect()->to('/user');
    }
}