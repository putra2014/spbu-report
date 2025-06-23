<?php
namespace App\Models;

use CodeIgniter\Model;

class EnhancedResetModel extends Model
{
    protected $table = 'meter_reset_logs';

    public function logReset($requestData)
    {
        $this->insert([
            'nozzle_id' => $requestData['nozzle_id'],
            'old_meter' => $requestData['meter_awal_lama'],
            'new_meter' => $requestData['meter_awal_baru'],
            'reset_type' => $requestData['reset_type'],
            'reason' => $requestData['alasan'],
            'request_id' => $requestData['id'],
            'created_at' => date('Y-m-d H:i:s')
        ]);
    }

    public function getResetsByNozzle($nozzle_id)
    {
        return $this->where('nozzle_id', $nozzle_id)
                   ->orderBy('created_at', 'DESC')
                   ->findAll();
    }
}