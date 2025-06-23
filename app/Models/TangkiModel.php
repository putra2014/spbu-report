<?php
namespace App\Models;

use CodeIgniter\Model;

class TangkiModel extends Model
{
    protected $table = 'tangki';
    protected $primaryKey = 'id';
    protected $allowedFields = ['kode_spbu', 'kode_produk','kode_tangki','jenis_tangki', 'kapasitas','dead_stock'];

    public function getTangkiWithProduk($kode_spbu = null)
    {
        $builder = $this->select('tangki.*, produk_bbm.nama as jenis_bbm')
                       ->join('produk_bbm', 'produk_bbm.id = tangki.kode_produk');
        
        if ($kode_spbu) {
            $builder->where('kode_spbu', $kode_spbu);
        }
        
        return $builder->findAll();
    }
}
