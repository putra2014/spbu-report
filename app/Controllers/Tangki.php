<?php
namespace App\Controllers;

use App\Models\TangkiModel;
use App\Models\SpbuModel;
use App\Models\ProdukModel;

class Tangki extends BaseController
{
    protected $tangkiModel;
    protected $spbuModel;
    protected $produkModel;

    public function __construct()
    {
        $this->tangkiModel = new TangkiModel();
        $this->spbuModel = new SpbuModel();
        $this->produkModel = new ProdukModel();
        helper('Access');
    }

    public function index()
    {
        $role = session()->get('role');
        $kode_spbu = session()->get('kode_spbu');

        if ($role === 'admin_spbu') {
            $data['tangki'] = $this->tangkiModel
                ->select('tangki.*, produk_bbm.nama_produk as nama_produk')
                ->join('produk_bbm', 'produk_bbm.kode_produk = tangki.kode_produk', 'left')
                ->where('kode_spbu', $kode_spbu)
                ->findAll();
        } else {
            $data['tangki'] = $this->tangkiModel
                ->select('tangki.*, produk_bbm.nama_produk as nama_produk')
                ->join('produk_bbm', 'produk_bbm.kode_produk = tangki.kode_produk', 'left')
                ->findAll();
        }

        return view('tangki/index', ['title' => 'Data Tangki BBM'] + $data);
    }


    public function create()
    {
        $role = session()->get('role');
        $kode_spbu = session()->get('kode_spbu');

        $data['produkList'] = $this->produkModel->findAll();
        $data['title'] = 'Tambah Tangki';

        if ($role === 'admin_spbu') {
            $data['spbuList'] = [['kode_spbu' => $kode_spbu, 'nama_spbu' => 'SPBU ' . $kode_spbu]];
        } else {
            $data['spbuList'] = $this->spbuModel->findAll();
        }

        return view('tangki/create', $data);
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

        $kode_tangki = $this->request->getPost('kode_tangki');

        $existing = $this->tangkiModel
            ->where('kode_spbu', $kode_spbu)
            ->where('kode_tangki', $kode_tangki)
            ->first();

        if ($existing) {
            return redirect()->back()->withInput()->with('warning', 'Kode tangki sudah digunakan pada SPBU ini.');
        }

        $this->tangkiModel->save([
            'kode_tangki' => $kode_tangki,
            'jenis_tangki' => $this->request->getPost('jenis_tangki'),
            'kode_produk' => $this->request->getPost('kode_produk'),
            'kapasitas' => $this->request->getPost('kapasitas'),
            'dead_stock' => $this->request->getPost('dead_stock'),
            'kode_spbu' => $kode_spbu
        ]);

        return redirect()->to('/tangki')->with('success', 'Tangki berhasil disimpan.');
    }

    public function edit($id)
    {
        $tangki = $this->tangkiModel->find($id);
        if (!$tangki || !hasAccessToSPBU($tangki['kode_spbu'])) {
            return redirect()->to('/unauthorized');
        }

        $data['tangki'] = $tangki;
        $data['produkList'] = $this->produkModel->findAll();
        $data['title'] = 'Edit Tangki';

        if (session()->get('role') === 'admin_spbu') {
            $data['spbuList'] = [['kode_spbu' => $tangki['kode_spbu'], 'nama_spbu' => 'SPBU ' . $tangki['kode_spbu']]];
        } else {
            $data['spbuList'] = $this->spbuModel->findAll();
        }

        return view('tangki/edit', $data);
    }

    public function update($id)
    {
        $tangki = $this->tangkiModel->find($id);
        if (!$tangki || !hasAccessToSPBU($tangki['kode_spbu'])) {
            return redirect()->to('/unauthorized');
        }

        $kode_tangki = $this->request->getPost('kode_tangki');

        $duplicate = $this->tangkiModel
            ->where('kode_spbu', $tangki['kode_spbu'])
            ->where('kode_tangki', $kode_tangki)
            ->where('id !=', $id)
            ->first();

        if ($duplicate) {
            return redirect()->back()->withInput()->with('warning', 'Kode tangki sudah dipakai di SPBU ini.');
        }

        $this->tangkiModel->save([
            'id' => $id,
            'kode_tangki' => $kode_tangki,
            'jenis_tangki' => $this->request->getPost('jenis_tangki'),
            'kode_produk' => $this->request->getPost('kode_produk'),
            'kapasitas' => $this->request->getPost('kapasitas'),
            'dead_stock' => $this->request->getPost('dead_stock'),
            'kode_spbu' => $tangki['kode_spbu']
        ]);

        return redirect()->to('/tangki')->with('success', 'Tangki diperbarui.');
    }

    public function delete($id)
    {
        $tangki = $this->tangkiModel->find($id);
        if (!$tangki || !hasAccessToSPBU($tangki['kode_spbu'])) {
            return redirect()->to('/unauthorized');
        }

        $this->tangkiModel->delete($id);
        return redirect()->to('/tangki')->with('success', 'Tangki dihapus.');
    }
}
