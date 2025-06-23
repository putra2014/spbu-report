<?php

namespace App\Models;
use CodeIgniter\Model;

class LogDispenserModel extends Model
{
    protected $table = 'log_dispenser';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'dispenser_id',
        'kode_spbu',
        'kode_dispenser',
        'merek_dispenser',
        'jumlah_nozzle',
        'type_dispenser',
        'tgl_kalibrasi_berakhir',
        'updated_by',
        'updated_at'
    ];
}
