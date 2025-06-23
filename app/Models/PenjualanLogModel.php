<?php

namespace App\Models;

use CodeIgniter\Model;

class PenjualanLogModel extends Model
{
    protected $table = 'penjualan_harian_log';
    protected $allowedFields = [
        'tanggal', 'nozzle_id', 'shift',
        'meter_awal', 'meter_akhir',
        'volume', 'harga_jual', 'total_penjualan','action', 'reset type',
        'operator_id', 'alasan_reset', 'approved_by', 'approved_at','kode_spbu'
    ];
}
