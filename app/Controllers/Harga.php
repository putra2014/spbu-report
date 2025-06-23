<?php

namespace App\Controllers;

use App\Models\HargaModel;
use App\Models\HargaLogModel;
use App\Models\ProdukModel;

class Harga extends BaseController
{
    protected $hargaModel;
    protected $logModel;
    protected $produkModel;

    public function __construct()
    {
        $this->hargaModel = new HargaModel();
        $this->logModel   = new HargaLogModel();
        $this->produkModel = new ProdukModel();
    }

    public function index()
    {
        $data['title'] = 'Harga BBM';
        $data['harga'] = $this->hargaModel
            ->select('harga_bbm.*, produk_bbm.nama_produk')
            ->join('produk_bbm', 'produk_bbm.kode_produk = harga_bbm.kode_produk')
            ->orderBy('produk_bbm.nama_produk', 'ASC')
            ->findAll();

        return view('harga/index', $data);
    }
    public function create()
    {
        $produkList = $this->produkModel->findAll();
        
        return view('harga/create', [
            'title' => 'Tambah Harga Produk BBM',

            'produkList' => $produkList
        ]);
    }

    public function store()
    {
        $this->hargaModel->save([
            'kode_produk'  => $this->request->getPost('kode_produk'),
            'harga_beli' => $this->request->getPost('harga_beli'),
            'harga_jual' => $this->request->getPost('harga_jual'),
        ]);

        return redirect()->to('/harga')->with('success', 'Harga awal disimpan.');
    }

    public function edit($id)
    {
        $harga = $this->hargaModel->find($id);
        $produk = $this->produkModel->find($harga['kode_produk']);

        return view('harga/edit', [
            'title' => 'Update Harga BBM',
            'harga' => $harga,
            'produk' => $produk
        ]);
    }

    public function update($id)
    {
        $hargaLama = $this->hargaModel->find($id);

        // Simpan log
        $this->logModel->save([
            'harga_bbm_id' => $id,
            'produk_id'    => $hargaLama['produk_id'],
            'harga_beli'   => $hargaLama['harga_beli'],
            'harga_jual'   => $hargaLama['harga_jual'],
        ]);

        // Update harga baru
        $this->hargaModel->save([
            'id' => $id,
            'harga_beli' => $this->request->getPost('harga_beli'),
            'harga_jual' => $this->request->getPost('harga_jual'),
        ]);

        return redirect()->to('/harga')->with('success', 'Harga diperbarui dan disimpan ke riwayat.');
    }
    public function log($produk_id)
    {
        $produk = $this->produkModel->find($produk_id);
        $log = $this->logModel->where('produk_id', $produk_id)
            ->orderBy('changed_at', 'DESC')->findAll();
    
        return view('harga/log', [
            'title' => 'Riwayat Harga: ' . $produk['nama'],
            'log' => $log,
            'produk' => $produk
        ]);
    }

    public function delete($id)
{
    $this->hargaModel->delete($id);
    return redirect()->to('/harga')->with('success', 'Data harga dihapus.');
}
}
