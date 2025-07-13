<?php

namespace App\Controllers;

use App\Models\PenerimaanModel;
use App\Models\SpbuModel;
use App\Models\TangkiModel;
use App\Models\ProdukModel;
use App\Models\StokModel;

use Config\Database;


class Penerimaan extends BaseController
{
    protected $penerimaanModel;
    protected $spbuModel;
    protected $tangkiModel; 
    protected $produkModel;
    protected $stokModel;
    protected $db;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
        $this->penerimaanModel = new PenerimaanModel();
        $this->spbuModel = new SpbuModel();
        $this->tangkiModel = new TangkiModel();
        $this->produkModel = new ProdukModel();
        $this->stokModel = new StokModel();
        helper(['form', 'Access','shift']);
    }
    protected function _getCurrentShift()
    {
        $hour = date('H');
        if ($hour >= 6 && $hour < 14) return '1';
        if ($hour >= 14 && $hour < 22) return '2';
        return '3';
    }


    public function index()
    {
        $role = session()->get('role');
        $kode_spbu = session()->get('kode_spbu');
    
        if ($role === 'admin_spbu') {
            $data['penerimaan'] = $this->penerimaanModel
                ->select('penerimaan.*, produk_bbm.nama_produk')
                ->join('produk_bbm', 'produk_bbm.kode_produk = penerimaan.kode_produk', 'left')
                ->where('penerimaan.kode_spbu', $kode_spbu)
                ->findAll();
        } else {
            $data['penerimaan'] = $this->penerimaanModel->getAllWithProduk();
        }
    
        return view('penerimaan/index', $data + ['title' => 'Data Penerimaan BBM']);
    }

    public function create()
    {
        $kode_spbu = session()->get('kode_spbu');
        $role = session()->get('role');

        $data['title'] = 'Tambah Penerimaan BBM';
        $data['produkList'] = $this->produkModel->findAll();

        if ($role === 'admin_spbu') {
            $spbu = $this->spbuModel->where('kode_spbu', $kode_spbu)->first();
            $data['spbuList'] = [$spbu];
            $data['tangkiList'] = $this->tangkiModel->where('kode_spbu', $kode_spbu)->findAll();
        } else {
            $data['spbuList'] = $this->spbuModel->findAll();
            $data['tangkiList'] = $this->tangkiModel->findAll();
        }

        return view('penerimaan/create', $data);
    }

// Di dalam Penerimaan.php
    public function store()
    {
        $role = session()->get('role');
        $kode_spbu = $this->request->getPost('kode_spbu');
        
        if ($role === 'admin_spbu') {
            $kode_spbu = session()->get('kode_spbu');
        }
        
        if (!hasAccessToSPBU($kode_spbu)) {
            return redirect()->to('/unauthorized');
        }
        
        $tangki = $this->tangkiModel->find($this->request->getPost('tangki_id'));
        $volume_do = floatval($this->request->getPost('volume_do'));
        
        $data = [
            'tanggal'          => $this->request->getPost('tanggal'),
            'kode_spbu'        => $kode_spbu,
            'tangki_id'        => $this->request->getPost('tangki_id'),
            'nomor_do'         => $this->request->getPost('nomor_do'),
            'volume_do'        => $volume_do,
            'volume_diterima'  => 0, // Default 0, akan diupdate setelah dipping
            'harga_beli'       => $this->request->getPost('harga_beli'),
            'supir'            => $this->request->getPost('supir'),
            'catatan'          => $this->request->getPost('catatan'),
            'status'           => 'pending_dipping'
        ];
        
        if ($this->penerimaanModel->save($data)) {
            return redirect()->to('/penerimaan/dipping/'.$this->penerimaanModel->getInsertID())
                           ->with('success', 'Data penerimaan berhasil disimpan. Silahkan input hasil dipping.');
        }
        
        return redirect()->back()
                       ->withInput()
                       ->with('errors', $this->penerimaanModel->errors());
    }

    public function dipping($id)
    {
        $penerimaan = $this->penerimaanModel->find($id);
        if (!$penerimaan || $penerimaan['status'] !== 'pending_dipping') {
            return redirect()->to('/penerimaan')->with('error', 'Penerimaan tidak valid untuk dipping');
        }

        $data = [
            'title' => 'Input Hasil Dipping',
            'penerimaan' => $penerimaan,
            'tangki' => $this->tangkiModel->find($penerimaan['tangki_id'])
        ];

        return view('penerimaan/dipping_form', $data);
    }

    public function processDipping($id)
    {
        $penerimaan = $this->penerimaanModel->find($id);
        if (!$penerimaan) {
            return redirect()->to('/penerimaan')->with('error', 'Data penerimaan tidak ditemukan');
        }

        $validation = $this->validate([
            'volume_diterima' => 'required|numeric|greater_than[0]'
        ]);

        if (!$validation) {
            return redirect()->back()
                           ->withInput()
                           ->with('errors', $this->validator->getErrors());
        }

        $volume_diterima = (float)$this->request->getPost('volume_diterima');

        $this->db->transStart();
        try {
            // 1. Update penerimaan
            $this->penerimaanModel->update($id, [
                'volume_diterima' => $volume_diterima,
                'dipping_by' => session()->get('user_id'),
                'dipping_time' => date('Y-m-d H:i:s'),
                'status' => 'completed'
            ]);

            // 2. Update stok
            $this->stokModel->updateStokAfterDipping(
                $penerimaan['kode_spbu'],
                $penerimaan['tangki_id'],
                $penerimaan['tanggal'],
                getCurrentShift(),
                $penerimaan['volume_do'],
                $volume_diterima
            );

            // 3. Update tangki
            $this->tangkiModel->update($penerimaan['tangki_id'], [
                'stok' => $volume_diterima
            ]);

            $this->db->transComplete();

            return redirect()->to('/penerimaan')->with('success', 'Hasil dipping berhasil disimpan');
        } catch (\Exception $e) {
            $this->db->transRollback();
            return redirect()->back()->with('error', 'Gagal menyimpan dipping: '.$e->getMessage());
        }
    }


    public function edit($id)
    {
        $penerimaan = $this->penerimaanModel->find($id);
        if (!$penerimaan || !hasAccessToSPBU($penerimaan['kode_spbu'])) {
            return redirect()->to('/unauthorized');
        }

        $data['penerimaan'] = $penerimaan;
        $data['title'] = 'Edit Penerimaan';
        $data['produkList'] = $this->produkModel->findAll();

        $role = session()->get('role');

        if ($role === 'admin_spbu') {
            $kode_spbu = session()->get('kode_spbu');
            $spbu = $this->spbuModel->where('kode_spbu', $kode_spbu)->first();
            $data['spbuList'] = [$spbu];
            $data['tangkiList'] = $this->tangkiModel->where('kode_spbu', $kode_spbu)->findAll();
        } else {
            $data['spbuList'] = $this->spbuModel->findAll();
            $data['tangkiList'] = $this->tangkiModel->findAll();
        }

        return view('penerimaan/edit', $data);
    }

    public function update($id)
    {
        $penerimaan = $this->penerimaanModel->find($id);
        if (!$penerimaan || !hasAccessToSPBU($penerimaan['kode_spbu'])) {
            return redirect()->to('/unauthorized');
        }

        $volume_do = floatval($this->request->getPost('volume_do'));
        $volume_diterima = floatval($this->request->getPost('volume_diterima'));
        $selisih = $volume_diterima - $volume_do;
        $status = ($selisih == 0) ? 'Cocok' : (($selisih > 0) ? 'Lebih' : 'Kurang');

        $this->penerimaanModel->update($id, [
            'tangki_id'        => $this->request->getPost('tangki_id'),
            'kode_produk'      => $this->request->getPost('kode_produk'),
            'tanggal'          => $this->request->getPost('tanggal'),
            'volume_do'        => $volume_do,
            'volume_diterima'  => $volume_diterima,
            'selisih'          => $selisih,
            'status'           => $status,
            'harga_beli'       => $this->request->getPost('harga_beli'),
            'supir'            => $this->request->getPost('supir'),
            'nomor_do'         => $this->request->getPost('nomor_do'),
            'catatan'          => $this->request->getPost('catatan'),
        ]);

        return redirect()->to('/penerimaan')->with('success', 'Data berhasil diperbarui.');
    }

    public function delete($id)
    {
        $penerimaan = $this->penerimaanModel->find($id);
        if (!$penerimaan || !hasAccessToSPBU($penerimaan['kode_spbu'])) {
            return redirect()->to('/unauthorized');
        }

        $this->penerimaanModel->delete($id);
        return redirect()->to('/penerimaan')->with('success', 'Data berhasil dihapus.');
    }
    // Di Controller yang sudah ada (mungkin Penerimaan.php), tambahkan:
public function prosesDipping($id)
{
    $penerimaan = $this->penerimaanModel->find($id);
    
    if ($this->request->getMethod() === 'post') {
        $volumeDipping = $this->request->getPost('volume_dipping');
        
        // Validasi
        if ($volumeDipping <= 0) {
            return redirect()->back()->with('error', 'Volume dipping harus lebih dari 0');
        }
        
        $this->penerimaanModel->simpanDipping($id, $volumeDipping);
        return redirect()->to('/penerimaan')->with('success', 'Hasil dipping berhasil disimpan');
    }
    
    return view('penerimaan/dipping_form', [
        'penerimaan' => $penerimaan
    ]);
}
}