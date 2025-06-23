<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\DispenserModel;
use App\Models\LogDispenserModel;
use App\Models\KodeDispenserModel;
use App\Models\SpbuModel;

class Dispenser extends BaseController
{
    protected $dispenserModel;
    protected $logDispenserModel;
    protected $spbuModel;
    protected $kodeDispenserModel;

    public function __construct()
    {
        $this->dispenserModel = new DispenserModel();
        $this->logDispenserModel = new LogDispenserModel();
        $this->spbuModel = new SpbuModel();
        $this->kodeDispenserModel = new KodeDispenserModel();
        helper('Access');
    }

    public function index()
    {
        $role = session()->get('role');
        $kode_spbu = session()->get('kode_spbu');

        if ($role === 'admin_spbu') {
            $data['dispenserList'] = $this->dispenserModel
                ->where('kode_spbu', $kode_spbu)
                ->findAll();
        } else {
            $data['dispenserList'] = $this->dispenserModel->findAll();
        }

        $data['title'] = 'Data Dispenser';
        return view('dispenser/index', $data);
    }

    public function create()
    {
        $kode_spbu = session()->get('kode_spbu');
        $role = session()->get('role');

        if ($role === 'admin_spbu') {
            $spbuList = [$this->spbuModel->where('kode_spbu', $kode_spbu)->first()];
        } else {
            $spbuList = $this->spbuModel->findAll();
        }

        $data = [
            'title' => 'Tambah Dispenser',
            'spbuList' => $spbuList,
            'kodeDispenserList' => $this->kodeDispenserModel->findAll()
        ];

        return view('dispenser/create', $data);
    }

    public function store()
    {
        $kode_spbu = $this->request->getPost('kode_spbu');
        $kode_dispenser = $this->request->getPost('kode_dispenser');

        // Validasi: kode_dispenser unik per SPBU
        $exists = $this->dispenserModel
            ->where('kode_spbu', $kode_spbu)
            ->where('kode_dispenser', $kode_dispenser)
            ->first();

        if ($exists) {
            return redirect()->back()->withInput()->with('error', 'Kode Dispenser sudah digunakan di SPBU ini.');
        }

        $this->dispenserModel->save([
            'kode_spbu' => $kode_spbu,
            'kode_dispenser' => $kode_dispenser,
            'merek_dispenser' => $this->request->getPost('merek_dispenser'),
            'jumlah_nozzle' => $this->request->getPost('jumlah_nozzle'),
            'type_dispenser' => $this->request->getPost('type_dispenser'),
            'tgl_kalibrasi_berakhir' => $this->request->getPost('tgl_kalibrasi_berakhir')
        ]);
        
        return redirect()->to('/dispenser')->with('success', 'Dispenser berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $dispenser = $this->dispenserModel->find($id);
        if (!$dispenser || !hasAccessToSPBU($dispenser['kode_spbu'])) {
            return redirect()->to('/unauthorized');
        }

        $data = [
            'title' => 'Edit Dispenser',
            'dispenser' => $dispenser,
            'kodeDispenserList' => $this->kodeDispenserModel->findAll(),
            'spbuList' => [$this->spbuModel->where('kode_spbu', $dispenser['kode_spbu'])->first()]
        ];

        return view('dispenser/edit', $data);
    }

    public function update($id)
    {
        $dispenser = $this->dispenserModel->find($id);
        if (!$dispenser || !hasAccessToSPBU($dispenser['kode_spbu'])) {
            return redirect()->to('/unauthorized');
        }

        $data = [
            'id' => $id,
            'kode_spbu' => $dispenser['kode_spbu'],
            'kode_dispenser' => $this->request->getPost('kode_dispenser'),
            'merek_dispenser' => $this->request->getPost('merek_dispenser'),
            'jumlah_nozzle' => $this->request->getPost('jumlah_nozzle'),
            'type_dispenser' => $this->request->getPost('type_dispenser'),
            'tgl_kalibrasi_berakhir' => $this->request->getPost('tgl_kalibrasi_berakhir')
        ];

        // Simpan log
        $this->logDispenserModel->save([
            'dispenser_id' => $id,
            'kode_spbu' => $dispenser['kode_spbu'],
            'kode_dispenser' => $dispenser['kode_dispenser'],
            'merek_dispenser' => $dispenser['merek_dispenser'],
            'jumlah_nozzle' => $dispenser['jumlah_nozzle'],
            'type_dispenser' => $dispenser['type_dispenser'],
            'tgl_kalibrasi_berakhir' => $dispenser['tgl_kalibrasi_berakhir'],
            'updated_by' => session()->get('user_id')
        ]);

        $this->dispenserModel->save($data);

        return redirect()->to('/dispenser')->with('success', 'Dispenser berhasil diperbarui.');
        $this->logDispenserModel->save([
        'dispenser_id'            => $id,
        'kode_spbu'               => $kode_spbu,
        'kode_dispenser'          => $this->request->getPost('kode_dispenser'),
        'merek_dispenser'         => $this->request->getPost('merek_dispenser'),
        'jumlah_nozzle'           => $this->request->getPost('jumlah_nozzle'),
        'type_dispenser'          => $this->request->getPost('type_dispenser'),
        'tgl_kalibrasi_berakhir'  => $this->request->getPost('tgl_kalibrasi_berakhir'),
        'updated_by'              => session()->get('user_id'),
    ]);
    }
    public function log($id)
    {
        $data = [
            'title' => 'Riwayat Perubahan Dispenser',
            'logList' => $this->logDispenserModel
                ->where('dispenser_id', $id)
                ->orderBy('updated_at', 'DESC')
                ->findAll(),
        ];
        return view('dispenser/log', $data);
    }

    public function delete($id)
    {
        $dispenser = $this->dispenserModel->find($id);
        if (!$dispenser || !hasAccessToSPBU($dispenser['kode_spbu'])) {
            return redirect()->to('/unauthorized');
        }

        $this->dispenserModel->delete($id);
        return redirect()->to('/dispenser')->with('success', 'Dispenser berhasil dihapus.');
    }
}