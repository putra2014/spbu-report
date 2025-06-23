<?php

namespace App\Controllers;

use App\Models\SpbuModel;
use App\Models\ProvinsiModel;
use App\Models\KabupatenModel;
use App\Models\WilayahModel;
use App\Models\AreaModel;

class Spbu extends BaseController
{
    protected $spbuModel, $provinsiModel, $kabupatenModel, $wilayahModel, $areaModel;

    public function __construct()
    {
        $this->spbuModel     = new SpbuModel();
        $this->provinsiModel = new ProvinsiModel();
        $this->kabupatenModel = new KabupatenModel();
        $this->wilayahModel  = new WilayahModel();
        $this->areaModel     = new AreaModel();
        helper('Access');
    }

    public function index()
    {
        $role = session()->get('role');
        $kode_spbu = session()->get('kode_spbu');

        if ($role === 'admin_spbu') {
            $spbu = $this->spbuModel->where('kode_spbu', $kode_spbu)->first();
            $data['spbu'] = $spbu ? [$spbu] : [];
        } elseif ($role === 'admin_area') {
            $area_id = session()->get('area_id');
            $data['spbu'] = $this->spbuModel->where('area_id', $area_id)->findAll();
        } else {
            $data['spbu'] = $this->spbuModel->findAll();
        }

        return view('spbu/index', ['title' => 'Data SPBU'] + $data);
    }

    public function edit($kode_spbu)
    {
        $spbu = $this->spbuModel->where('kode_spbu', $kode_spbu)->first();
        if (!$spbu || !hasAccessToSPBU($kode_spbu)) {
            return redirect()->to('/unauthorized');
        }

        return view('spbu/edit', [
            'title' => 'Edit SPBU',
            'spbu' => $spbu,
            'provinsiList' => $this->provinsiModel->findAll(),
            'kabupatenList' => $this->kabupatenModel->where('provinsi_id', $spbu['provinsi_id'])->findAll(),
            'wilayahList' => $this->wilayahModel->findAll(),
            'areaList' => $this->areaModel->findAll()
        ]);
    }

    public function update($kode_spbu)
    {
        $spbu = $this->spbuModel->where('kode_spbu', $kode_spbu)->first();
        if (!$spbu || !hasAccessToSPBU($kode_spbu)) {
            return redirect()->to('/unauthorized');
        }

        $this->spbuModel->update($spbu['id'], [
            'nama_spbu' => $this->request->getPost('nama_spbu'),
            'provinsi_id' => $this->request->getPost('provinsi_id'),
            'kabupaten_id' => $this->request->getPost('kabupaten_id'),
            'alamat_spbu' => $this->request->getPost('alamat_spbu'),
            'no_telp'        => $this->request->getPost('no_telp'),
            'nama_pemilik'   => $this->request->getPost('nama_pemilik'),
            'telp_pemilik'   => $this->request->getPost('telp_pemilik'),
            'nama_manager'   => $this->request->getPost('nama_manager'),
            'telp_manager'   => $this->request->getPost('telp_manager'),
            'sold_to_party'  => $this->request->getPost('sold_to_party'),
            'ship_to_party'  => $this->request->getPost('ship_to_party'),
            'wilayah_id'     => $this->request->getPost('wilayah_id'),
            'area_id'        => $this->request->getPost('area_id'),
            'jumlah_tangki'  => $this->request->getPost('jumlah_tangki'),
            'jumlah_dispenser' => $this->request->getPost('jumlah_dispenser'),
            'latitude'       => $this->request->getPost('latitude'),
            'longitude'      => $this->request->getPost('longitude'),
            'keterangan'     => $this->request->getPost('keterangan'),
        ]);

        // Reset incomplete status jika sudah lengkap
        if (
            $this->request->getPost('nama_spbu') &&
            $this->request->getPost('provinsi_id') &&
            $this->request->getPost('kabupaten_id')
        ) {
            session()->remove('spbu_incomplete');
        }

        return redirect()->to('/spbu')->with('success', 'Data SPBU diperbarui.');
    }
}
