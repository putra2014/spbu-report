<?php

namespace App\Models;

use CodeIgniter\Model;

class MeterResetLogModel extends Model
{
    protected $table = 'meter_reset_logs';
    protected $allowedFields = [
        'nozzle_id', 'old_meter', 'new_meter', 'reset_type',
        'reason', 'request_id', 'created_at'
    ];
}