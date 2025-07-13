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
    'bukti_reset' => 'permit_empty|max_size[bukti_reset,2048]|is_image[bukti_reset]',
    'penjualan_id' => [
        'rules' => 'permit_empty|numeric',
        'errors' => [
            'numeric' => 'ID Penjualan harus angka'
        ]
    ]
];

// Hapus $validationMessages jika ada





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