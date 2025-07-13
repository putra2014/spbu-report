<?php

namespace App\Models;

use CodeIgniter\Model;

class PenjualanLogModel extends Model
{
    protected $table = 'penjualan_harian_log';
    protected $allowedFields = [
        'tanggal', 
        'kode_spbu',
        'nozzle_id', 
        'shift',
        'meter_awal', 
        'meter_akhir',
        'volume', 
        'harga_jual', 
        'total_penjualan',
        'operator_id', 
        'action', 
        'executed_by', 
        'notes',
        'approval_status',
        'approved_by',
        'approved_at'
    ];
}
