<?php
namespace App\Controllers;

use App\Models\AreaModel;
use App\Models\WilayahModel;

class Area extends BaseController
{
    protected $area, $wilayah;

    public function __construct()
    {
        $this->area = new AreaModel();
        $this->wilayah = new WilayahModel();
        helper('Access');
    }

    public function index()
    {
        if (!in_array(session()->get('role'), ['admin_region', 'admin_area'])) {
            return redirect()->to('/unauthorized');
        }
        $data['area'] = $this->area->withWilayah()->findAll();
        return view('area/index', $data);
    }

    public function create()
    {
        if (!in_array(session()->get('role'), ['admin_region', 'admin_area'])) {
            return redirect()->to('/unauthorized');
        }
        $data['wilayah'] = $this->wilayah->findAll();
        return view('area/create', $data);
    }

    public function store()
    {
        $this->area->save([
            'wilayah_id' => $this->request->getPost('wilayah_id'),
            'nama_area' => $this->request->getPost('nama_area')
        ]);
        return redirect()->to('/area');
    }

    public function edit($id)
    {
        if (!in_array(session()->get('role'), ['admin_region', 'admin_area'])) {
            return redirect()->to('/unauthorized');
        }
        $data['area'] = $this->area->find($id);
        $data['wilayah'] = $this->wilayah->findAll();
        return view('area/edit', $data);
    }

    public function update($id)
    {
        $this->area->update($id, [
            'wilayah_id' => $this->request->getPost('wilayah_id'),
            'nama_area' => $this->request->getPost('nama_area')
        ]);
        return redirect()->to('/area');
    }

    public function delete($id)
    {
        if (!in_array(session()->get('role'), ['admin_region', 'admin_area'])) {
            return redirect()->to('/unauthorized');
        } 
        $this->area->delete($id);
        return redirect()->to('/area');
    }
}
