<?php
namespace App\Models;

use CodeIgniter\Model;

class ProdukModel extends Model
{
    protected $table = 'produk_bbm';
    protected $allowedFields = ['kode_produk', 'nama_produk', 'kategori', 'jenis'];
    protected $useTimestamps = false;

    public function getProductName($id)
    {
        if (empty($id)) return '-';
        $product = $this->find($id);
        return $product ? $product['nama_produk'] : 'Produk tidak ditemukan';
    }
}