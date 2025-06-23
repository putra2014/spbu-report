<?php

namespace App\Controllers;

use App\Models\SpbuModel;
use App\Models\ProvinsiModel;
use App\Models\KabupatenModel;
use App\Models\WilayahModel;
use App\Models\AreaModel;
use App\Models\JenisSpbuModel;
use App\Models\TypeSpbuModel;
use App\Models\KelasSpbuModel;

class Spbu extends BaseController
{
    protected $spbuModel, $provinsiModel, $kabupatenModel, $wilayahModel, 
              $areaModel, $jenisSpbuModel, $typeSpbuModel, $kelasSpbuModel;

    public function __construct()
    {
        $this->spbuModel = new SpbuModel();
        $this->jenisSpbuModel = new JenisSpbuModel();
        $this->typeSpbuModel = new TypeSpbuModel();
        $this->kelasSpbuModel = new KelasSpbuModel();
        $this->provinsiModel = new ProvinsiModel();
        $this->kabupatenModel = new KabupatenModel();
        $this->wilayahModel = new WilayahModel();
        $this->areaModel = new AreaModel();
        helper('Access');
    }

    public function index()
    {
        // Get filter parameters from GET request
        $search = $this->request->getGet('search');
        $wilayah = $this->request->getGet('wilayah');
        $area = $this->request->getGet('area');
        
        // Initialize the query builder
        $builder = $this->spbuModel
            ->select('spbu.*, wilayah.nama_wilayah, area.nama_area')
            ->join('wilayah', 'wilayah.id = spbu.wilayah_id', 'left')
            ->join('area', 'area.id = spbu.area_id', 'left');
        
        // Apply role-based restrictions
        $role = session()->get('role');
        $kode_spbu = session()->get('kode_spbu');
        
        if ($role === 'admin_spbu') {
            $builder->where('spbu.kode_spbu', $kode_spbu);
        } elseif ($role === 'admin_area') {
            $area_id = session()->get('area_id');
            $builder->where('spbu.area_id', $area_id);
        }
        
        // Apply search filter
        if (!empty($search)) {
            $builder->like('spbu.kode_spbu', $search);
        }
        
        // Apply wilayah filter
        if (!empty($wilayah)) {
            $builder->where('spbu.wilayah_id', $wilayah);
        }
        
        // Apply area filter
        if (!empty($area)) {
            $builder->where('spbu.area_id', $area);
        }
        
        // Configure pagination
        $perPage = 10;
        $currentPage = (int)($this->request->getGet('page') ?? 1);
        
        // Get paginated results
        $data = [
            'spbu' => $builder->paginate($perPage, 'default', $currentPage),
            'pager' => $this->spbuModel->pager,
            'currentPage' => $currentPage,
            'wilayahList' => $this->wilayahModel->findAll(),
            'areaList' => $this->areaModel->findAll(),
            'search' => $search,
            'filterWilayah' => $wilayah,
            'filterArea' => $area,
            'title' => 'Data SPBU'
        ];
        
        return view('spbu/index', $data);
    }

    // ... [rest of your existing methods remain unchanged] ...

    public function create()
    {
        if (!in_array(session()->get('role'), ['admin_region', 'admin_area'])) {
            return redirect()->to('/unauthorized');
        }
        
        return view('spbu/create', [
            'title' => 'Tambah SPBU',
            'provinsiList' => $this->provinsiModel->findAll(),
            'kabupatenList' => [],
            'wilayahList' => $this->wilayahModel->findAll(),
            'areaList' => $this->areaModel->findAll(),
            'jenisSpbuList' => $this->jenisSpbuModel->findAll(),
            'typeSpbuList'  => $this->typeSpbuModel->findAll(),
            'kelasSpbuList' => $this->kelasSpbuModel->findAll()
        ]);
    }
    
    public function store()
    {
        if (!in_array(session()->get('role'), ['admin_region', 'admin_area'])) {
            return redirect()->to('/unauthorized');
        }

        // Validasi data
        

        $data = [
            'kode_spbu'        => $this->request->getPost('kode_spbu'),
            'nama_spbu'        => $this->request->getPost('nama_spbu'),
            'alamat_spbu'      => $this->request->getPost('alamat_spbu'),
            'provinsi_id'      => $this->request->getPost('provinsi_id'),
            'kabupaten_id'     => $this->request->getPost('kabupaten_id'),
            'nama_perusahaan'  => $this->request->getPost('nama_perusahaan'),
            'telp_spbu'        => $this->request->getPost('telp_spbu'),
            'nama_direktur'    => $this->request->getPost('nama_direktur'),
            'telp_direktur'    => $this->request->getPost('telp_direktur'),
            'nama_manager'     => $this->request->getPost('nama_manager'),
            'telp_manager'     => $this->request->getPost('telp_manager'),
            'jenis_spbu_id'    => $this->request->getPost('jenis_spbu_id'),
            'type_spbu_id'     => $this->request->getPost('type_spbu_id'),
            'kelas_spbu_id'    => $this->request->getPost('kelas_spbu_id'),
            'sold_to_party'    => $this->request->getPost('sold_to_party'),
            'ship_to_party'    => $this->request->getPost('ship_to_party'),
            'wilayah_id'       => $this->request->getPost('wilayah_id'),
            'area_id'          => $this->request->getPost('area_id'),
            'jumlah_tangki'    => $this->request->getPost('jumlah_tangki'),
            'jumlah_dispenser' => $this->request->getPost('jumlah_dispenser'),
            'latitude'         => $this->request->getPost('latitude'),
            'longitude'        => $this->request->getPost('longitude'),
            'keterangan'      => $this->request->getPost('keterangan'),
        ];

        $this->spbuModel->save($data);

        return redirect()->to('/spbu')->with('success', 'SPBU berhasil ditambahkan.');
    }


    public function edit($kode_spbu)
    {
        $spbu = $this->spbuModel->where('kode_spbu', $kode_spbu)->first();

        if (!$spbu || !hasAccessToSPBU($kode_spbu)) {
            return redirect()->to('/unauthorized');
        }

        $kabupatenList = $this->kabupatenModel
            ->where('provinsi_id', $spbu['provinsi_id'])
            ->findAll();

        return view('spbu/edit', [
            'title' => 'Edit SPBU',
            'spbu' => $spbu,
            'provinsiList' => $this->provinsiModel->findAll(),
            'kabupatenList' => $kabupatenList,
            'wilayahList' => $this->wilayahModel->findAll(),
            'areaList' => $this->areaModel->findAll(),
            'jenisSpbuList' => $this->jenisSpbuModel->findAll(),
            'typeSpbuList'  => $this->typeSpbuModel->findAll(),
            'kelasSpbuList' => $this->kelasSpbuModel->findAll()
        ]);
    }

public function update($kode_spbu)
    {
        $spbu = $this->spbuModel->where('kode_spbu', $kode_spbu)->first();
        if (!$spbu || !hasAccessToSPBU($kode_spbu)) {
            return redirect()->to('/unauthorized');
        }

        // Validasi data


        $data = [
            'nama_spbu'        => $this->request->getPost('nama_spbu'),
            'alamat_spbu'      => $this->request->getPost('alamat_spbu'),
            'provinsi_id'      => $this->request->getPost('provinsi_id'),
            'kabupaten_id'     => $this->request->getPost('kabupaten_id'),
            'nama_perusahaan'  => $this->request->getPost('nama_perusahaan'),
            'telp_spbu'        => $this->request->getPost('telp_spbu'),
            'nama_direktur'    => $this->request->getPost('nama_direktur'),
            'telp_direktur'    => $this->request->getPost('telp_direktur'),
            'nama_manager'     => $this->request->getPost('nama_manager'),
            'telp_manager'     => $this->request->getPost('telp_manager'),
            'jenis_spbu_id'    => $this->request->getPost('jenis_spbu_id'),
            'type_spbu_id'     => $this->request->getPost('type_spbu_id'),
            'kelas_spbu_id'    => $this->request->getPost('kelas_spbu_id'),
            'sold_to_party'    => $this->request->getPost('sold_to_party'),
            'ship_to_party'    => $this->request->getPost('ship_to_party'),
            'wilayah_id'       => $this->request->getPost('wilayah_id'),
            'area_id'          => $this->request->getPost('area_id'),
            'jumlah_tangki'    => $this->request->getPost('jumlah_tangki'),
            'jumlah_dispenser' => $this->request->getPost('jumlah_dispenser'),
            'latitude'         => $this->request->getPost('latitude'),
            'longitude'        => $this->request->getPost('longitude'),
            'keterangan'       => $this->request->getPost('keterangan'),
        ];
        

        if (
            $this->request->getPost('nama_spbu') &&
            $this->request->getPost('provinsi_id') &&
            $this->request->getPost('kabupaten_id')
        ) {
            session()->remove('spbu_incomplete');
        }
                $this->spbuModel->update($spbu['id'], $data);

        return redirect()->to('/spbu')->with('success', 'Data SPBU berhasil diperbarui.');
    }


    public function detail($kode_spbu)
    {
        $spbu = $this->spbuModel
            ->select('spbu.*, wilayah.nama_wilayah, area.nama_area, provinsi.nama, kabupaten.nama_kabupaten, type_spbu.nama_type, jenis_spbu.nama_jenis, kelas_spbu.nama_kelas')
            ->join('wilayah', 'wilayah.id = spbu.wilayah_id', 'left')
            ->join('area', 'area.id = spbu.area_id', 'left')
            ->join('provinsi', 'provinsi.id = spbu.provinsi_id', 'left')
            ->join('kabupaten', 'kabupaten.id = spbu.kabupaten_id', 'left')
            ->join('jenis_spbu', 'jenis_spbu.id = spbu.jenis_spbu_id', 'left')
            ->join('type_spbu', 'type_spbu.id = spbu.type_spbu_id', 'left')
            ->join('kelas_spbu', 'kelas_spbu.id = spbu.kelas_spbu_id', 'left') 
            ->where('kode_spbu', $kode_spbu)
            ->first();

        if (!$spbu || !hasAccessToSPBU($kode_spbu)) {
            return redirect()->to('/unauthorized');
        }

        return view('spbu/detail', ['title' => 'Detail SPBU', 'spbu' => $spbu]);
    }

}