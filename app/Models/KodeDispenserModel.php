<?php
namespace App\Models;
use CodeIgniter\Model;

class KodeDispenserModel extends Model
{
    protected $table = 'kode_dispenser_master';
    protected $primaryKey = 'id';
    protected $allowedFields = ['kode_dispenser', 'keterangan'];
}
