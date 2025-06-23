<?php

namespace App\Controllers;

use App\Models\ProdukModel;

class Produk extends BaseController
{
    protected $model;

    public function __construct()
    {
        $this->model = new ProdukModel();
    }

    public function index()
    {
        if (!in_array(session()->get('role'), ['admin_region', 'admin_area'])) {
            return redirect()->to('/unauthorized');
        }

        $data = [
            'title' => 'Data Produk BBM',
            'produk' => $this->model->findAll()
        ];

        return view('produk/index', $data);
    }

    public function create()
    {
        if (!in_array(session()->get('role'), ['admin_region', 'admin_area'])) {
            return redirect()->to('/unauthorized');
        }

        return view('produk/create', ['title' => 'Tambah Produk BBM']);
    }

    public function store()
    {
        $this->model->save([
            'kode_produk' => $this->request->getPost('kode_produk'),
            'nama_produk' => $this->request->getPost('nama_produk'),
            'kategori' => $this->request->getPost('kategori'),
            'jenis' => $this->request->getPost('jenis')
        ]);

        return redirect()->to('/produk')->with('success', 'Produk ditambahkan.');
    }

    public function edit($id)
    {
        if (!in_array(session()->get('role'), ['admin_region', 'admin_area'])) {
            return redirect()->to('/unauthorized');
        }

        $data = $this->model->find($id);
        return view('produk/edit', ['title' => 'Edit Produk', 'produk' => $data]);
    }

    public function update($id)
    {
        $this->model->save([
            'id'   => $id,
            'kode_produk' => $this->request->getPost('kode_produk'),
            'nama_produk' => $this->request->getPost('nama_produk'),
            'kategori' => $this->request->getPost('kategori'),
            'jenis' => $this->request->getPost('jenis')
        ]);

        return redirect()->to('/produk')->with('success', 'Produk diperbarui.');
    }

    public function delete($id)
    {
        if (!in_array(session()->get('role'), ['admin_region', 'admin_area'])) {
            return redirect()->to('/unauthorized');
        }

        $this->model->delete($id);
        return redirect()->to('/produk')->with('success', 'Produk dihapus.');
    }
}
