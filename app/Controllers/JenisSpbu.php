<?php
namespace App\Controllers;
use App\Models\JenisSpbuModel;

class JenisSpbu extends BaseController
{
    protected $model;
    public function __construct()
    {
        $this->model = new JenisSpbuModel();
    }

    public function index()
    {
        if (!in_array(session()->get('role'), ['admin_region', 'admin_area'])) {
            return redirect()->to('/unauthorized');
        }
        $data['jenis'] = $this->model->findAll();
        return view('jenis_spbu/index', $data);
    }

    public function create()
    {
        if (!in_array(session()->get('role'), ['admin_region', 'admin_area'])) {
            return redirect()->to('/unauthorized');
        }
        return view('jenis_spbu/create');
    }

    public function store()
    {
        if (!in_array(session()->get('role'), ['admin_region', 'admin_area'])) {
            return redirect()->to('/unauthorized');
        }
        $this->model->save(['nama_jenis' => $this->request->getPost('nama_jenis')]);
        return redirect()->to('/jenis-spbu');
    }

    public function edit($id)
    {
        if (!in_array(session()->get('role'), ['admin_region', 'admin_area'])) {
            return redirect()->to('/unauthorized');
        }
        $data['jenis'] = $this->model->find($id);
        return view('jenis_spbu/edit', $data);
    }

    public function update($id)
    {
        $this->model->update($id, ['nama_jenis' => $this->request->getPost('nama_jenis')]);
        return redirect()->to('/jenis-spbu');
    }

    public function delete($id)
    {
        if (!in_array(session()->get('role'), ['admin_region', 'admin_area'])) {
            return redirect()->to('/unauthorized');
        }
        $this->model->delete($id);
        return redirect()->to('/jenis-spbu');
    }
}
