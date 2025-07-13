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

    public function withNozzles()
    {
        return $this->select('dispenser.*, nozzle.kode_nozzle, nozzle.current_meter')
                   ->join('nozzle', 'nozzle.dispenser_id = dispenser.id', 'left')
                   ->where('dispenser.kode_spbu', session()->get('kode_spbu'))
                   ->findAll();
    }
}
