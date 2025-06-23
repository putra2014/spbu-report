<?php
namespace App\Controllers;
use App\Models\KelasSpbuModel;

class KelasSpbu extends BaseController
{
    protected $model;
    public function __construct()
    {
        $this->model = new KelasSpbuModel();
    }

    public function index()
    {
        if (!in_array(session()->get('role'), ['admin_region', 'admin_area'])) {
            return redirect()->to('/unauthorized');
        }
        $data['kelas'] = $this->model->findAll();
        return view('kelas_spbu/index', $data);
    }

    public function create()
    {
        if (!in_array(session()->get('role'), ['admin_region', 'admin_area'])) {
            return redirect()->to('/unauthorized');
        }
        return view('kelas_spbu/create');
    }

    public function store()
    {
        $this->model->save(['nama_kelas' => $this->request->getPost('nama_kelas')]);
        return redirect()->to('/kelas-spbu');
    }

    public function edit($id)
    {
        if (!in_array(session()->get('role'), ['admin_region', 'admin_area'])) {
            return redirect()->to('/unauthorized');
        }
        $data['kelas'] = $this->model->find($id);
        return view('kelas_spbu/edit', $data);
    }

    public function update($id)
    {
        $this->model->update($id, ['nama_kelas' => $this->request->getPost('nama_kelas')]);
        return redirect()->to('/kelas-spbu');
    }

    public function delete($id)
    {
        if (!in_array(session()->get('role'), ['admin_region', 'admin_area'])) {
            return redirect()->to('/unauthorized');
        }
        $this->model->delete($id);
        return redirect()->to('/kelas-spbu');
    }
}
