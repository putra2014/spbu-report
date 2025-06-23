<?php
namespace App\Models;
use CodeIgniter\Model;

class KodeNozzleModel extends Model
{
    protected $table = 'kode_nozzle_master';
    protected $primaryKey = 'id';
    protected $allowedFields = ['kode_nozzle', 'keterangan'];
}
