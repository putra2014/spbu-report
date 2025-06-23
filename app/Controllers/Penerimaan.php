<?php

namespace App\Controllers;

use App\Models\PenerimaanModel;
use App\Models\SpbuModel;
use App\Models\TangkiModel;
use App\Models\ProdukModel;

class Penerimaan extends BaseController
{
    protected $penerimaanModel, $spbuModel, $tangkiModel, $produkModel;

    public function __construct()
    {
        $this->penerimaanModel = new PenerimaanModel();
        $this->spbuModel = new SpbuModel();
        $this->tangkiModel = new TangkiModel();
        $this->produkModel = new ProdukModel();
        helper(['form', 'Access']);
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

        $volume_do = floatval($this->request->getPost('volume_do'));
        $volume_diterima = floatval($this->request->getPost('volume_diterima'));
        $selisih = $volume_diterima - $volume_do;
        $status = ($selisih == 0) ? 'Cocok' : (($selisih > 0) ? 'Lebih' : 'Kurang');

        $this->penerimaanModel->save([
            'kode_spbu'        => $kode_spbu,
            'tangki_id'        => $this->request->getPost('tangki_id'),
            'kode_produk'      => $this->request->getPost('nama _produk'),
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

        return redirect()->to('/penerimaan')->with('success', 'Data berhasil disimpan.');
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
}
