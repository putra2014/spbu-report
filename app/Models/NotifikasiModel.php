<?php
namespace App\Models;

use CodeIgniter\Model;

class NotifikasiModel extends Model
{
    protected $table = 'notifikasi';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'user_id',
        'tipe',
        'pesan',
        'status_baca',
        'link',
        'created_at'
    ];

    public function getUnreadNotifications($userId)
    {
        return $this->where('user_id', $userId)
                   ->where('status_baca', 'belum')
                   ->orderBy('created_at', 'DESC')
                   ->findAll();
    }
}