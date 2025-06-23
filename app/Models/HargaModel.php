<?php
namespace App\Models;

use CodeIgniter\Model;

class HargaModel extends Model
{
    protected $table = 'harga_bbm';
    protected $allowedFields = ['kode_produk', 'harga_beli', 'harga_jual']; // Ubah produk_id menjadi kode_produk
    protected $useTimestamps = true;
    protected $updatedField = 'updated_at';

    public function getByKodeProduk($kode_produk)
    {
        return $this->where('kode_produk', $kode_produk)
                   ->orderBy('created_at', 'DESC')
                   ->first();
    }
}