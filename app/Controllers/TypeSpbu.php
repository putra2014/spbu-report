<?php
namespace App\Controllers;
use App\Models\TypeSpbuModel;

class TypeSpbu extends BaseController
{
    protected $model;
    public function __construct()
    {
        $this->model = new TypeSpbuModel();
    }

    public function index()
    {
        if (!in_array(session()->get('role'), ['admin_region', 'admin_area'])) {
            return redirect()->to('/unauthorized');
        }

        $data['type'] = $this->model->findAll();
        return view('type_spbu/index', $data);
    }

    public function create()
    {
        if (!in_array(session()->get('role'), ['admin_region', 'admin_area'])) {
            return redirect()->to('/unauthorized');
        }

        return view('type_spbu/create');
    }

    public function store()
    {
        $this->model->save(['nama_type' => $this->request->getPost('nama_type')]);
        return redirect()->to('/type-spbu');
    }

    public function edit($id)
    {
        if (!in_array(session()->get('role'), ['admin_region', 'admin_area'])) {
            return redirect()->to('/unauthorized');
        }

        $data['type'] = $this->model->find($id);
        return view('type_spbu/edit', $data);
    }

    public function update($id)
    {
        $this->model->update($id, ['nama_type' => $this->request->getPost('nama_type')]);
        return redirect()->to('/type-spbu');
    }

    public function delete($id)
    {
        if (!in_array(session()->get('role'), ['admin_region', 'admin_area'])) {
            return redirect()->to('/unauthorized');
        }

        $this->model->delete($id);
        return redirect()->to('/type-spbu');
    }
}
