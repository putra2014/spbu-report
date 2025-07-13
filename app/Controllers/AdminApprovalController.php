<?php
namespace App\Controllers;

use App\Models\MeterResetRequestModel;
use App\Models\NozzleModel;
use App\Models\PenjualanModel;
use App\Models\PenjualanLogModel;
use Config\Database;

class AdminApprovalController extends BaseController
{
    protected $resetModel;
    protected $nozzleModel;
    protected $penjualanModel;
    protected $logModel;
    protected $db;

    public function __construct()
    {
        // Inisialisasi koneksi database
        $this->db = Database::connect();
        
        // Inisialisasi model dengan dependency injection
        $this->resetModel = new MeterResetRequestModel($this->db);
        $this->nozzleModel = new NozzleModel($this->db);
        $this->penjualanModel = new PenjualanModel($this->db);
        $this->logModel = new PenjualanLogModel($this->db);

        helper(['form', 'session']);

        // Validasi role
        if (session()->get('role') !== 'admin_region') {
            return redirect()->to('/unauthorized');
        }
    }

    // METHOD INDEX YANG DIBUTUHKAN
    public function index()
    {
        $data = [
            'requests' => $this->resetModel->getPendingRequests(),
            'operatorNames' => $this->getOperatorNames()
        ];
        
        return view('admin/approvals', $data);
    }

    // METHOD APPROVE YANG SUDAH DIPERBAIKI
public function approveReset($id)
{
    // Mulai transaksi database
    $this->db->transStart();

    try {
        // 1. Dapatkan data permintaan reset
        $builder = $this->db->table('meter_reset_request');
        $request = $builder->where('id', $id)->get()->getRowArray();

        // Validasi data permintaan
        if (!$request) {
            throw new \RuntimeException("Data permintaan tidak ditemukan");
        }

        // Debug: Tampilkan data request
        log_message('debug', 'Data request dari DB: '.print_r($request, true));

        // 2. Validasi reset_type
        if (!array_key_exists('reset_type', $request)) {
            log_message('error', 'Struktur data tidak valid: '.print_r($request, true));
            throw new \RuntimeException("Struktur data permintaan tidak valid");
        }

        // 3. Proses approval berdasarkan jenis reset
        if ($request['reset_type'] === 'physical') {
            if (empty($request['nozzle_id'])) {
                throw new \RuntimeException("Nozzle ID diperlukan untuk reset fisik");
            }
            $this->processPhysicalReset($request);
        } else {
            if (empty($request['penjualan_id'])) {
                throw new \RuntimeException("ID Penjualan diperlukan untuk koreksi data");
            }
            $this->processDataCorrection($request);
        }

        // 4. Update status permintaan
        $this->db->table('meter_reset_request')
            ->where('id', $id)
            ->update([
                'status' => 'approved',
                'approved_by' => session()->get('user_id'),
                'approved_at' => date('Y-m-d H:i:s')
            ]);

        // 5. Buka kunci nozzle
        $this->nozzleModel->update($request['nozzle_id'], [
            'is_locked' => 0,
            'lock_reason' => null
        ]);

        // Commit transaksi
        $this->db->transComplete();

        log_message('debug', 'Approval berhasil untuk ID: '.$id);
        return redirect()->to('/admin/approvals')->with('success', 'Approval berhasil');

    } catch (\Exception $e) {
        // Rollback transaksi jika ada error
        $this->db->transRollback();
        
        log_message('error', 'Gagal approve ID '.$id.': '.$e->getMessage());
        return redirect()->back()->with('error', 'Gagal: '.$e->getMessage());
    }
}

    private function processPhysicalReset($request)
    {
        // 1. Update nozzle (TIDAK BERUBAH)
        $this->nozzleModel->update($request['nozzle_id'], [
            'current_meter' => $request['meter_awal_baru'],
            'last_reset_at' => date('Y-m-d H:i:s')
        ]);

        // 2. Update penjualan yang belum diapprove (DITAMBAHKAN)
        $this->penjualanModel
            ->where('nozzle_id', $request['nozzle_id'])
            ->where('approval_status', 'pending')
            ->where('tanggal >=', date('Y-m-d')) // hanya yang tanggalnya sama atau setelah reset
            ->set([
                'meter_awal' => $request['meter_awal_baru'],
                'volume' => "meter_akhir - {$request['meter_awal_baru']}"
            ])
            ->update();

        // 3. Catat log (TIDAK BERUBAH)
        $this->logModel->insert([
            'tanggal' => date('Y-m-d H:i:s'),
            'kode_spbu' => $request['kode_spbu'],
            'nozzle_id' => $request['nozzle_id'],
            'meter_awal' => $request['meter_awal_lama'],
            'meter_akhir' => $request['meter_awal_baru'],
            'action' => 'physical_reset',
            'executed_by' => session()->get('user_id'),
            'notes' => 'Reset fisik. Alasan: ' . $request['alasan'], // DIPERBAIKI
            'approval_status' => 'approved',
            'approved_at' => date('Y-m-d H:i:s')
        ]);
    }

    private function processDataCorrection($request)
    {
        // 1. Dapatkan data penjualan
        $penjualan = $this->penjualanModel->find($request['penjualan_id']);
        if (!$penjualan) {
            throw new \RuntimeException("Data penjualan tidak ditemukan");
        }

        // 2. Update penjualan
        $this->penjualanModel->update($request['penjualan_id'], [
            'meter_akhir' => $request['meter_awal_baru'],
            'volume' => $request['meter_awal_baru'] - $penjualan['meter_awal'],
            'is_adjusted' => 1
        ]);

        // 3. Update nozzle
        $this->nozzleModel->update($request['nozzle_id'], [
            'current_meter' => $request['meter_awal_baru']
        ]);

        // 4. Catat log SESUAI STRUKTUR
        $this->logModel->insert([
            'tanggal' => date('Y-m-d H:i:s'),
            'kode_spbu' => $request['kode_spbu'],
            'nozzle_id' => $request['nozzle_id'],
            'shift' => $penjualan['shift'],
            'meter_awal' => $penjualan['meter_awal'],
            'meter_akhir' => $request['meter_awal_baru'],
            'harga_jual' => $penjualan['harga_jual'],
            'action' => 'data_correction',
            'executed_by' => session()->get('user_id'),
            'notes' => 'Koreksi data. Penjualan ID: ' . $request['penjualan_id'],
            'approval_status' => 'approved',
            'approved_at' => date('Y-m-d H:i:s')
        ]);
    }

    // METHOD REJECT YANG SUDAH ADA
public function rejectReset($id)
{
    $this->db->transStart();
    try {
        $request = $this->resetModel->find($id);
        
        $this->resetModel->update($id, [
            'status' => 'rejected',
            'approved_by' => session()->get('user_id'),
            'approved_at' => date('Y-m-d H:i:s')
        ]);

        // Buka kunci nozzle
        $this->nozzleModel->update($request['nozzle_id'], [
            'is_locked' => 0,
            'lock_reason' => null
        ]);

        $this->db->transComplete();
        return redirect()->to('/admin/approvals')->with('success', 'Permintaan reset ditolak');

    } catch (\Exception $e) {
        $this->db->transRollback();
        return redirect()->back()->with('error', 'Gagal: '.$e->getMessage());
    }
}

    // METHOD HISTORY YANG SUDAH ADA
    public function history()
    {
        $data = [
            'requests' => $this->resetModel
                ->where('status !=', 'pending')
                ->orderBy('approved_at', 'DESC')
                ->findAll(),
            'operatorNames' => $this->getOperatorNames()
        ];

        return view('admin/reset_history', $data);
    }

    /*********************
     * PRIVATE METHODS
     *********************/
    private function getValidatedRequest($id)
    {
        $request = $this->resetModel->find($id);
        if (!$request) {
            throw new \RuntimeException("Data permintaan tidak ditemukan");
        }

        // Fallback untuk reset_type
        if (!isset($request['reset_type'])) {
            $request['reset_type'] = 'physical';
            log_message('warning', 'Reset type tidak ditemukan, menggunakan default physical');
        }

        return $request;
    }


    private function handleDataCorrection($request)
    {
        if (empty($request['penjualan_id'])) {
            throw new \RuntimeException("ID Penjualan diperlukan untuk koreksi data");
        }

        $penjualan = $this->penjualanModel->find($request['penjualan_id']);
        if (!$penjualan) {
            throw new \RuntimeException("Data penjualan tidak ditemukan");
        }

        // Update data penjualan
        $this->penjualanModel->update($request['penjualan_id'], [
            'meter_akhir' => $request['meter_awal_baru'],
            'volume' => $request['meter_awal_baru'] - $penjualan['meter_awal'],
            'is_adjusted' => 1
        ]);

        // Update nozzle
        $this->nozzleModel->update($request['nozzle_id'], [
            'current_meter' => $request['meter_awal_baru']
        ]);

        // Adjust shift berikutnya
        $this->adjustSubsequentShifts($request['penjualan_id'], $request['meter_awal_baru']);
    }

    private function adjustSubsequentShifts($penjualan_id, $new_meter)
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

    private function updateRequestStatus($id)
    {
        $this->resetModel->update($id, [
            'status' => 'approved',
            'approved_by' => session()->get('user_id'),
            'approved_at' => date('Y-m-d H:i:s')
        ]);
    }

    private function getOperatorNames()
    {
        $operatorModel = new \App\Models\OperatorModel();
        $operators = $operatorModel->findAll();

        $names = [];
        foreach ($operators as $operator) {
            $names[$operator['id']] = $operator['nama_operator'];
        }

        return $names;
    }
}