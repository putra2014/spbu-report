<?php
namespace App\Models;

use CodeIgniter\Model;

class StokHistoryModel extends Model
{
    protected $table = 'stok_history';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'stok_id', 'field_changed', 'old_value', 
        'new_value', 'changed_by', 'catatan'
    ];

    public function logChange($stokId, $field, $oldValue, $newValue, $userId, $notes = '')
    {
        return $this->insert([
            'stok_id' => $stokId,
            'field_changed' => $field,
            'old_value' => $oldValue,
            'new_value' => $newValue,
            'changed_by' => $userId,
            'catatan' => $notes
        ]);
    }

    public function getHistory($stokId)
    {
        return $this->db->table($this->table)
            ->select('stok_history.*, users.username as changed_by_name')
            ->join('users', 'users.id = stok_history.changed_by')
            ->where('stok_id', $stokId)
            ->orderBy('changed_at', 'DESC')
            ->get()
            ->getResultArray();
    }
}