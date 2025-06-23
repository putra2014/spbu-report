<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\SpbuModel;

class Auth extends BaseController
{
    public function index()
    {
        return view('auth/login');
    }

    public function loginProcess()
    {
        $username = $this->request->getPost('username');
        $password = $this->request->getPost('password');

        $user = (new UserModel())->where('username', $username)->first();

        if (!$user || !password_verify($password, $user['password'])) {
            return redirect()->to('/login')->with('error', 'Username atau password salah.');
        }

        session()->set([
            'user_id'     => $user['id'],
            'username'    => $user['username'],
            'role'        => $user['role'],
            'kode_spbu'   => (int)$user['kode_spbu'], // disimpan sebagai INT
            'logged_in'   => true
        ]);

        // Cek kelengkapan data SPBU jika admin_spbu
        if ($user['role'] === 'admin_spbu') {
            $spbu = (new SpbuModel())->where('kode_spbu', $user['kode_spbu'])->first();
            if (
                !$spbu ||
                empty($spbu['nama_spbu']) ||
                empty($spbu['provinsi_id']) ||
                empty($spbu['kabupaten_id'])
            ) {
                session()->set('spbu_incomplete', true);
                return redirect()->to('/spbu/edit/' . $user['kode_spbu']);
            } else {
                session()->remove('spbu_incomplete');
            }
        }

        return redirect()->to('/dashboard');
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('/login');
    }
}
