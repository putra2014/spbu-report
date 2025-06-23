<?php
namespace App\Controllers;

use App\Models\WilayahModel;

class Wilayah extends BaseController
{
    protected $wilayah;

    public function __construct()
    {
        $this->wilayah = new WilayahModel();
    }

    public function index()
    {
        if (!in_array(session()->get('role'), ['admin_region', 'admin_area'])) {
            return redirect()->to('/unauthorized');
        }

        $data['wilayah'] = $this->wilayah->findAll();
        return view('wilayah/index', $data);
    }

    public function create()
    {
        if (!in_array(session()->get('role'), ['admin_region', 'admin_area'])) {
            return redirect()->to('/unauthorized');
        }

        return view('wilayah/create');
    }

    public function store()
    {
        $this->wilayah->save([
            'nama_wilayah' => $this->request->getPost('nama_wilayah')
        ]);
        return redirect()->to('/wilayah');
    }

    public function edit($id)
    {
        if (!in_array(session()->get('role'), ['admin_region', 'admin_area'])) {
            return redirect()->to('/unauthorized');
        }

        $data['wilayah'] = $this->wilayah->find($id);
        return view('wilayah/edit', $data);
    }

    public function update($id)
    {
        $this->wilayah->update($id, [
            'nama_wilayah' => $this->request->getPost('nama_wilayah')
        ]);
        return redirect()->to('/wilayah');
    }

    public function delete($id)
    {
        if (!in_array(session()->get('role'), ['admin_region', 'admin_area'])) {
            return redirect()->to('/unauthorized');
        }

        $this->wilayah->delete($id);
        return redirect()->to('/wilayah');
    }
}

