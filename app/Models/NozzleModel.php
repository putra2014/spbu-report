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
        'nozzle_no', // Tambahkan
        'dispenser_id',
        'kode_tangki',
        'kode_produk',
        'current_meter',
        'initial_meter',
        'last_reset_at',
        'last_meter_at', // Tambahkan
        'status',
        'catatan',
        'is_locked',
        'lock_reason'
    ];
    public function beforeInsert(array $data)
    {
        // Validasi tangki sesuai SPBU
        $tangki = model('TangkiModel')
            ->where('kode_tangki', $data['kode_tangki'])
            ->where('kode_spbu', $data['kode_spbu'])
            ->first();

        if (!$tangki) {
            throw new \RuntimeException('Tangki tidak ditemukan atau tidak sesuai SPBU');
        }

        return $data;
    }
    public function withNozzles()
    {
        return $this->select('dispenser.*, COUNT(nozzle.id) as total_nozzles')
                   ->join('nozzle', 'nozzle.dispenser_id = dispenser.id', 'left')
                   ->groupBy('dispenser.id');
    }
}
