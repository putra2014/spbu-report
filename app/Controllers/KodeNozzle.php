<?php
namespace App\Controllers;

use App\Models\KodeNozzleModel;

class KodeNozzle extends BaseController
{
    protected $model;

    public function __construct()
    {
        $this->model = new KodeNozzleModel();
    }

    public function index()
    {
        if (!in_array(session()->get('role'), ['admin_region', 'admin_area'])) {
            return redirect()->to('/unauthorized');
        }

        $data = [
            'title' => 'Master Kode Nozzle',
            'list' => $this->model->findAll()
        ];
        return view('kode_nozzle/index', $data);
    }

    public function create()
    {
        if (!in_array(session()->get('role'), ['admin_region', 'admin_area'])) {
            return redirect()->to('/unauthorized');
        }
        return view('kode_nozzle/create', ['title' => 'Tambah Kode Nozzle']);
    }

    public function store()
    {
        $this->model->save([
            'kode_nozzle' => $this->request->getPost('kode_nozzle'),
            'keterangan' => $this->request->getPost('keterangan')
        ]);
        return redirect()->to('/kode-nozzle')->with('success', 'Data berhasil ditambahkan.');
    }

    public function edit($id)
    {
        if (!in_array(session()->get('role'), ['admin_region', 'admin_area'])) {
            return redirect()->to('/unauthorized');
        }

        $data = [
            'title' => 'Edit Kode Nozzle',
            'item' => $this->model->find($id)
        ];
        return view('kode_nozzle/edit', $data);
    }

    public function update($id)
    {
        $this->model->save([
            'id' => $id,
            'kode_nozzle' => $this->request->getPost('kode_nozzle'),
            'keterangan' => $this->request->getPost('keterangan')
        ]);
        return redirect()->to('/kode-nozzle')->with('success', 'Data berhasil diperbarui.');
    }

    public function delete($id)
    {
        if (!in_array(session()->get('role'), ['admin_region', 'admin_area'])) {
            return redirect()->to('/unauthorized');
        }

        $this->model->delete($id);
        return redirect()->to('/kode-nozzle')->with('success', 'Data dihapus.');
    }
}
