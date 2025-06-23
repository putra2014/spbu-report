<?php
namespace App\Controllers;

use App\Models\ProvinsiModel;

class Provinsi extends BaseController
{
    protected $model;

    public function __construct()
    {
        $this->model = new ProvinsiModel();
    }

    public function index()
    {
        if (!in_array(session()->get('role'), ['admin_region', 'admin_area'])) {
            return redirect()->to('/unauthorized');
        }

        return view('provinsi/index', [
            'title' => 'Data Provinsi',
            'provinsi' => $this->model->findAll()
        ]);
    }

    public function create()
    {
        if (!in_array(session()->get('role'), ['admin_region', 'admin_area'])) {
            return redirect()->to('/unauthorized');
        }


        return view('provinsi/create', ['title' => 'Tambah Provinsi']);
    }

    public function store()
    {
        $this->model->save(['nama' => $this->request->getPost('nama')]);
        return redirect()->to('/provinsi')->with('success', 'Provinsi ditambahkan.');
    }

    public function edit($id)
    {
        if (!in_array(session()->get('role'), ['admin_region', 'admin_area'])) {
            return redirect()->to('/unauthorized');
        }

        return view('provinsi/edit', [
            'title' => 'Edit Provinsi',
            'provinsi' => $this->model->find($id)
        ]);
    }

    public function update($id)
    {
        $this->model->save(['id' => $id, 'nama' => $this->request->getPost('nama')]);
        return redirect()->to('/provinsi')->with('success', 'Provinsi diperbarui.');
    }

    public function delete($id)
    {
        if (!in_array(session()->get('role'), ['admin_region', 'admin_area'])) {
            return redirect()->to('/unauthorized');
        }

        $this->model->delete($id);
        return redirect()->to('/provinsi')->with('success', 'Provinsi dihapus.');
    }
}
