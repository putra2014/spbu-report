<?php
namespace App\Models;
use CodeIgniter\Model;

class TypeSpbuModel extends Model
{
    protected $table = 'type_spbu';
    protected $primaryKey = 'id';
    protected $allowedFields = ['nama_type'];
}
