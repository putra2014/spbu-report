<?php
namespace App\Controllers;

use App\Models\KodeDispenserModel;

class KodeDispenser extends BaseController
{
    protected $model;

    public function __construct()
    {
        $this->model = new KodeDispenserModel();
    }

    public function index()
    {
        if (!in_array(session()->get('role'), ['admin_region', 'admin_area'])) {
            return redirect()->to('/unauthorized');
        }
        $data = [
            'title' => 'Master Kode Dispenser',
            'list' => $this->model->findAll()
        ];
        return view('kode_dispenser/index', $data);
    }

    public function create()
    {
        if (!in_array(session()->get('role'), ['admin_region', 'admin_area'])) {
            return redirect()->to('/unauthorized');
        }
        return view('kode_dispenser/create', ['title' => 'Tambah Kode Dispenser']);
    }

    public function store()
    {
        $this->model->save([
            'kode_dispenser' => $this->request->getPost('kode_dispenser'),
            'keterangan' => $this->request->getPost('keterangan')
        ]);
        return redirect()->to('/kode-dispenser')->with('success', 'Data berhasil ditambahkan.');
    }

    public function edit($id)
    {
        if (!in_array(session()->get('role'), ['admin_region', 'admin_area'])) {
            return redirect()->to('/unauthorized');
        }
        $data = [
            'title' => 'Edit Kode Dispenser',
            'item' => $this->model->find($id)
        ];
        return view('kode_dispenser/edit', $data);
    }

    public function update($id)
    {
        $this->model->save([
            'id' => $id,
            'kode_dispenser' => $this->request->getPost('kode_dispenser'),
            'keterangan' => $this->request->getPost('keterangan')
        ]);
        return redirect()->to('/kode-dispenser')->with('success', 'Data berhasil diperbarui.');
    }

    public function delete($id)
    {
        if (!in_array(session()->get('role'), ['admin_region', 'admin_area'])) {
            return redirect()->to('/unauthorized');
        }
        $this->model->delete($id);
        return redirect()->to('/kode-dispenser')->with('success', 'Data dihapus.');
    }
}
