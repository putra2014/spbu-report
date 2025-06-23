<?php

namespace App\Models;
use CodeIgniter\Model;

class DispenserModel extends Model
{
    protected $table = 'dispenser';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'kode_spbu',
        'kode_dispenser',
        'merek_dispenser',
        'jumlah_nozzle',
        'type_dispenser',
        'tgl_kalibrasi_berakhir'
    ];
}
