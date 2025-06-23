<?php

namespace App\Controllers;

use App\Models\MeterResetRequestModel;
use App\Models\NozzleModel;
use App\Models\PenjualanLogModel;

class AdminApprovalController extends BaseController
{
    protected $resetModel;
    protected $nozzleModel;
    protected $logModel;

    public function __construct()
    {
        $this->resetModel = new MeterResetRequestModel();
        $this->nozzleModel = new NozzleModel();
        $this->logModel = new PenjualanLogModel();
        
        helper(['form', 'session']);

        if (!session()->get('admin_region')) {
            return redirect()->to('/unauthorized');
        }
    }

    public function index()
    {
        
    
        if (session()->get('role') !== 'admin_region') {
            return redirect()->to('/unauthorized');
        }
    
        $data = [
            'requests' => $this->resetModel->getPendingRequests(),
            'operatorNames' => $this->_getOperatorNames()
        ];
        
        return view('admin/approvals', $data);
    }

    private function _getOperatorNames()
    {
        $operatorModel = new \App\Models\OperatorModel();
        $operators = $operatorModel->findAll();

        $operatorNames = [];
        foreach ($operators as $operator) {
            $operatorNames[$operator['id']] = $operator['nama_operator'];
        }

        return $operatorNames;
    }

public function approveReset($id)
{
    $db = \Config\Database::connect();
    $db->transStart();

    try {
        $request = $this->resetModel->find($id);
        if (!$request) {
            throw new \Exception("Permintaan reset tidak ditemukan");
        }

        // **Pisahkan logika berdasarkan reset_type**
        if ($request['reset_type'] === 'physical') {
            // **Hanya update nozzle, tidak ubah data penjualan**
            $this->nozzleModel->update($request['nozzle_id'], [
                'current_meter' => $request['meter_awal_baru'],
                'last_reset_at' => date('Y-m-d H:i:s')
            ]);

            // **Catat log reset fisik**
            $this->logModel->insert([
                'nozzle_id' => $request['nozzle_id'],
                'kode_spbu' => $request['kode_spbu'],
                'action' => 'physical_reset',
                'old_value' => $request['meter_awal_lama'],
                'new_value' => $request['meter_awal_baru'],
                'notes' => $request['alasan'],
                'created_at' => date('Y-m-d H:i:s')
            ]);

        } else if ($request['reset_type'] === 'correction') {
            // **Handle koreksi data (boleh ubah penjualan)**
            $this->_handleCorrection(
                $request['penjualan_id'],
                $request['meter_awal_baru'],
                $request['alasan']
            );
        }

        // **Update status request (tanpa sentuh data lain)**
        $this->resetModel->update($id, [
            'status' => 'approved',
            'approved_by' => session()->get('user_id'),
            'approved_at' => date('Y-m-d H:i:s')
        ]);

        $db->transComplete();
        return redirect()->to('/admin/approvals')->with('success', 'Reset disetujui');

    } catch (\Exception $e) {
        $db->transRollback();
        return redirect()->back()->with('error', 'Gagal: ' . $e->getMessage());
    }
}

private function _handleCorrection($request)
{
    $penjualanModel = model('PenjualanModel');
    $lastSale = $penjualanModel->where('nozzle_id', $request['nozzle_id'])
        ->orderBy('tanggal', 'DESC')
        ->orderBy('shift', 'DESC')
        ->first();

    if ($lastSale) {
        $penjualanModel->update($lastSale['id'], [
            'meter_akhir' => $request['meter_awal_baru'],
            'is_adjusted' => 1
        ]);
    }
}

    public function rejectReset($id)
    {
        if (session()->get('role') != 'admin_region') {
        return redirect()->to('/unauthorized');
    }

        $this->resetModel->update($id, [
            'status' => 'rejected',
            'approved_by' => session()->get('user_id'),
            'approved_at' => date('Y-m-d H:i:s')
        ]);

        return redirect()->to('/admin/approvals')->with('success', 'Permintaan reset telah ditolak');
    }
    public function history()
    {
        $data = [
            'requests' => $this->resetModel
                ->where('status !=', 'pending')
                ->orderBy('approved_at', 'DESC')
                ->findAll(),
            'operatorNames' => $this->_getOperatorNames()
        ];

        return view('admin/reset_history', $data);
    }
}