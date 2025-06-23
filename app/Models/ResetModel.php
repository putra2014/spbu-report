<?php
namespace App\Models;
use CodeIgniter\Model;

class ResetModel extends Model {
    protected $table = 'nozzle_reset_logs';
    protected $allowedFields = [
        'nozzle_id', 'kode_spbu', 'old_meter', 'new_meter', 
        'reason', 'notes', 'status', 'requested_by', 'approved_by'
    ];
}