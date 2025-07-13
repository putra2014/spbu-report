<?php
namespace App\Models;

use CodeIgniter\Model;

class TangkiModel extends Model
{
    protected $table = 'tangki';
    protected $primaryKey = 'id';
    protected $allowedFields = ['kode_spbu', 'kode_produk','kode_tangki','jenis_tangki', 'kapasitas','dead_stock','stok'];

    public function getTangkiWithProduk($kode_spbu = null)
    {
        $builder = $this->select('tangki.*, produk_bbm.nama as jenis_bbm')
                       ->join('produk_bbm', 'produk_bbm.id = tangki.kode_produk');
        
        if ($kode_spbu) {
            $builder->where('kode_spbu', $kode_spbu);
        }
        
        return $builder->findAll();
    }
    public function getTangkiBySPBU($kode_spbu)
    {
    return $this->select('tangki.id, tangki.kode_tangki, produk_bbm.nama_produk')
               ->join('produk_bbm', 'produk_bbm.kode_produk = tangki.kode_produk')
               ->where('kode_spbu', $kode_spbu)
               ->findAll();
    }
    public function updateStok($id, $volume, $action = 'tambah')
    {
        $tangki = $this->find($id);
        
        if ($action === 'tambah') {
            $stokBaru = $tangki['stok'] + $volume;
        } else {
            $stokBaru = $tangki['stok'] - $volume;
        }
        
        if ($stokBaru < 0 || $stokBaru > $tangki['kapasitas']) {
            return false;
        }
        
        return $this->update($id, ['stok' => $stokBaru]);
    }
}
