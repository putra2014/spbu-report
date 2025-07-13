<?php
namespace App\Controllers;

use App\Models\StokModel;
use App\Models\TangkiModel;
use App\Models\PenerimaanModel;
use App\Models\PenjualanModel;

class Stok extends BaseController
{
    protected $stokModel;
    protected $tangkiModel;
    protected $penerimaanModel;
    protected $penjualanModel;
    protected $db;

    public function __construct()
    {
        $this->stokModel = new StokModel();
        $this->tangkiModel = new TangkiModel();
        $this->penerimaanModel = new PenerimaanModel();
        $this->penjualanModel = new PenjualanModel();
        $this->db = \Config\Database::connect();
        helper('shift');
    }

        protected function _getStokAwalHariIni($tangki_id, $tanggal)
    {
        $lastStok = $this->stokModel
            ->where('tangki_id', $tangki_id)
            ->where('tanggal <', $tanggal)
            ->orderBy('tanggal', 'DESC')
            ->orderBy('shift', 'DESC')
            ->first();

        return $lastStok ? $lastStok['stok_real'] : 0;
    }
    

    // 1. METHOD INPUT STOK REAL (EXISTING)
    public function inputStokReal()
    {
        $kode_spbu = session()->get('kode_spbu');
        
        $data = [
            'title' => 'Input Stok Real',
            'tangki' => $this->tangkiModel->where('kode_spbu', $kode_spbu)->findAll(),
            'shift' => $this->_getCurrentShift(),
            'lastStok' => $this->_getLastStokPerTangki($kode_spbu)
        ];
        
        return view('stok/input_stok_real', $data);
    }

    // 2. METHOD SIMPAN STOK REAL (EXISTING)
    public function simpanStokReal()
    {
        $rules = [
            'tangki_id' => 'required|numeric',
            'stok_real' => 'required|decimal|greater_than_equal_to[0]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->db->transStart();
        try {
            $tangki_id = $this->request->getPost('tangki_id');
            $stok_real = $this->request->getPost('stok_real');

            // Get last record
            $lastStok = $this->stokModel
                ->where('tangki_id', $tangki_id)
                ->orderBy('tanggal', 'DESC')
                ->orderBy('shift', 'DESC')
                ->first();

            // Prepare data
            $data = [
                'tanggal' => date('Y-m-d'),
                'shift' => $this->_getCurrentShift(),
                'kode_spbu' => session()->get('kode_spbu'),
                'tangki_id' => $tangki_id,
                'stok_awal' => $lastStok ? $lastStok['stok_real'] : 0,
                'penerimaan' => 0,
                'penjualan' => 0,
                'stok_real' => $stok_real,
                'created_by' => session()->get('user_id'),
                'is_closing' => false
            ];

            // Simpan dan update
            $this->stokModel->insert($data);
            $this->tangkiModel->update($tangki_id, ['stok' => $stok_real]);

            $this->db->transComplete();
            return redirect()->to('/stok/laporan')->with('success', 'Data stok berhasil disimpan');
        } catch (\Exception $e) {
            $this->db->transRollback();
            return redirect()->back()->withInput()->with('error', 'Gagal menyimpan: '.$e->getMessage());
        }
    }

    // 3. METHOD CLOSING HARIAN (EXISTING)
// Di dalam Stok.php (Controller)
public function closingHarian()
{
    $kode_spbu = session()->get('kode_spbu');
    $tanggal = $this->request->getPost('tanggal') ?? date('Y-m-d');
    
    $this->db->transStart();
    
    try {
        // 1. Get semua tangki di SPBU
        $tangkiList = $this->tangkiModel->where('kode_spbu', $kode_spbu)->findAll();
        
        foreach ($tangkiList as $tangki) {
            // 2. Hitung total penerimaan dan penjualan per tangki
            $totalPenerimaan = $this->db->table('penerimaan')
                                      ->selectSum('volume_diterima', 'total')
                                      ->where('kode_spbu', $kode_spbu)
                                      ->where('tanggal', $tanggal)
                                      ->where('tangki_id', $tangki['id'])
                                      ->get()
                                      ->getRow()->total ?? 0;

            $totalPenjualan = $this->db->table('penjualan_harian ph')
                                     ->selectSum('ph.volume', 'total')
                                     ->join('nozzle n', 'n.id = ph.nozzle_id')
                                     ->where('ph.kode_spbu', $kode_spbu)
                                     ->where('DATE(ph.tanggal)', $tanggal)
                                     ->where('n.kode_tangki', $tangki['kode_tangki'])
                                     ->get()
                                     ->getRow()->total ?? 0;

            // 3. Get stok real terakhir
            $stokReal = $this->db->table('stok_bbm')
                               ->select('stok_real')
                               ->where('tangki_id', $tangki['id'])
                               ->where('tanggal', $tanggal)
                               ->orderBy('shift', 'DESC')
                               ->get()
                               ->getRow()->stok_real ?? 0;

            // 4. Buat record closing
            $this->stokModel->insert([
                'tanggal' => $tanggal,
                'shift' => '0', // 0 untuk closing harian
                'kode_spbu' => $kode_spbu,
                'tangki_id' => $tangki['id'],
                'stok_awal' => $this->_getStokAwalHariIni($tangki['id'], $tanggal),
                'penerimaan' => $totalPenerimaan,
                'penjualan' => $totalPenjualan,
                'stok_real' => $stokReal,
                'is_closing' => true,
                'created_by' => session()->get('user_id')
            ]);

            // 5. Update stok di tabel tangki
            $this->tangkiModel->update($tangki['id'], ['stok' => $stokReal]);
        }
        
        $this->db->transComplete();
        return redirect()->to('/stok/laporan')->with('success', 'Closing harian berhasil disimpan');
    } catch (\Exception $e) {
        $this->db->transRollback();
        return redirect()->back()->with('error', 'Gagal proses closing: ' . $e->getMessage());
    }
}

    // 4. METHOD LAPORAN (EXISTING)
    public function laporan()
    {
        $kode_spbu = session()->get('kode_spbu');
        $startDate = $this->request->getGet('start_date') ?? date('Y-m-01');
        $endDate = $this->request->getGet('end_date') ?? date('Y-m-d');
        $selectedTangki = $this->request->getGet('tangki_id');
        $groupByDay = $this->request->getGet('group_by_day');

        $builder = $this->stokModel
            ->select('stok_bbm.*, tangki.kode_tangki, produk_bbm.nama_produk')
            ->join('tangki', 'tangki.id = stok_bbm.tangki_id')
            ->join('produk_bbm', 'produk_bbm.kode_produk = tangki.kode_produk')
            ->where('stok_bbm.kode_spbu', $kode_spbu)
            ->where('stok_bbm.tanggal >=', $startDate)
            ->where('stok_bbm.tanggal <=', $endDate);

        if ($groupByDay) {
            $builder->where('stok_bbm.is_closing', true);
        }

        if ($selectedTangki) {
            $builder->where('stok_bbm.tangki_id', $selectedTangki);
        }

        $data = [
            'stok' => $builder->orderBy('stok_bbm.tanggal', 'DESC')
                             ->orderBy('stok_bbm.shift', 'DESC')
                             ->findAll(),
            'tangkiList' => $this->tangkiModel->getTangkiBySPBU($kode_spbu),
            'startDate' => $startDate,
            'endDate' => $endDate,
            'selectedTangki' => $selectedTangki,
            'groupByDay' => $groupByDay
        ];

        return view('stok/laporan', $data);
    }

    // 5. METHOD BARU: INPUT STOK PER SHIFT
    public function inputStokShift()
    {
        $kode_spbu = session()->get('kode_spbu');
        
        $data = [
            'title' => 'Input Stok per Shift',
            'tangki' => $this->tangkiModel->where('kode_spbu', $kode_spbu)->findAll(),
            'shift' => $this->_getCurrentShift(),
            'lastStok' => $this->_getLastStokPerTangki($kode_spbu)
        ];
        
        return view('stok/input_stok_shift', $data);
    }

    // 6. METHOD BARU: SIMPAN STOK PER SHIFT
    public function simpanStokShift()
    {
        $rules = [
            'tangki_id' => 'required|numeric',
            'stok_real' => 'required|decimal|greater_than_equal_to[0]',
            'shift' => 'required|in_list[1,2,3]',
            'penerimaan' => 'permit_empty|decimal',
            'penjualan' => 'permit_empty|decimal'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->db->transStart();
        try {
            $tangki_id = $this->request->getPost('tangki_id');
            $stok_real = $this->request->getPost('stok_real');
            $shift = $this->request->getPost('shift');
            $penerimaan = $this->request->getPost('penerimaan') ?? 0;
            $penjualan = $this->request->getPost('penjualan') ?? 0;

            $stokAwal = $this->_getStokAwalShift($tangki_id, date('Y-m-d'), $shift);

            $data = [
                'tanggal' => date('Y-m-d'),
                'shift' => $shift,
                'kode_spbu' => session()->get('kode_spbu'),
                'tangki_id' => $tangki_id,
                'stok_awal' => $stokAwal,
                'penerimaan' => $penerimaan,
                'penjualan' => $penjualan,
                'stok_real' => $stok_real,
                'created_by' => session()->get('user_id'),
                'is_closing' => false
            ];

            $this->stokModel->insert($data);
            $this->tangkiModel->update($tangki_id, ['stok' => $stok_real]);

            $this->db->transComplete();
            return redirect()->to('/stok/laporan')->with('success', 'Data stok shift berhasil disimpan');
        } catch (\Exception $e) {
            $this->db->transRollback();
            return redirect()->back()->withInput()->with('error', 'Gagal menyimpan: '.$e->getMessage());
        }
    }

    // HELPER METHOD YANG SUDAH ADA
    private function _getCurrentShift()
    {
        $hour = date('H');
        if ($hour >= 6 && $hour < 14) return '1';
        if ($hour >= 14 && $hour < 22) return '2';
        return '3';
    }

    private function _getLastStokPerTangki($kode_spbu)
    {
        $tangki = $this->tangkiModel->where('kode_spbu', $kode_spbu)->findAll();
        $lastStok = [];

        foreach ($tangki as $t) {
            $stokRecord = $this->stokModel
                ->where('tangki_id', $t['id'])
                ->orderBy('tanggal', 'DESC')
                ->orderBy('shift', 'DESC')
                ->first();

            $lastStok[$t['id']] = $stokRecord ? $stokRecord['stok_real'] : $t['stok'];
        }

        return $lastStok;
    }

    // HELPER METHOD BARU
    private function _getStokAwalShift($tangki_id, $tanggal, $shift)
    {
        if ($shift == '1') {
            $closingKemarin = $this->stokModel
                ->where('tangki_id', $tangki_id)
                ->where('tanggal <', $tanggal)
                ->where('is_closing', true)
                ->orderBy('tanggal', 'DESC')
                ->first();
            
            return $closingKemarin ? $closingKemarin['stok_real'] : 0;
        }
        
        $shiftSebelumnya = $this->stokModel
            ->where('tangki_id', $tangki_id)
            ->where('tanggal', $tanggal)
            ->where('shift', ($shift - 1))
            ->first();
            
        return $shiftSebelumnya ? $shiftSebelumnya['stok_real'] : 0;
    }
}