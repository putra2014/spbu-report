<?php

namespace App\Models;

use CodeIgniter\Model;

class MeterResetRequestModel extends Model
{
    protected $table = 'meter_reset_request';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $allowedFields = [
        'penjualan_id',
        'nozzle_id', 
        'kode_spbu',
        'status',
        'catatan',
        'meter_awal_lama',
        'meter_awal_baru',
        'requested_by',
        'alasan',
        'approved_by',
        'approved_at',
        'bukti_reset',
        'reset_type'
    ];
    
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    // Aturan validasi
    protected $validationRules = [
        'nozzle_id' => 'required|numeric',
        'meter_awal_baru' => 'required|decimal',
        'alasan' => 'required|min_length[5]',
        'reset_type' => 'required|in_list[physical,correction]',
        'bukti_reset' => 'max_size[bukti_reset,2048]|is_image[bukti_reset]',
        'penjualan_id' => [
            'rules' => 'required_if[reset_type,correction]|numeric',
            'errors' => [
                'required_if' => 'Pilih penjualan yang akan dikoreksi'
            ]
        ]
    ];

    // Pesan validasi custom
    protected $validationMessages = [
        'meter_awal_baru' => [
            'decimal' => 'Format meter harus angka desimal'
        ]
    ];

    // Validasi sebelum insert/update
    protected $beforeInsert = ['validateReset'];
    protected $beforeUpdate = ['validateReset'];

    protected function validateReset(array $data)
    {
        if ($data['data']['reset_type'] === 'correction') {
            $penjualanModel = new \App\Models\PenjualanModel();
            $penjualan = $penjualanModel->find($data['data']['penjualan_id']);
            
            if ($data['data']['meter_awal_baru'] < $penjualan['meter_awal']) {
                $this->validation->setError(
                    'meter_awal_baru', 
                    'Untuk koreksi data, meter baru ('.number_format($data['data']['meter_awal_baru'], 2).') 
                    tidak boleh lebih kecil dari meter awal ('.number_format($penjualan['meter_awal'], 2).')'
                );
                return false;
            }
        }
        return $data;
    }

    public function getPendingRequests() {
        return $this->select('meter_reset_request.*, nozzle.kode_nozzle, produk_bbm.nama_produk, users.username as nama_admin_spbu')
            ->join('nozzle', 'nozzle.id = meter_reset_request.nozzle_id')
            ->join('produk_bbm', 'produk_bbm.kode_produk = nozzle.kode_produk', 'left')
            ->join('users', 'users.id = meter_reset_request.requested_by')
            ->where('meter_reset_request.status', 'pending')
            ->orderBy('created_at', 'DESC')
            ->findAll();
    }
    
    public function getHistory() {
        return $this->select('meter_reset_request.*, nozzle.kode_nozzle, users.username as nama_admin_region')
            ->join('nozzle', 'nozzle.id = meter_reset_request.nozzle_id')
            ->join('users', 'users.id = meter_reset_request.approved_by')
            ->where('meter_reset_request.status', 'approved')
            ->orderBy('approved_at', 'DESC')
            ->findAll();
    }

    // Untuk mendapatkan data reset berdasarkan penjualan_id
    public function getByPenjualanId($penjualan_id)
    {
        return $this->where('penjualan_id', $penjualan_id)
                   ->first();
    }
}