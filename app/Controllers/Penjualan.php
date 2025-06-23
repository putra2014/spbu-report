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

        $data = [
            'operatorList' => $this->operatorModel->where('kode_spbu', $kode_spbu)->findAll(),
            'nozzleList' => $this->nozzleModel->where('kode_spbu', $kode_spbu)->findAll(),
            'showResetButton' => $this->_shouldShowResetButton($kode_spbu),
            'lastMeters' => $this->_getLastMeters($kode_spbu)
        ];

        return view('penjualan/create', $data);
    }

    public function store()
    {
        
        if (!hasAccessToSPBU(session()->get('kode_spbu'))) {
            return redirect()->to('/unauthorized');
        }

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
                $errorMsg .= "- Tanggal: ".date('d/m/Y', strtotime($tanggal))."<br>";
                $errorMsg .= "- Shift: $shift<br>";
                $errorMsg .= "- Nozzle: ".($nozzle ? $nozzle['kode_nozzle'] : 'Unknown')."<br>";
                $errorMsg .= "Meter akhir: ".number_format($existing['meter_akhir'], 2)." L";
                
                throw new \RuntimeException($errorMsg);
            }

            if ($meter_akhir < $meter_awal) {
                throw new \RuntimeException('Meter akhir tidak boleh lebih kecil dari meter awal');
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
                'approval_status' => 'approved'
            ];

            if (!$this->penjualanModel->save($penjualanData)) {
                throw new \RuntimeException('Gagal menyimpan penjualan: ' . implode(', ', $this->penjualanModel->errors()));
            }

            $this->logModel->save($penjualanData);
            $this->nozzleModel->update($nozzle_id, ['current_meter' => $meter_akhir]);
            
            $db->transComplete();

            return redirect()->to('/penjualan')->with('success', 'Penjualan berhasil disimpan');

        } catch (\Exception $e) {
            $db->transRollback();
            log_message('error', 'Error Penjualan/store: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', $e->getMessage())
                ->withInput()
                ->with('showResetButton', strpos($e->getMessage(), 'Meter awal harus sama') !== false);
        }
    }

public function handleReset()
{
    if (!hasAccessToSPBU(session()->get('kode_spbu'))) {
        return redirect()->to('/unauthorized');
    }

    $validationRules = [
        'nozzle_id' => 'required|numeric',
        'meter_awal_baru' => 'required|decimal',
        'alasan' => 'required|min_length[5]',
        'reset_type' => 'required|in_list[physical,correction]',
        'bukti_reset' => 'if_exist|max_size[bukti_reset,2048]|is_image[bukti_reset]',
    ];

    if ($this->request->getPost('reset_type') === 'correction') {
        $validationRules['penjualan_id'] = 'required|numeric';
    }

    if (!$this->validate($validationRules)) {
        return redirect()->back()
            ->with('errors', $this->validator->getErrors())
            ->withInput();
    }

    // Tangani file upload di Controller saja
    $file = $this->request->getFile('bukti_reset');
    $buktiReset = null;

    try {
        if ($file && $file->isValid() && !$file->hasMoved()) {
            $newName = $file->getRandomName();
            $uploadPath = WRITEPATH . 'uploads/reset_bukti';
            if (!is_dir($uploadPath)) {
                mkdir($uploadPath, 0777, true);
            }
            $file->move($uploadPath, $newName);
            $buktiReset = $newName;
        } elseif ($file && $file->getError() !== UPLOAD_ERR_NO_FILE) {
            throw new \RuntimeException($file->getErrorString());
        }
    } catch (\Exception $e) {
        log_message('error', 'File upload error: ' . $e->getMessage());
        return redirect()->back()
            ->with('error', 'Gagal mengupload bukti reset: ' . $e->getMessage())
            ->withInput();
    }

    $meterAwalBaru = (float)$this->request->getPost('meter_awal_baru');
    $resetType = $this->request->getPost('reset_type');

    if ($resetType === 'correction') {
        $penjualanId = $this->request->getPost('penjualan_id');
        $penjualan = $this->penjualanModel->find($penjualanId);

        if ($meterAwalBaru < $penjualan['meter_awal']) {
            return redirect()->back()
                ->with('error', 'Untuk koreksi data, meter baru tidak boleh lebih kecil dari meter awal (' . number_format($penjualan['meter_awal'], 2) . ' L)')
                ->withInput();
        }
    }

    // Data untuk INSERT manual
    $data = [
        'kode_spbu' => session()->get('kode_spbu'),
        'nozzle_id' => $this->request->getPost('nozzle_id'),
        'meter_awal_lama' => $this->_getLastMeter(session()->get('kode_spbu'), $this->request->getPost('nozzle_id')),
        'meter_awal_baru' => $this->request->getPost('meter_awal_baru'),
        'alasan' => $this->request->getPost('alasan'),
        'reset_type' => $this->request->getPost('reset_type'),
        'penjualan_id' => $this->request->getPost('penjualan_id'),
        'requested_by' => session()->get('user_id'),
        'status' => 'pending',
        'approved_by' => null,
        'approved_at' => null,
        'bukti_reset' => $buktiReset,
        'catatan' => $this->request->getPost('catatan'),
        'created_at' => date('Y-m-d H:i:s'),
    ];

    // INSERT manual → TANPA validasi Model
    $this->db->table('meter_reset_request')->insert($data);

    return redirect()->to('/penjualan/reset-requests')
        ->with('success', 'Permintaan reset telah diajukan. Menunggu persetujuan Admin Region.');
}


    public function approveReset($id)
    {
        if (session()->get('role') !== 'admin_region') {
            return redirect()->to('/unauthorized');
        }
    
        $request = $this->resetModel->find($id);
        if (!$request) {
            return redirect()->back()->with('error', 'Permintaan reset tidak ditemukan');
        }
    
        $db = \Config\Database::connect();
        $db->transStart();
    
        try {
            if ($request['reset_type'] === 'physical') {
                /********************************************
                 * HANDLE PHYSICAL RESET (KERUSAKAN/KALIBRASI)
                 ********************************************/
                
                // 1. Update nozzle current meter
                $this->nozzleModel->update($request['nozzle_id'], [
                    'current_meter' => $request['meter_awal_baru'],
                    'last_reset_at' => date('Y-m-d H:i:s')
                ]);
            
                // 2. Log khusus reset fisik (TANPA sentuh data penjualan)
                $this->logModel->insert([
                    'nozzle_id' => $request['nozzle_id'],
                    'kode_spbu' => $request['kode_spbu'],
                    'action' => 'physical_reset',
                    'old_value' => $request['meter_awal_lama'],
                    'new_value' => $request['meter_awal_baru'],
                    'executed_by' => session()->get('user_id'),
                    'notes' => $request['alasan'],
                    'created_at' => date('Y-m-d H:i:s')
                ]);
            
            } else {
                /********************************************
                 * HANDLE DATA CORRECTION
                 ********************************************/
                
                // Validasi wajib untuk koreksi data
                if (empty($request['penjualan_id'])) {
                    throw new \Exception("Data penjualan tidak ditemukan");
                }
            
                $this->_handleCorrection(
                    $request['penjualan_id'],
                    $request['meter_awal_baru'],
                    $request['alasan']
                );
            }
        
            // 3. Update status request (untuk semua jenis reset)
            $this->resetModel->update($id, [
                'status' => 'approved',
                'approved_by' => session()->get('user_id'),
                'approved_at' => date('Y-m-d H:i:s')
            ]);
        
            $db->transComplete();
            return redirect()->to('/admin/approvals')->with('success', 'Reset meter telah disetujui');
        
        } catch (\Exception $e) {
            $db->transRollback();
            log_message('error', 'Gagal approve reset: '.$e->getMessage());
            return redirect()->back()
                   ->with('error', 'Gagal menyetujui: '.$e->getMessage())
                   ->withInput();
        }
    }
    
    private function _handleCorrection($penjualan_id, $new_meter, $alasan)
    {
        // 1. Validasi data
        $currentData = $this->penjualanModel->find($penjualan_id);
        if (!$currentData) {
            throw new \Exception("Data penjualan tidak ditemukan");
        }
    
        if ($new_meter < $currentData['meter_awal']) {
            throw new \Exception(
                "Meter baru (".number_format($new_meter,2).") ".
                "tidak boleh kurang dari meter awal (".number_format($currentData['meter_awal'],2).")"
            );
        }
    
        // 2. Update data penjualan
        $this->penjualanModel->update($penjualan_id, [
            'meter_akhir' => $new_meter,
            'volume' => $new_meter - $currentData['meter_awal'],
            'alasan_reset' => $alasan,
            'approved_by' => session()->get('user_id'),
            'approved_at' => date('Y-m-d H:i:s'),
            'is_adjusted' => 1
        ]);
    
        // 3. Update nozzle
        $this->nozzleModel->update($currentData['nozzle_id'], [
            'current_meter' => $new_meter
        ]);
    
        // 4. Adjust shift berikutnya
        $this->_adjustSubsequentShifts($penjualan_id, $new_meter);
    
        // 5. Catat log koreksi
        $this->logModel->insert([
            'penjualan_id' => $penjualan_id,
            'nozzle_id' => $currentData['nozzle_id'],
            'kode_spbu' => $currentData['kode_spbu'],
            'action' => 'data_correction',
            'old_value' => json_encode([
                'meter_awal' => $currentData['meter_awal'],
                'meter_akhir' => $currentData['meter_akhir'],
                'volume' => $currentData['volume']
            ]),
            'new_value' => json_encode([
                'meter_awal' => $currentData['meter_awal'],
                'meter_akhir' => $new_meter,
                'volume' => ($new_meter - $currentData['meter_awal'])
            ]),
            'executed_by' => session()->get('user_id'),
            'notes' => $alasan,
            'created_at' => date('Y-m-d H:i:s')
        ]);
    }

    private function _adjustSubsequentShifts($penjualan_id, $new_meter)
    {
        $currentSale = $this->penjualanModel->find($penjualan_id);
        $nextSales = $this->penjualanModel
            ->where('nozzle_id', $currentSale['nozzle_id'])
            ->where('tanggal >=', $currentSale['tanggal'])
            ->where('shift >', $currentSale['shift'])
            ->orderBy('tanggal', 'ASC')
            ->orderBy('shift', 'ASC')
            ->findAll();

        foreach ($nextSales as $nextSale) {
            $this->penjualanModel->update($nextSale['id'], [
                'meter_awal' => $new_meter,
                'volume' => $nextSale['meter_akhir'] - $new_meter
            ]);
            $new_meter = $nextSale['meter_akhir'];
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
        // 1. Cek nozzle untuk initial meter jika belum ada penjualan
        $nozzle = $this->nozzleModel->find($nozzle_id);
        $hasSales = $this->penjualanModel->where('nozzle_id', $nozzle_id)->countAllResults() > 0;

        if (!$hasSales && isset($nozzle['initial_meter'])) {
            return (float)$nozzle['initial_meter'];
        }

        // 2. Ambil meter akhir penjualan terakhir (jika ada)
        $lastSale = $this->penjualanModel
            ->where('nozzle_id', $nozzle_id)
            ->where('kode_spbu', $kode_spbu)
            ->orderBy('tanggal', 'DESC')
            ->orderBy('shift', 'DESC')
            ->first();

        if ($lastSale) {
            return (float)$lastSale['meter_akhir'];
        }

        // 3. Fallback ke reset terakhir atau 0
        $lastReset = $this->resetModel
            ->where('nozzle_id', $nozzle_id)
            ->where('status', 'approved')
            ->orderBy('approved_at', 'DESC')
            ->first();

        return $lastReset ? (float)$lastReset['meter_awal_baru'] : 0;
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
        
        // Data nozzle dengan meter terakhir
        $data['nozzles'] = $this->nozzleModel
            ->select('nozzle.*, 
                (SELECT meter_akhir FROM penjualan_harian 
                 WHERE nozzle_id = nozzle.id 
                 ORDER BY tanggal DESC, shift DESC LIMIT 1) as last_meter')
            ->where('kode_spbu', $kode_spbu)
            ->findAll();
    
        // Data penjualan terakhir untuk koreksi
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
            log_message('error', 'Gagal save initial meters: '.$e->getMessage());
            return redirect()->back()
                ->with('error', 'Gagal menyimpan: '.$e->getMessage())
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
}