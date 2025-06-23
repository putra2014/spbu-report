<?php
namespace App\Controllers;

use App\Models\KabupatenModel;
use App\Models\ProvinsiModel;

class Kabupaten extends BaseController
{
    protected $model, $provinsiModel;

    public function __construct()
    {
        $this->model = new KabupatenModel();
        $this->provinsiModel = new ProvinsiModel();
    }

    public function index()
    {
        if (!in_array(session()->get('role'), ['admin_region', 'admin_area'])) {
            return redirect()->to('/unauthorized');
        }
        $kabupaten = $this->model
            ->select('kabupaten.*, provinsi.nama AS provinsi_nama')
            ->join('provinsi', 'provinsi.id = kabupaten.provinsi_id')
            ->findAll();

        return view('kabupaten/index', [
            'title' => 'Data Kabupaten',
            'kabupaten' => $kabupaten
        ]);
    }

    public function create()
    {
        if (!in_array(session()->get('role'), ['admin_region', 'admin_area'])) {
            return redirect()->to('/unauthorized');
        }
        return view('kabupaten/create', [
            'title' => 'Tambah Kabupaten',
            'provinsiList' => $this->provinsiModel->findAll()
        ]);
    }

    public function store()
    {
        
        $this->model->save([
            'provinsi_id' => $this->request->getPost('provinsi_id'),
            'nama_kabupaten' => $this->request->getPost('nama_kabupaten')
        ]);
        return redirect()->to('/kabupaten')->with('success', 'Kabupaten ditambahkan.');
    }

    public function edit($id)
    {
        if (!in_array(session()->get('role'), ['admin_region', 'admin_area'])) {
            return redirect()->to('/unauthorized');
        }
        return view('kabupaten/edit', [
            'title' => 'Edit Kabupaten',
            'kabupaten' => $this->model->find($id),
            'provinsiList' => $this->provinsiModel->findAll()
        ]);
    }

    public function update($id)
    {
        $this->model->save([
            'id' => $id,
            'provinsi_id' => $this->request->getPost('provinsi_id'),
            'nama_kabupaten' => $this->request->getPost('nama_kabupaten')
        ]);
        return redirect()->to('/kabupaten')->with('success', 'Kabupaten diperbarui.');
    }

    public function delete($id)
    {
        if (!in_array(session()->get('role'), ['admin_region', 'admin_area'])) {
            return redirect()->to('/unauthorized');
        }
        $this->model->delete($id);
        return redirect()->to('/kabupaten')->with('success', 'Kabupaten dihapus.');
    }
}
