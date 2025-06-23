<?php

namespace App\Controllers;

use App\Models\PerusahaanModel;

class Perusahaan extends BaseController
{
    protected $perusahaanModel;

    public function __construct()
    {
        $this->perusahaanModel = new PerusahaanModel();
        helper('Access');
    }

    public function index()
    {
        if (!hasAccessToSPBU(session()->get('kode_spbu'))) {
            return redirect()->to('/unauthorized');
        }

        $kode_spbu = session()->get('kode_spbu');
        $data = [
            'title' => 'Daftar Perusahaan',
            'perusahaan' => $this->perusahaanModel->getBySpbu($kode_spbu)
        ];
        return view('perusahaan/index', $data);
    }

    public function create()
    {
        if (!hasAccessToSPBU(session()->get('kode_spbu'))) {
            return redirect()->to('/unauthorized');
        }

        // Load the ProvinsiModel
        $provinsiModel = new \App\Models\ProvinsiModel();

        $data = [
            'kode_spbu' => session()->get('kode_spbu'),
            'title' => 'Input Data Perusahaan',
            'validation' => \Config\Services::validation(),
            'provinsi' => $provinsiModel->findAll() // Add this line to pass province data to view
        ];

        return view('perusahaan/create', $data);
    }

    public function save()
    {
        // Validasi input
        $rules = [
            'nama_perusahaan' => 'required',
            'nama_pengusaha' => 'required',
            'jabatan' => 'required',
            'alamat' => 'required',
            'kabupaten_kota' => 'required',
            'provinsi' => 'required',
            'no_handphone' => 'required|numeric'
        ];

        if (!$this->validate($rules)) {
            return redirect()->to('/perusahaan/create')->withInput()->with('validation', $this->validator);
        }

        $this->perusahaanModel->save([
            'nama_perusahaan' => $this->request->getVar('nama_perusahaan'),
            'nama_pengusaha' => $this->request->getVar('nama_pengusaha'),
            'jabatan' => $this->request->getVar('jabatan'),
            'alamat' => $this->request->getVar('alamat'),
            'kabupaten_kota' => $this->request->getVar('kabupaten_kota'),
            'provinsi' => $this->request->getVar('provinsi'),
            'no_handphone' => $this->request->getVar('no_handphone')
        ]);

        return redirect()->to('/perusahaan')->with('success', 'Data perusahaan berhasil ditambahkan');
    }

    public function edit($id)
    {
        $data = [
            'title' => 'Edit Data Perusahaan',
            'validation' => \Config\Services::validation(),
            'perusahaan' => $this->perusahaanModel->find($id)
        ];

        return view('perusahaan/edit', $data);
    }

    public function update($id)
    {
        // Validasi input
        $rules = [
            'nama_perusahaan' => 'required',
            'nama_pengusaha' => 'required',
            'jabatan' => 'required',
            'alamat' => 'required',
            'kabupaten_kota' => 'required',
            'provinsi' => 'required',
            'no_handphone' => 'required|numeric'
        ];

        if (!$this->validate($rules)) {
            return redirect()->to('/perusahaan/edit/' . $id)->withInput()->with('validation', $this->validator);
        }

        $this->perusahaanModel->save([
            'id' => $id,
            'nama_perusahaan' => $this->request->getVar('nama_perusahaan'),
            'nama_pengusaha' => $this->request->getVar('nama_pengusaha'),
            'jabatan' => $this->request->getVar('jabatan'),
            'alamat' => $this->request->getVar('alamat'),
            'kabupaten_kota' => $this->request->getVar('kabupaten_kota'),
            'provinsi' => $this->request->getVar('provinsi'),
            'no_handphone' => $this->request->getVar('no_handphone')
        ]);

        return redirect()->to('/perusahaan')->with('success', 'Data perusahaan berhasil diubah');
    }

    public function delete($id)
    {
        $this->perusahaanModel->delete($id);
        return redirect()->to('/perusahaan')->with('success', 'Data perusahaan berhasil dihapus');
    }
    // Tambahkan method ini di Perusahaan Controller
    public function getKabupatenByProvinsi()
    {
        // Only allow AJAX requests
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(405)->setJSON([
                'error' => 'Method Not Allowed'
            ]);
        }
    
        $provinsiName = $this->request->getPost('provinsi');
        
        if (empty($provinsiName)) {
            return $this->response->setJSON([
                'error' => 'Province name is required'
            ]);
        }
    
        $provinsiModel = new \App\Models\ProvinsiModel();
        $provinsi = $provinsiModel->where('nama', $provinsiName)->first();
    
        if (!$provinsi) {
            return $this->response->setJSON([]);
        }
    
        $kabupatenModel = new \App\Models\KabupatenModel();
        $kabupaten = $kabupatenModel->select('id, nama_kabupaten')
                                  ->where('provinsi_id', $provinsi['id'])
                                  ->findAll();
    
        return $this->response->setJSON($kabupaten);
    }
}