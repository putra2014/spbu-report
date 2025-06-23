<?php
namespace App\Models;

use CodeIgniter\Model;

class WilayahModel extends Model
{
    protected $table = 'wilayah';
    protected $primaryKey = 'id';
    protected $allowedFields = ['nama_wilayah'];
}
