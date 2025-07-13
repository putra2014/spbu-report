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
use App\Models\StokModel;

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
    protected $stokModel;

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
        $this->spbuModel = new SpbuModel();
        $this->dispenserModel = new DispenserModel();
        $this->db = \Config\Database::connect();
        $this->stokModel = new StokModel();
        helper('Access');
    }

    public function index()
    {
        $role = session()->get('role');
        $kode_spbu = session()->get('kode_spbu');

        $builder = $this->penjualanModel
            ->select('penjualan_harian.*, nozzle.kode_nozzle, produk_bbm.nama_produk, operator.nama_operator')
            ->join('nozzle', 'nozzle.id = penjualan_harian.nozzle_id')
            ->join('produk_bbm', 'produk_bbm.kode_produk = nozzle.kode_produk', 'left')
            ->join('operator', 'operator.id = penjualan_harian.operator_id', 'left')
            ->orderBy('tanggal DESC, shift ASC');

        if ($role === 'admin_spbu') {
            $builder->where('penjualan_harian.kode_spbu', $kode_spbu);
        }

        $data['penjualan'] = $builder->findAll();
        return view('penjualan/index', $data);
    }

    public function create()
    {
        if (!hasAccessToSPBU(session()->get('kode_spbu'))) {
            return redirect()->to('/unauthorized');
        }

        $kode_spbu = session()->get('kode_spbu');
        $role = session()->get('role');
        
        $data = [
            'operatorList' => $this->operatorModel->where('kode_spbu', $kode_spbu)->findAll(),
            'dispensers' => $this->dispenserModel->where('kode_spbu', $kode_spbu)->findAll(),
            'showResetButton' => $this->_shouldShowResetButton($kode_spbu),
            'lastMeters' => $this->_getLastMeters($kode_spbu),
            'disable_meter_awal' => true
        ];

        if ($role === 'admin_region') {
            $data['spbuList'] = $this->spbuModel->findAll();
        }

        return view('penjualan/create', $data);
    }

    public function store()
    {
        if (!hasAccessToSPBU(session()->get('kode_spbu'))) {
            return redirect()->to('/unauthorized');
        }

        $validation = \Config\Services::validation();
        $validation->setRules([
            'meter_akhir' => 'required|decimal',
            'meter_awal' => 'required|decimal',
            'nozzle_id' => 'required|numeric',
            'shift' => 'required|in_list[1,2,3]',
            'tanggal' => 'required|valid_date'
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        $nozzle_id = $this->request->getPost('nozzle_id');
        $nozzle = $this->nozzleModel->find($nozzle_id);
        
        if ($nozzle['is_locked']) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Nozzle ini terkunci: ' . $nozzle['lock_reason']);
        }

        $db = \Config\Database::connect();
        $db->transStart();

        try {
            $kode_spbu = session()->get('kode_spbu');
            $tanggal = date('Y-m-d', strtotime($this->request->getPost('tanggal')));
            $shift = $this->request->getPost('shift');
            $meter_awal = (float)$this->request->getPost('meter_awal');
            $meter_akhir = (float)$this->request->getPost('meter_akhir');
            $operator_id = $this->request->getPost('operator_id');
            $user_id = session()->get('user_id');

            $existing = $this->penjualanModel
                ->where('kode_spbu', $kode_spbu)
                ->where('DATE(tanggal)', $tanggal)
                ->where('shift', $shift)
                ->where('nozzle_id', $nozzle_id)
                ->first();

            if ($existing) {
                throw new \RuntimeException("Data penjualan sudah ada untuk shift ini");
            }

            $lastMeter = $this->_getLastMeter($kode_spbu, $nozzle_id);
            if ($meter_awal < $lastMeter) {
                throw new \RuntimeException("Meter awal tidak valid");
            }

            $tangki = $this->tangkiModel->where('kode_spbu', $kode_spbu)
                ->where('kode_tangki', $nozzle['kode_tangki'])
                ->first();

            if (!$tangki) {
                throw new \RuntimeException('Konfigurasi tangki tidak valid');
            }

            $harga = $this->hargaModel
                ->where('kode_produk', $tangki['kode_produk'])
                ->orderBy('created_at', 'DESC')
                ->first();

            if (!$harga) {
                throw new \RuntimeException("Harga produk belum ditentukan");
            }

            $volume = $meter_akhir - $meter_awal;
            $total_penjualan = $volume * $harga['harga_jual'];

            $penjualanData = [
                'kode_spbu' => $kode_spbu,
                'tanggal' => $tanggal,
                'shift' => $shift,
                'nozzle_id' => $nozzle_id,
                'meter_awal' => $meter_awal,
                'meter_akhir' => $meter_akhir,
                'harga_jual' => $harga['harga_jual'],
                'operator_id' => $operator_id,
                'approved_by' => $user_id,
                'approved_at' => date('Y-m-d H:i:s'),
                'approval_status' => 'approved'
            ];

            if (!$this->penjualanModel->save($penjualanData)) {
                throw new \RuntimeException('Gagal menyimpan penjualan');
            }

            $penjualanId = $this->penjualanModel->getInsertID();
            $nozzle = $this->nozzleModel->find($nozzle_id);
            $tangki = $this->tangkiModel->where('kode_spbu', $kode_spbu)
                                       ->where('kode_tangki', $nozzle['kode_tangki'])
                                       ->first();

            if (!$tangki) {
                throw new \RuntimeException('Konfigurasi tangki tidak valid');
            }

            // 2. Update stok_bbm
            $penjualanModel = new \App\Models\PenjualanModel();
            $this->stokModel->updateStok($tangki['id'], $volume, 'kurang');

            // 3. Update stok di tabel tangki (jika diperlukan)
            $this->tangkiModel->update($tangki['id'], [
                'stok' => $tangki['stok'] - $volume
]);
            $this->nozzleModel->update($nozzle_id, ['current_meter' => $meter_akhir]);
            $this->_createLog($penjualanId, 'create', $user_id, 'Input penjualan baru');

            $db->transComplete();
            return redirect()->to('/penjualan')->with('success', 'Data berhasil disimpan');
        } catch (\Exception $e) {
            $db->transRollback();
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function log($id)
    {
        if (!hasAccessToSPBU(session()->get('kode_spbu'))) {
            return redirect()->to('/unauthorized');
        }

        $data['log'] = $this->logModel
            ->select('penjualan_harian_log.*, users.username as executed_by_name')
            ->join('users', 'users.id = penjualan_harian_log.executed_by', 'left')
            ->where('penjualan_id', $id)
            ->orderBy('created_at', 'DESC')
            ->findAll();

        return view('penjualan/log', $data);
    }

    public function delete($id)
    {
        $penjualan = $this->penjualanModel->find($id);
        if (!$penjualan || !hasAccessToSPBU($penjualan['kode_spbu'])) {
            return redirect()->to('/unauthorized');
        }

        $db = \Config\Database::connect();
        $db->transStart();

        try {
            $this->penjualanModel->delete($id);
            $this->_createLog($id, 'delete', session()->get('user_id'), 'Hapus data penjualan');
            
            $db->transComplete();
            return redirect()->to('/penjualan')->with('success', 'Penjualan berhasil dihapus');
        } catch (\Exception $e) {
            $db->transRollback();
            return redirect()->back()->with('error', 'Gagal menghapus: ' . $e->getMessage());
        }
    }

    public function handleReset()
    {
        if (!hasAccessToSPBU(session()->get('kode_spbu'))) {
            return redirect()->to('/unauthorized');
        }

        $validation = \Config\Services::validation();
        $validation->setRules([
            'nozzle_id' => 'required|numeric',
            'meter_awal_baru' => 'required|decimal',
            'alasan' => 'required|min_length[5]',
            'reset_type' => 'required|in_list[physical,correction]'
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        $nozzle_id = $this->request->getPost('nozzle_id');
        $user_id = session()->get('user_id');

        $db = \Config\Database::connect();
        $db->transStart();

        try {
            $this->nozzleModel->update($nozzle_id, [
                'is_locked' => 1,
                'lock_reason' => 'Menunggu approval reset'
            ]);

            $file = $this->request->getFile('bukti_reset');
            $buktiReset = null;
            
            if ($file && $file->isValid()) {
                $newName = $file->getRandomName();
                $file->move(WRITEPATH.'uploads/reset_bukti', $newName);
                $buktiReset = $newName;
            }

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
                'requested_by' => $user_id,
                'status' => 'pending',
                'bukti_reset' => $buktiReset
            ];

            if (!$this->resetModel->save($data)) {
                throw new \RuntimeException('Gagal menyimpan permintaan reset');
            }

            $requestId = $this->resetModel->getInsertID();
            $this->_createLog($requestId, 'reset_request', $user_id, 'Permintaan reset meter');

            $db->transComplete();
            return redirect()->to('/penjualan/reset-requests')
                ->with('success', 'Permintaan reset diajukan!');
        } catch (\Exception $e) {
            $db->transRollback();
            $this->nozzleModel->update($nozzle_id, [
                'is_locked' => 0,
                'lock_reason' => null
            ]);
            return redirect()->back()
                ->with('error', 'Gagal: '.$e->getMessage())
                ->withInput();
        }
    }

    public function resetRequests()
    {
        if (!hasAccessToSPBU(session()->get('kode_spbu'))) {
            return redirect()->to('/unauthorized');
        }

        $data['requests'] = $this->resetModel
            ->select('meter_reset_request.*, nozzle.kode_nozzle, produk_bbm.nama_produk, users.username as requested_by_name')
            ->join('nozzle', 'nozzle.id = meter_reset_request.nozzle_id')
            ->join('produk_bbm', 'produk_bbm.kode_produk = nozzle.kode_produk', 'left')
            ->join('users', 'users.id = meter_reset_request.requested_by', 'left')
            ->where('meter_reset_request.kode_spbu', session()->get('kode_spbu'))
            ->orderBy('created_at', 'DESC')
            ->findAll();

        return view('penjualan/reset_requests', $data);
    }

    public function approveReset($id)
    {
        if (!hasAccessToSPBU(session()->get('kode_spbu'))) {
            return redirect()->to('/unauthorized');
        }

        $request = $this->resetModel->find($id);
        if (!$request) {
            return redirect()->back()->with('error', 'Permintaan tidak ditemukan');
        }

        $user_id = session()->get('user_id');
        $db = \Config\Database::connect();
        $db->transStart();

        try {
            // Update request status
            $this->resetModel->update($id, [
                'status' => 'approved',
                'approved_by' => $user_id,
                'approved_at' => date('Y-m-d H:i:s')
            ]);

            // Update nozzle meter and unlock
            $this->nozzleModel->update($request['nozzle_id'], [
                'current_meter' => $request['meter_awal_baru'],
                'is_locked' => 0,
                'lock_reason' => null
            ]);

            // Log the approval
            $this->_createLog($id, 'reset_approve', $user_id, 'Approval reset meter');

            $db->transComplete();
            return redirect()->back()->with('success', 'Permintaan reset disetujui');
        } catch (\Exception $e) {
            $db->transRollback();
            return redirect()->back()->with('error', 'Gagal menyetujui: ' . $e->getMessage());
        }
    }

    public function rejectReset($id)
    {
        if (!hasAccessToSPBU(session()->get('kode_spbu'))) {
            return redirect()->to('/unauthorized');
        }

        $request = $this->resetModel->find($id);
        if (!$request) {
            return redirect()->back()->with('error', 'Permintaan tidak ditemukan');
        }

        $user_id = session()->get('user_id');
        $db = \Config\Database::connect();
        $db->transStart();

        try {
            $this->resetModel->update($id, [
                'status' => 'rejected',
                'approved_by' => $user_id,
                'approved_at' => date('Y-m-d H:i:s')
            ]);

            $this->nozzleModel->update($request['nozzle_id'], [
                'is_locked' => 0,
                'lock_reason' => null
            ]);

            $this->_createLog($id, 'reset_reject', $user_id, 'Penolakan reset meter');

            $db->transComplete();
            return redirect()->back()->with('success', 'Permintaan reset ditolak');
        } catch (\Exception $e) {
            $db->transRollback();
            return redirect()->back()->with('error', 'Gagal menolak: ' . $e->getMessage());
        }
    }

    public function getNozzles()
{
    if (!session()->has('kode_spbu') || !session()->has('user_id')) {
        return $this->response->setStatusCode(401)
            ->setJSON(['error' => 'Unauthorized']);
    }

    $dispenserId = $this->request->getGet('dispenser_id');
    if (!$dispenserId) {
        return $this->response->setJSON(['error' => 'dispenser_id required']);
    }

    $nozzles = $this->nozzleModel
        ->select('id, kode_nozzle, current_meter, initial_meter')
        ->where('dispenser_id', $dispenserId)
        ->where('kode_spbu', session()->get('kode_spbu'))
        ->where('status', 'Aktif')
        ->findAll();

    // Tambahkan fallback untuk current_meter null
    $response = array_map(function($nozzle) {
        return [
            'id' => $nozzle['id'],
            'kode_nozzle' => $nozzle['kode_nozzle'],
            'current_meter' => $nozzle['current_meter'] ?? $nozzle['initial_meter'] ?? 0
        ];
    }, $nozzles);

    return $this->response->setJSON($response ?: []);
}

    public function getLastMeter($nozzle_id)
    {
        if (!hasAccessToSPBU(session()->get('kode_spbu'))) {
            return $this->response->setStatusCode(403);
        }

        $lastMeter = $this->_getLastMeter(session()->get('kode_spbu'), $nozzle_id);

        return $this->response->setJSON([
            'success' => true,
            'lastMeter' => number_format($lastMeter, 2)
        ]);
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
            'kode_spbu' => $kode_spbu,
            'needInitialSetup' => $this->_needInitialSetup($kode_spbu)
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
        $user_id = session()->get('user_id');

        $db = \Config\Database::connect();
        $db->transStart();

        try {
            $updated = 0;
            foreach ($meters as $nozzle_id => $initial_meter) {
                $data = ['initial_meter' => $initial_meter];
                
                if ($isInitialSetup) {
                    $this->nozzleModel->update($nozzle_id, $data);
                    $updated++;
                } else {
                    $builder = $db->table('nozzle');
                    $builder->where('id', $nozzle_id)
                        ->where("initial_meter != {$initial_meter} OR initial_meter IS NULL")
                        ->update($data);
                    $updated += $db->affectedRows();
                }

                $this->_createLog($nozzle_id, 'meter_setup', $user_id, 'Setup initial meter: ' . $initial_meter);
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
            return redirect()->back()
                ->with('error', 'Gagal menyimpan: '.$e->getMessage())
                ->withInput();
        }
    }

    private function _createLog($entityId, $action, $executedBy, $notes = '')
    {
        $logData = [
            'entity_id' => $entityId,
            'entity_type' => ($action === 'create' || $action === 'delete') ? 'penjualan' : 'reset_request',
            'action' => $action,
            'executed_by' => $executedBy,
            'notes' => $notes,
            'created_at' => date('Y-m-d H:i:s')
        ];

        $this->logModel->insert($logData);
    }

    private function _getLastMeter($kode_spbu, $nozzle_id) 
    {
        $lastSaleToday = $this->penjualanModel
            ->where('nozzle_id', $nozzle_id)
            ->where('kode_spbu', $kode_spbu)
            ->where('DATE(tanggal)', date('Y-m-d'))
            ->orderBy('shift', 'DESC')
            ->first();
    
        if ($lastSaleToday) {
            return (float)$lastSaleToday['meter_akhir'];
        }
    
        $lastReset = $this->resetModel
            ->where('nozzle_id', $nozzle_id)
            ->where('kode_spbu', $kode_spbu)
            ->where('status', 'approved')
            ->orderBy('approved_at', 'DESC')
            ->first();
    
        if ($lastReset) {
            return (float)$lastReset['meter_awal_baru'];
        }
    
        $nozzle = $this->nozzleModel->find($nozzle_id);
        if (!$this->penjualanModel->where('nozzle_id', $nozzle_id)->countAllResults() && isset($nozzle['initial_meter'])) {
            return (float)$nozzle['initial_meter'];
        }
    
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

    private function _shouldShowResetButton($kode_spbu)
    {
        return $this->penjualanModel
            ->where('kode_spbu', $kode_spbu)
            ->countAllResults() > 0;
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
}