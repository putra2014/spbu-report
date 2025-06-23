<?php
namespace App\Models;

use CodeIgniter\Model;

class HargaLogModel extends Model
{
    protected $table = 'harga_bbm_log';
    protected $allowedFields = ['harga_bbm_id', 'produk_id', 'harga_beli', 'harga_jual'];
    protected $useTimestamps = true;
    protected $createdField = 'changed_at';
}
