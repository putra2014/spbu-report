<?php
namespace App\Models;

use CodeIgniter\Model;

class PenerimaanModel extends Model
{
    protected $table = 'penerimaan';
    protected $primaryKey = 'id';
    protected $tangkiModel;
    protected $allowedFields = [
    'tanggal', 
    'kode_spbu', 
    'tangki_id', 
    'kode_produk', // Wajib ada
    'nomor_do',  // Wajib ada
    'volume_do', 
    'volume_diterima', 
    'harga_beli',
    'supir'
    

    
];
    public function __construct()
    {
        parent::__construct();
        $this->tangkiModel = new \App\Models\TangkiModel();
    }
    protected $beforeInsert = ['setProdukFromTangki'];
    protected $beforeUpdate = ['setProdukFromTangki'];

    protected function setProdukFromTangki(array $data)
    {
        if (isset($data['data']['tangki_id'])) {
            $tangki = $this->tangkiModel->find($data['data']['tangki_id']);
            if ($tangki) {
                $data['data']['kode_produk'] = $tangki['kode_produk'];
            }
        }
        return $data;
    }

    public function validatePenerimaan($data)
    {
        $validation = \Config\Services::validation();
        $validation->setRules([
            'kode_produk' => 'required|integer',
            'nomor_do' => 'required|string|max_length[50]',
            'volume_do' => 'required|numeric',
            'volume_diterima' => 'required|numeric'
        ]);

        return $validation->run($data);
    }
    public function getPenerimaanWithDetails($kode_spbu = null)
    {
        $builder = $this->select('penerimaan.*, 
                               spbu.nama_spbu, 
                               tangki.kode_tangki, 
                               produk_bbm.nama_produk as jenis_bbm,
                               IFNULL(penerimaan.volume_do, 0) as volume_do,
                               IFNULL(penerimaan.volume_diterima, 0) as volume_diterima,
                               IFNULL(penerimaan.harga_beli, 0) as harga_beli')
                       ->join('spbu', 'spbu.id = penerimaan.kode_spbu', 'left')
                       ->join('tangki', 'tangki.id = penerimaan.tangki_id', 'left')
                       ->join('produk_bbm', 'produk_bbm.id = penerimaan.kode_produk', 'left');

        if ($kode_spbu !== null) {
            $builder->where('penerimaan.kode_spbu', $kode_spbu);
        }

        return $builder->orderBy('penerimaan.tanggal', 'DESC')->findAll();
    }
    public function getAllWithProduk()
    {
        return $this->select('penerimaan.*, produk_bbm.nama_produk')
                    ->join('produk_bbm', 'produk_bbm.kode_produk = penerimaan.kode_produk', 'left')
                    ->findAll();
    }

    public function getByIdWithProduk($id)
    {
        return $this->select('penerimaan.*, produk_bbm.nama_produk')
                    ->join('produk_bbm', 'produk_bbm.kode_produk = penerimaan.kode_produk', 'left')
                    ->where('penerimaan.id', $id)
                    ->first();
    }
}