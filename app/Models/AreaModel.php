<?php
namespace App\Models;

use CodeIgniter\Model;

class AreaModel extends Model
{
    protected $table = 'area';
    protected $primaryKey = 'id';
    protected $allowedFields = ['wilayah_id', 'nama_area'];

    public function withWilayah()
    {
        return $this->select('area.*, wilayah.nama_wilayah')
                    ->join('wilayah', 'wilayah.id = area.wilayah_id');
    }
}
