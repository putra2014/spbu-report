<?php

namespace App\Controllers;

use App\Models\OperatorModel;
use App\Models\SpbuModel;

class Operator extends BaseController
{
    protected $operatorModel;
    protected $spbuModel;

    public function __construct()
    {
        $this->operatorModel = new OperatorModel();
        $this->spbuModel = new SpbuModel();
        helper('Access');
    }

    public function index()
    {
        $role = session('role');
        $kode_spbu = session('kode_spbu');

        if ($role === 'admin_spbu') {
            $data['operator'] = $this->operatorModel->where('kode_spbu', $kode_spbu)->findAll();
        } else {
            $data['operator'] = $this->operatorModel->findAll();
        }

        $data['title'] = 'Data Operator SPBU';
        return view('operator/index', $data);
    }

    public function create()
    {
        $data['title'] = 'Tambah Operator';

        if (session('role') == 'admin_spbu') {
            $data['spbuList'] = [
                ['kode_spbu' => session('kode_spbu')]
            ];
        } else {
            $data['spbuList'] = $this->spbuModel->findAll();
        }

        return view('operator/create', $data);
    }

    public function store()
    {
        $data = [
            'kode_spbu'     => $this->request->getPost('kode_spbu'),
            'nama_operator' => $this->request->getPost('nama_operator'),
            'no_hp'         => $this->request->getPost('no_hp'),
            'shift'         => $this->request->getPost('shift'),
        ];

        $this->operatorModel->save($data);
        return redirect()->to('/operator')->with('success', 'Data operator disimpan.');
    }

    public function edit($id)
    {
        $data['operator'] = $this->operatorModel->find($id);
        if (!$data['operator']) return redirect()->to('/operator');

        if (!hasAccessToSPBU($data['operator']['kode_spbu'])) {
            return redirect()->to('/unauthorized');
        }

        $data['title'] = 'Edit Operator';

        if (session('role') == 'admin_spbu') {
            $data['spbuList'] = [['kode_spbu' => session('kode_spbu')]];
        } else {
            $data['spbuList'] = $this->spbuModel->findAll();
        }

        return view('operator/edit', $data);
    }

    public function update($id)
    {
        $this->operatorModel->update($id, [
            'kode_spbu'     => $this->request->getPost('kode_spbu'),
            'nama_operator' => $this->request->getPost('nama_operator'),
            'no_hp'         => $this->request->getPost('no_hp'),
            'shift'         => $this->request->getPost('shift'),
        ]);

        return redirect()->to('/operator')->with('success', 'Data operator diperbarui.');
    }

    public function delete($id)
    {
        $this->operatorModel->delete($id);
        return redirect()->to('/operator')->with('success', 'Data operator dihapus.');
    }
}
