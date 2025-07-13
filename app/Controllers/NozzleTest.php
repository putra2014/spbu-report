<?php
namespace App\Controllers;

use App\Models\NozzleTestModel;
use App\Models\PenjualanModel;
use App\Models\NozzleModel;
use App\Models\TangkiModel;
use App\Models\DispenserModel;

class NozzleTest extends BaseController
{
    protected $nozzleTestModel;
    protected $penjualanModel;
    protected $nozzleModel;
    protected $tangkiModel;
    protected $dispenserModel;

    public function __construct()
    {
        $this->nozzleTestModel = new NozzleTestModel();
        $this->penjualanModel = new PenjualanModel();
        $this->nozzleModel = new NozzleModel();
        $this->tangkiModel = new TangkiModel();
        $this->dispenserModel = new DispenserModel();
    }

    public function index()
    {
        $data['tests'] = $this->nozzleTestModel
            ->select('nozzle_tests.*, nozzle.kode_nozzle, produk_bbm.nama_produk')
            ->join('nozzle', 'nozzle.id = nozzle_tests.nozzle_id', 'left')
            ->join('produk_bbm', 'produk_bbm.kode_produk = nozzle.kode_produk', 'left')
            ->where('nozzle_tests.kode_spbu', session()->get('kode_spbu'))
            ->orderBy('nozzle_tests.tanggal DESC, nozzle_tests.shift DESC')
            ->findAll();
    
        return view('nozzle_test/index', $data);
    }

    public function create()
    {
        $kode_spbu = session()->get('kode_spbu');

        $data = [
            'title' => 'Tambah Test Nozzle',
            'dispensers' => $this->dispenserModel
                ->where('kode_spbu', $kode_spbu)
                ->findAll(),
            'penjualan' => []
        ];

        if ($dispenserId = $this->request->getGet('dispenser_id')) {
            $data['penjualan'] = $this->penjualanModel
                ->select('penjualan_harian.*, nozzle.kode_nozzle, nozzle.dispenser_id, produk_bbm.nama_produk, dispenser.kode_dispenser')
                ->join('nozzle', 'nozzle.id = penjualan_harian.nozzle_id')
                ->join('dispenser', 'dispenser.id = nozzle.dispenser_id')
                ->join('produk_bbm', 'produk_bbm.kode_produk = nozzle.kode_produk')
                ->where('penjualan_harian.kode_spbu', $kode_spbu)
                ->where('dispenser.id', $dispenserId)
                ->orderBy('penjualan_harian.tanggal DESC, penjualan_harian.shift DESC')
                ->findAll();
        }

        return view('nozzle_test/create', $data);
    }



    public function store()
    {
        $rules = [
            'penjualan_id' => 'required|numeric',
            'nozzle_id' => 'required|numeric',
            'volume_test' => 'required|decimal|greater_than[0]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $db = \Config\Database::connect();
        $db->transStart();

        try {
            // Data penjualan
            $penjualan = $this->penjualanModel->find($this->request->getPost('penjualan_id'));

            // Simpan data test
            $this->nozzleTestModel->save([
                'penjualan_id' => $penjualan['id'],
                'kode_spbu' => $penjualan['kode_spbu'],
                'nozzle_id' => $this->request->getPost('nozzle_id'),
                'shift' => $penjualan['shift'],
                'tanggal' => $penjualan['tanggal'],
                'volume_penjualan' => $penjualan['meter_akhir'] - $penjualan['meter_awal'],
                'volume_test' => $this->request->getPost('volume_test'),
                'created_by' => session()->get('user_id')
            ]);

            $db->transComplete();

            return redirect()->to(base_url('nozzle-test'))->with('success', 'Data test nozzle berhasil disimpan');
        } catch (\Exception $e) {
            $db->transRollback();
            return redirect()->back()->withInput()->with('error', 'Gagal menyimpan: '.$e->getMessage());
        }
    }
    public function getNozzles()
    {
        $dispenser_id = $this->request->getGet('dispenser_id');

        if (!$dispenser_id) {
            return $this->response->setJSON([]);
        }

        $nozzles = $this->nozzleModel
            ->select('id, kode_nozzle, kode_produk')
            ->where('dispenser_id', $dispenser_id)
            ->where('status', 'Aktif')
            ->findAll();

        // Pastikan format response benar
        return $this->response->setJSON($nozzles ?? []);
    }
    
    public function getPenjualan()
    {
        $tanggal = $this->request->getGet('tanggal');
        $dispenserId = $this->request->getGet('dispenser_id');
        $nozzleId = $this->request->getGet('nozzle_id');

        // Validasi parameter
        if (!$tanggal || !$dispenserId || !$nozzleId) {
            return $this->response->setJSON([]);
        }

        // Query yang lebih robust
        $result = $this->penjualanModel
            ->select('penjualan_harian.id, penjualan_harian.tanggal, penjualan_harian.shift, 
                     penjualan_harian.meter_awal, penjualan_harian.meter_akhir,
                     nozzle.kode_nozzle, nozzle.id as nozzle_id,
                     dispenser.kode_dispenser, dispenser.id as dispenser_id')
            ->join('nozzle', 'nozzle.id = penjualan_harian.nozzle_id')
            ->join('dispenser', 'dispenser.id = nozzle.dispenser_id')
            ->where('penjualan_harian.kode_spbu', session()->get('kode_spbu'))
            ->where('DATE(penjualan_harian.tanggal)', date('Y-m-d', strtotime($tanggal)))
            ->where('nozzle.dispenser_id', $dispenserId)
            ->where('penjualan_harian.nozzle_id', $nozzleId)
            ->orderBy('penjualan_harian.shift', 'DESC')
            ->findAll();

        return $this->response->setJSON($result);
    }
}