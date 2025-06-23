<?php
namespace App\Models;
use CodeIgniter\Model;

class JenisSpbuModel extends Model
{
    protected $table = 'jenis_spbu';
    protected $primaryKey = 'id';
    protected $allowedFields = ['nama_jenis'];
}
