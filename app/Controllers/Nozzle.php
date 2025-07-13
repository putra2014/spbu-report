<?php

namespace App\Controllers;

use App\Models\NozzleModel;
use App\Models\DispenserModel;
use App\Models\TangkiModel;
use App\Models\KodeNozzleModel;
use App\Models\SpbuModel;

class Nozzle extends BaseController
{
    protected $nozzleModel, $dispenserModel, $tangkiModel, $kodeNozzleModel,$spbuModel;

    public function __construct()
    {
        $this->nozzleModel = new NozzleModel();
        $this->dispenserModel = new DispenserModel();
        $this->tangkiModel = new TangkiModel();
        $this->kodeNozzleModel = new KodeNozzleModel();
        $this->spbuModel = new SpbuModel();
    }

    public function index()
    {
        $role = session()->get('role');
        $kode_spbu = session()->get('kode_spbu');

        if ($role === 'admin_spbu') {
            $data['nozzleList'] = $this->nozzleModel
                ->where('kode_spbu', $kode_spbu)
                ->findAll();
        } else {
            $data['nozzleList'] = $this->nozzleModel->findAll();
        }

        $data['title'] = 'Data Nozzle';
        return view('nozzle/index', $data);
    }

    public function create()
    {
        $role = session()->get('role');
        $kode_spbu = session()->get('kode_spbu');

        $data['title'] = 'Tambah Nozzle';
        $data['kodeNozzleList'] = $this->kodeNozzleModel->findAll();

        if ($role === 'admin_spbu') {
            $data['dispenserList'] = $this->dispenserModel->where('kode_spbu', $kode_spbu)->findAll();
            $data['tangkiList'] = $this->tangkiModel->where('kode_spbu', $kode_spbu)->findAll();
        } else {
            $data['dispenserList'] = $this->dispenserModel->findAll();
            $data['tangkiList'] = $this->tangkiModel->findAll();
            $data['spbuList'] = $this->spbuModel->findAll();
        }

        return view('nozzle/create', $data);
    }

    public function store()
    {
        $kode_spbu     = session()->get('kode_spbu');
        $dispenser_id  = $this->request->getPost('dispenser_id');
        $kode_nozzle   = $this->request->getPost('kode_nozzle');
        $kode_tangki   = $this->request->getPost('tangki_id');
        $status        = $this->request->getPost('status');
        $catatan       = $this->request->getPost('catatan');
    
        // Validasi kode nozzle duplikat
        $duplikat = $this->nozzleModel
            ->where('dispenser_id', $dispenser_id)
            ->where('kode_nozzle', $kode_nozzle)
            ->first();
    
        if ($duplikat) {
            return redirect()->back()->with('error', 'Kode Nozzle "' . $kode_nozzle . '" sudah digunakan di dispenser ini.')->withInput();
        }
    
        // Validasi jumlah nozzle
        $dispenser = $this->dispenserModel->find($dispenser_id);
        $jumlahSaatIni = $this->nozzleModel->where('dispenser_id', $dispenser_id)->countAllResults();
    
        if ($jumlahSaatIni >= $dispenser['jumlah_nozzle']) {
            return redirect()->back()->with('error', 'Jumlah Nozzle pada dispenser melebihi maksimal (' . $dispenser['jumlah_nozzle'] . ')')->withInput();
        }
    
        // Validasi tangki dan produk
        $tangki = $this->tangkiModel->find($kode_tangki);
        if (!$tangki) {
            return redirect()->back()
                ->with('error', 'Tangki tidak ditemukan')
                ->withInput();
        }
    
        $kode_produk = $tangki['kode_produk'];
        if (empty($kode_produk)) {
            return redirect()->back()
                ->with('error', 'Tangki yang dipilih belum memiliki produk terkait')
                ->withInput();
        }
    
        // Simpan data
        $this->nozzleModel->save([
            'kode_spbu'    => $kode_spbu,
            'dispenser_id' => $dispenser_id,
            'kode_nozzle'  => $kode_nozzle,
            'kode_tangki'  => $tangki['kode_tangki'] ?? '',
            'kode_produk'   => $kode_produk,
            'status'       => $status,
            'catatan'      => $catatan,
            'initial_meter' => $this->request->getPost('initial_meter'),
            'current_meter' => $this->request->getPost('initial_meter')
        ]);
    
        return redirect()->to('/nozzle')->with('success', 'Data Nozzle berhasil disimpan.');
    }



    public function edit($id)
    {
        $nozzle = $this->nozzleModel->find($id);
        if (!$nozzle || !hasAccessToSPBU($nozzle['kode_spbu'])) {
            return redirect()->to('/unauthorized');
        }

        $data['title'] = 'Edit Nozzle';
        $data['nozzle'] = $nozzle;
        $data['kodeNozzleList'] = $this->kodeNozzleModel->findAll();
        $kodeSpbu = session()->get('kode_spbu');

        $role = session()->get('role');
        if ($role === 'admin_spbu') {
            $data['dispenserList'] = $this->dispenserModel->where('kode_spbu', $kodeSpbu)->findAll();
            $data['tangkiList'] = $this->tangkiModel->where('kode_spbu', $kodeSpbu)->findAll();
        } else {
            $data['dispenserList'] = $this->dispenserModel->findAll();
            $data['tangkiList'] = $this->tangkiModel->findAll();
        }

        return view('nozzle/edit', $data);
    }

    public function update($id)
    {
        $nozzle = $this->nozzleModel->find($id);
        if (!$nozzle || !hasAccessToSPBU($nozzle['kode_spbu'])) {
            return redirect()->to('/unauthorized');
        }

        $tangkiId = $this->request->getPost('tangki_id');
        $tangki = $this->tangkiModel->find($tangkiId);

        $this->nozzleModel->save([
            'id'           => $id,
            'kode_nozzle'  => $this->request->getPost('kode_nozzle'),
            'dispenser_id' => $this->request->getPost('dispenser_id'),
            'kode_tangki'  => $tangki['kode_tangki'],
            'kode_produk'  => $tangki['kode_produk'],
            'status'       => $this->request->getPost('status'),
            'catatan'      => $this->request->getPost('catatan'),
        ]);

        return redirect()->to('/nozzle')->with('success', 'Nozzle berhasil diperbarui.');
    }

    public function delete($id)
    {
        $nozzle = $this->nozzleModel->find($id);
        if (!$nozzle || !hasAccessToSPBU($nozzle['kode_spbu'])) {
            return redirect()->to('/unauthorized');
        }

        $this->nozzleModel->delete($id);
        return redirect()->to('/nozzle')->with('success', 'Nozzle berhasil dihapus.');
    }
    public function getByDispenser()
    {
        $dispenserId = $this->request->getGet('dispenser_id');
        $nozzles = $this->nozzleModel->where('dispenser_id', $dispenserId)->findAll();
        return $this->response->setJSON($nozzles);
    }
}
