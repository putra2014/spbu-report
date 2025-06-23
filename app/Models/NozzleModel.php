<?php

namespace App\Models;

use CodeIgniter\Model;

class NozzleModel extends Model
{
    protected $table = 'nozzle';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'kode_spbu',
        'kode_nozzle',
        'dispenser_id',
        'kode_tangki',
        'kode_produk',
        'current_meter',
        'initial_meter', // Pastikan ini ada
        'last_reset_at',
        'status',
        'catatan'
    ];
}
