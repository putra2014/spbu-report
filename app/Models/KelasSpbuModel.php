<?php
namespace App\Models;
use CodeIgniter\Model;

class KelasSpbuModel extends Model
{
    protected $table = 'kelas_spbu';
    protected $primaryKey = 'id';
    protected $allowedFields = ['nama_kelas'];
}