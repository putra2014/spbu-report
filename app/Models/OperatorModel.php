<?php

namespace App\Models;

use CodeIgniter\Model;

class OperatorModel extends Model
{
    protected $table = 'operator';
    protected $primaryKey = 'id';
    protected $allowedFields = ['kode_spbu', 'nama_operator', 'no_hp', 'shift'];
    protected $useTimestamps = true;
}
