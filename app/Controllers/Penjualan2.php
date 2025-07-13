<?php

namespace App\Controllers;

use App\Models\PenjualanModel;
use App\Models\NozzleModel;
use App\Models\OperatorModel;
use App\Models\HargaModel;
use App\Models\PenjualanLogModel;
use App\Models\MeterResetRequestModel;
use App\Models\ProdukModel;
use App\Models\TangkiModel;
use App\Models\SpbuModel;
use App\Models\DispenserModel;

class Penjualan extends BaseController
{
    protected $penjualanModel;
    protected $nozzleModel;
    protected $operatorModel;
    protected $hargaModel;
    protected $logModel;
    protected $resetModel;
    protected $produkModel;
    protected $tangkiModel;
    protected $spbuModel;
    protected $dispenserModel;
    protected $db;

    public function __construct()
    {
        $this->penjualanModel = new PenjualanModel();
        $this->nozzleModel = new NozzleModel();
        $this->operatorModel = new OperatorModel();
        $this->hargaModel = new HargaModel();
        $this->logModel = new PenjualanLogModel();
        $this->resetModel = new MeterResetRequestModel();
        $this->produkModel = new ProdukModel();
        $this->tangkiModel = new TangkiModel();
        $this->dispenserModel = new DispenserModel();
        $this->spbuModel = new SpbuModel();
        $this->db = \Config\Database::connect();
        helper('Access');
    }

    public function index()
    {
        $role = session()->get('role');
        $kode_spbu = session()->get('kode_spbu');

        if ($role === 'admin_spbu') {
            $data['penjualan'] = $this->penjualanModel
                ->select('penjualan_harian.*, nozzle.kode_nozzle, produk_bbm.nama_produk, operator.nama_operator')
                ->join('nozzle', 'nozzle.id = penjualan_harian.nozzle_id')
                ->join('produk_bbm', 'produk_bbm.kode_produk = nozzle.kode_produk', 'left')
                ->join('operator', 'operator.id = penjualan_harian.operator_id', 'left')
                ->orderBy('tanggal DESC, shift ASC')
                ->where('penjualan_harian.kode_spbu', $kode_spbu)
                ->findAll();
        } else {
            $data['penjualan'] = $this->penjualanModel
                ->select('penjualan_harian.*, nozzle.kode_nozzle, produk_bbm.nama_produk, operator.nama_operator')
                ->join('nozzle', 'nozzle.id = penjualan_harian.nozzle_id')
                ->join('produk_bbm', 'produk_bbm.kode_produk = nozzle.kode_produk', 'left')
                ->join('operator', 'operator.id = penjualan_harian.operator_id', 'left')
                ->orderBy('tanggal DESC, shift ASC')
                ->findAll();
        }

        return view('penjualan/index', $data);
    }

    public function create()
    {
        if (!hasAccessToSPBU(session()->get('kode_spbu'))) {
            return redirect()->to('/unauthorized');
        }

        $kode_spbu = session()->get('kode_spbu');

        if (session()->get('role') === 'admin_region') {
            $spbuModel = new SpbuModel();
            $data['spbuList'] = $spbuModel->findAll();
        }

        try {
            // Data dispenser dengan nozzle terkait
            $dispensers = $this->dispenserModel
                ->where('kode_spbu', $kode_spbu)
                ->findAll();

            // Data nozzle untuk debug (jika diperlukan)
            $debugNozzle = $this->nozzleModel
                ->where('kode_spbu', $kode_spbu)
                ->where('status', 'Aktif')
                ->first();

            $debugData = [];
            if ($debugNozzle) {
                $debugData = [
                    'nozzle_current' => $debugNozzle['current_meter'] ?? 0,
                    'last_sale_today' => $this->penjualanModel
                        ->where('nozzle_id', $debugNozzle['id'])
                        ->where('kode_spbu', $kode_spbu)
                        ->where('DATE(tanggal)', date('Y-m-d'))
                        ->orderBy('shift', 'DESC')
                        ->first(),
                    'last_reset' => $this->resetModel
                        ->where('nozzle_id', $debugNozzle['id'])
                        ->where('status', 'approved')
                        ->orderBy('approved_at', 'DESC')
                        ->first()
                ];
                log_message('debug', 'Meter Debug: ' . print_r($debugData, true));
            }

            // Siapkan data untuk view
            $data = array_merge($data, [
                'operatorList' => $this->operatorModel->where('kode_spbu', $kode_spbu)->findAll(),
                'dispensers' => $dispensers,
                'showResetButton' => $this->_shouldShowResetButton($kode_spbu),
                'lastMeters' => $this->_getLastMeters($kode_spbu),
                'disable_meter_awal' => true,
                'debugData' => $debugData
            ]);

            return view('penjualan/create', $data);
        } catch (\Exception $e) {
            log_message('error', 'Error in Penjualan/create: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan sistem: ' . $e->getMessage());
        }
    }

    public function store()
    {
        if (!hasAccessToSPBU(session()->get('kode_spbu'))) {
            return redirect()->to('/unauthorized');
        }
        $nozzle_id = $this->request->getPost('nozzle_id');
        $nozzle = $this->nozzleModel->find($nozzle_id);

        if ($nozzle['is_locked']) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Nozzle ini terkunci: ' . $nozzle['lock_reason']);
        }

        log_message('debug', 'Data yang diterima: ' . print_r($this->request->getPost(), true));

        // Validasi baru
        $validation = \Config\Services::validation();
        $validation->setRules([
            'meter_akhir' => [
                'rules' => 'required|decimal',
                'errors' => [
                    'decimal' => 'Meter akhir harus berupa angka desimal'
                ]
            ],
            'meter_awal' => [
                'rules' => 'required|decimal',
                'errors' => [
                    'decimal' => 'Meter awal harus berupa angka desimal'
                ]
            ]
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        // Validasi manual perbandingan nilai
        $meter_awal = (float)$this->request->getPost('meter_awal');
        $meter_akhir = (float)$this->request->getPost('meter_akhir');

        if ($meter_akhir <= $meter_awal) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Meter akhir harus lebih besar dari meter awal (Current: ' . $meter_awal . ')');
        }

        $tanggalInput = $this->request->getPost('tanggal');
        $lastMeter = $this->_getLastMeter(
            session()->get('kode_spbu'),
            $this->request->getPost('nozzle_id'),
            $tanggalInput
        );

        $db = \Config\Database::connect();
        $db->transStart();

        try {
            $kode_spbu = session()->get('kode_spbu');
            $tanggalInput = $this->request->getPost('tanggal');
            $tanggal = date('Y-m-d', strtotime($tanggalInput));
            $shift = $this->request->getPost('shift');
            $nozzle_id = $this->request->getPost('nozzle_id');
            $meter_awal = (float) $this->request->getPost('meter_awal');
            $meter_akhir = (float) $this->request->getPost('meter_akhir');
            $operator_id = $this->request->getPost('operator_id');

            $existing = $this->penjualanModel
                ->where('kode_spbu', $kode_spbu)
                ->where('DATE(tanggal)', $tanggal)
                ->where('shift', $shift)
                ->where('nozzle_id', $nozzle_id)
                ->first();

            if ($existing) {
                $nozzle = $this->nozzleModel->find($nozzle_id);
                $errorMsg = "Data penjualan sudah ada untuk:<br>";
                $errorMsg .= "- Tanggal: " . date('d/m/Y', strtotime($tanggal)) . "<br>";
                $errorMsg .= "- Shift: $shift<br>";
                $errorMsg .= "- Nozzle: " . ($nozzle ? $nozzle['kode_nozzle'] : 'Unknown') . "<br>";
                $errorMsg .= "Meter akhir: " . number_format($existing['meter_akhir'], 2) . " L";

                throw new \RuntimeException($errorMsg);
            }

            $lastMeter = $this->_getLastMeter($kode_spbu, $nozzle_id);
            if ($meter_awal < $lastMeter) {
                throw new \RuntimeException("Meter awal tidak boleh lebih kecil dari $lastMeter");
            }

            $nozzle = $this->nozzleModel->find($nozzle_id);
            if (!$nozzle || empty($nozzle['kode_tangki'])) {
                throw new \RuntimeException('Konfigurasi nozzle tidak valid: Tangki tidak terdefinisi');
            }

            $tangki = $this->tangkiModel->where('kode_spbu', $kode_spbu)
                ->where('kode_tangki', $nozzle['kode_tangki'])
                ->first();

            if (!$tangki || empty($tangki['kode_produk'])) {
                throw new \RuntimeException('Konfigurasi tangki tidak valid: Produk tidak terdefinisi');
            }

            $harga = $this->hargaModel
                ->where('kode_produk', $tangki['kode_produk'])
                ->orderBy('created_at', 'DESC')
                ->first();

            if (!$harga) {
                $produk = $this->produkModel->where('kode_produk', $tangki['kode_produk'])->first();
                $nama_produk = $produk ? $produk['nama_produk'] : $tangki['kode_produk'];
                throw new \RuntimeException("Harga untuk $nama_produk belum ditentukan. Silakan atur harga terlebih dahulu.");
            }

            $volume = $meter_akhir - $meter_awal;
            $harga_jual = $harga['harga_jual'];
            $total_penjualan = $volume * $harga_jual;

            $penjualanData = [
                'kode_spbu' => $kode_spbu,
                'tanggal' => $tanggal,
                'shift' => $shift,
                'nozzle_id' => $nozzle_id,
                'meter_awal' => $meter_awal,
                'meter_akhir' => $meter_akhir,
                'volume' => $volume,
                'harga_jual' => $harga_jual,
                'total_penjualan' => $total_penjualan,
                'operator_id' => $operator_id,
                'approval_status' => 'approved',
                'approved_at' => date('Y-m-d H:i:s'), // Tambahkan ini
                'approved_by' => session()->get('user_id') // Tambahkan ini
            ];

            if (!$this->penjualanModel->save($penjualanData)) {
                throw new \RuntimeException('Gagal menyimpan penjualan: ' . implode(', ', $this->penjualanModel->errors()));
            }

            $this->logModel->save($penjualanData);
            $this->nozzleModel->update($nozzle_id, ['current_meter' => $meter_akhir]);

            $db->transComplete();
            return redirect()->to('/penjualan')->with('success', 'Data berhasil disimpan');
        } catch (\Exception $e) {
            $db->transRollback();
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function handleReset()
    {
        // Debug 1: Cek data input
        log_message('debug', 'Data Input: ' . print_r($this->request->getPost(), true));

        try {
            $nozzle_id = $this->request->getPost('nozzle_id');

            // Debug 2: Cek status nozzle sebelum dikunci
            $nozzleBefore = $this->nozzleModel->find($nozzle_id);
            log_message('debug', 'Nozzle Before Lock: ' . print_r($nozzleBefore, true));

            // Kunci nozzle
            $lockResult = $this->nozzleModel->update($nozzle_id, [
                'is_locked' => 1,
                'lock_reason' => 'Menunggu approval reset (' . $this->request->getPost('reset_type') . ')'
            ]);

            // Debug 3: Cek hasil kunci
            log_message('debug', 'Nozzle Lock Result: ' . var_export($lockResult, true));
            log_message('debug', 'Nozzle After Lock: ' . print_r($this->nozzleModel->find($nozzle_id), true));

            // Proses upload file
            $file = $this->request->getFile('bukti_reset');
            $buktiReset = null;

            if ($file && $file->isValid()) {
                $newName = $file->getRandomName();
                $file->move(WRITEPATH . 'uploads/reset_bukti', $newName);
                $buktiReset = $newName;
                log_message('debug', 'File uploaded: ' . $newName);
            }

            // Simpan permintaan reset
            $data = [
                'kode_spbu' => session()->get('kode_spbu'),
                'nozzle_id' => $nozzle_id,
                'meter_awal_lama' => $this->_getLastMeter(session()->get('kode_spbu'), $nozzle_id),
                'meter_awal_baru' => (float)$this->request->getPost('meter_awal_baru'),
                'alasan' => $this->request->getPost('alasan'),
                'reset_type' => $this->request->getPost('reset_type'),
                'penjualan_id' => ($this->request->getPost('reset_type') === 'correction')
                    ? $this->request->getPost('penjualan_id')
                    : null,
                'requested_by' => session()->get('user_id'),
                'status' => 'pending',
                'bukti_reset' => $buktiReset
            ];

            // Debug 4: Cek data sebelum save
            log_message('debug', 'Data to save: ' . print_r($data, true));

            if (!$this->resetModel->save($data)) {
                throw new \RuntimeException('Gagal menyimpan permintaan reset');
            }

            // Debug 5: Cek data setelah save
            log_message('debug', 'Reset Request Saved. ID: ' . $this->resetModel->getInsertID());

            return redirect()->to('/penjualan/reset-requests')
                ->with('success', 'Permintaan reset diajukan!');
        } catch (\Exception $e) {
            // Debug 6: Log error detail
            log_message('error', 'Error in handleReset: ' . $e->getMessage());
            log_message('error', 'Trace: ' . $e->getTraceAsString());

            // Buka kunci nozzle jika gagal
            if (isset($nozzle_id)) {
                $this->nozzleModel->update($nozzle_id, [
                    'is_locked' => 0,
                    'lock_reason' => null
                ]);
            }

            return redirect()->back()
                ->with('error', 'Gagal: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function log($id)
    {
        if (!hasAccessToSPBU(session()->get('kode_spbu'))) {
            return redirect()->to('/unauthorized');
        }

        $data['log'] = $this->logModel
            ->where('penjualan_id', $id)
            ->findAll();

        return view('penjualan/log', $data);
    }

    public function delete($id)
    {
        $penjualan = $this->penjualanModel->find($id);
        if (!$penjualan || !hasAccessToSPBU($penjualan['kode_spbu'])) {
            return redirect()->to('/unauthorized');
        }

        $this->penjualanModel->delete($id);
        return redirect()->to('/penjualan')->with('success', 'Penjualan berhasil dihapus');
    }

    private function _shouldShowResetButton($kode_spbu)
    {
        return $this->penjualanModel
            ->where('kode_spbu', $kode_spbu)
            ->countAllResults() > 0;
    }



    private function _getLastMeter($kode_spbu, $nozzle_id)
    {
        // 1. Cek penjualan terakhir di hari yang sama (PRIORITAS UTAMA)
        $lastSaleToday = $this->penjualanModel
            ->where('nozzle_id', $nozzle_id)
            ->where('kode_spbu', $kode_spbu)
            ->where('DATE(tanggal)', date('Y-m-d'))
            ->orderBy('shift', 'DESC')
            ->first();

        if ($lastSaleToday) {
            return (float)$lastSaleToday['meter_akhir'];
        }

        // 2. Cek reset approved terakhir HANYA jika tidak ada penjualan hari ini
        $lastReset = $this->resetModel
            ->where('nozzle_id', $nozzle_id)
            ->where('kode_spbu', $kode_spbu)
            ->where('status', 'approved')
            ->orderBy('approved_at', 'DESC')
            ->first();

        if ($lastReset) {
            return (float)$lastReset['meter_awal_baru'];
        }

        // 3. Cek initial meter jika belum ada penjualan sama sekali
        $nozzle = $this->nozzleModel->find($nozzle_id);
        if (!$this->penjualanModel->where('nozzle_id', $nozzle_id)->countAllResults() && isset($nozzle['initial_meter'])) {
            return (float)$nozzle['initial_meter'];
        }

        // 4. Fallback ke penjualan terakhir (jika ada)
        $lastSale = $this->penjualanModel
            ->where('nozzle_id', $nozzle_id)
            ->where('kode_spbu', $kode_spbu)
            ->orderBy('tanggal', 'DESC')
            ->orderBy('shift', 'DESC')
            ->first();

        return $lastSale ? (float)$lastSale['meter_akhir'] : 0;
    }


    private function _getLastMeters($kode_spbu)
    {
        $meters = [];
        $nozzles = $this->nozzleModel->where('kode_spbu', $kode_spbu)->findAll();

        foreach ($nozzles as $nozzle) {
            $meters[$nozzle['id']] = $this->_getLastMeter($kode_spbu, $nozzle['id']);
        }

        return $meters;
    }

    public function resetForm()
    {
        if (!hasAccessToSPBU(session()->get('kode_spbu'))) {
            return redirect()->to('/unauthorized');
        }

        $kode_spbu = session()->get('kode_spbu');

        $data['nozzles'] = $this->nozzleModel
            ->select('nozzle.*, 
                (SELECT meter_akhir FROM penjualan_harian 
                 WHERE nozzle_id = nozzle.id 
                 ORDER BY tanggal DESC, shift DESC LIMIT 1) as last_meter')
            ->where('kode_spbu', $kode_spbu)
            ->findAll();

        $data['lastSales'] = $this->penjualanModel
            ->select('penjualan_harian.id, penjualan_harian.shift, penjualan_harian.tanggal, 
                     penjualan_harian.meter_awal, penjualan_harian.meter_akhir, nozzle.kode_nozzle')
            ->join('nozzle', 'nozzle.id = penjualan_harian.nozzle_id')
            ->where('penjualan_harian.kode_spbu', $kode_spbu)
            ->orderBy('penjualan_harian.tanggal', 'DESC')
            ->orderBy('penjualan_harian.shift', 'DESC')
            ->findAll();

        return view('penjualan/reset_form', $data);
    }

    public function setupInitialMeters()
    {
        if (!hasAccessToSPBU(session()->get('kode_spbu'))) {
            return redirect()->to('/unauthorized');
        }

        $kode_spbu = session()->get('kode_spbu');
        $nozzles = $this->nozzleModel->where('kode_spbu', $kode_spbu)->findAll();

        $data = [
            'nozzles' => $nozzles,
            'kode_spbu' => $kode_spbu
        ];

        return view('penjualan/setup_initial_meters', $data);
    }

    public function saveInitialMeters()
    {
        if (!hasAccessToSPBU(session()->get('kode_spbu'))) {
            return redirect()->to('/unauthorized');
        }

        $validation = \Config\Services::validation();
        $validation->setRules([
            'meters.*' => 'required|numeric|greater_than_equal_to[0]',
            'is_initial_setup' => 'required'
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()
                ->with('errors', $validation->getErrors())
                ->withInput();
        }

        $meters = $this->request->getPost('meters');
        $isInitialSetup = $this->request->getPost('is_initial_setup') == '1';

        $db = \Config\Database::connect();
        $db->transStart();

        try {
            $updated = 0;
            foreach ($meters as $nozzle_id => $initial_meter) {
                $data = ['initial_meter' => $initial_meter];

                if ($isInitialSetup) {
                    $this->nozzleModel->update($nozzle_id, $data, true);
                    $updated++;
                } else {
                    $builder = $db->table('nozzle');
                    $builder->where('id', $nozzle_id)
                        ->where("initial_meter != {$initial_meter} OR initial_meter IS NULL")
                        ->update($data);
                    $updated += $db->affectedRows();
                }
            }

            $db->transComplete();

            if ($updated > 0) {
                return redirect()->to('/penjualan/create')
                    ->with('success', "Berhasil update $updated nozzle");
            } else {
                return redirect()->back()
                    ->with('info', 'Tidak ada perubahan data')
                    ->withInput();
            }
        } catch (\Exception $e) {
            $db->transRollback();
            log_message('error', 'Gagal save initial meters: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Gagal menyimpan: ' . $e->getMessage())
                ->withInput();
        }
    }

    private function _needInitialSetup($kode_spbu)
    {
        if ($this->penjualanModel->where('kode_spbu', $kode_spbu)->countAllResults() > 0) {
            return false;
        }

        $nozzles = $this->nozzleModel->where('kode_spbu', $kode_spbu)->findAll();
        foreach ($nozzles as $nozzle) {
            if (!isset($nozzle['initial_meter'])) {
                return true;
            }
        }

        return false;
    }

    public function resetRequests()
    {
        if (!hasAccessToSPBU(session()->get('kode_spbu'))) {
            return redirect()->to('/unauthorized');
        }

        $data['requests'] = $this->resetModel
            ->select('meter_reset_request.*, nozzle.kode_nozzle, produk_bbm.nama_produk, operator.nama_operator')
            ->join('nozzle', 'nozzle.id = meter_reset_request.nozzle_id')
            ->join('produk_bbm', 'produk_bbm.kode_produk = nozzle.kode_produk', 'left')
            ->join('operator', 'operator.id = meter_reset_request.requested_by', 'left')
            ->where('meter_reset_request.kode_spbu', session()->get('kode_spbu'))
            ->orderBy('created_at', 'DESC')
            ->findAll();

        return view('penjualan/reset_requests', $data);
    }

    public function getLastMeter($nozzle_id)
    {
        if (!hasAccessToSPBU(session()->get('kode_spbu'))) {
            return $this->response->setStatusCode(403);
        }

        // Gunakan method _getLastMeter yang sudah diperbaiki
        $lastMeter = $this->_getLastMeter(session()->get('kode_spbu'), $nozzle_id);

        return $this->response->setJSON([
            'success' => true,
            'lastMeter' => number_format($lastMeter, 2)
        ]);
    }

    private function _validateDate($date)
    {
        return (bool)strtotime($date); // Validasi sederhana
    }
    public function getNozzles()
    {
        // Pastikan output benar-benar JSON
        header('Content-Type: application/json');

        try {
            // Validasi session lebih ketat
            if (!session()->has('kode_spbu') || !session()->has('user_id')) {
                return $this->response->setStatusCode(401)
                    ->setJSON(['error' => 'Session expired, please login']);
            }

            $dispenserId = $this->request->getGet('dispenser_id');
            if (!$dispenserId) {
                return $this->response->setJSON(['error' => 'dispenser_id required']);
            }

            $nozzles = $this->nozzleModel
                ->select('id, kode_nozzle, current_meter')
                ->where('dispenser_id', $dispenserId)
                ->where('kode_spbu', session()->get('kode_spbu'))
                ->findAll();

            return $this->response->setJSON($nozzles ?: []);
        } catch (\Exception $e) {
            log_message('error', 'getNozzles error: ' . $e->getMessage());
            return $this->response->setStatusCode(500)
                ->setJSON(['error' => 'Internal server error']);
        }
    }
}
