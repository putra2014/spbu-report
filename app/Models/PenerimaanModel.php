<?php
namespace App\Models;

use CodeIgniter\Model;
helper('shift');
class PenerimaanModel extends Model
{
    protected $table = 'penerimaan';
    protected $primaryKey = 'id';
    protected $tangkiModel;
    protected $afterInsert = ['updateStokAfterInsert'];
    protected $allowedFields = [
    'tanggal', 
    'kode_spbu', 
    'tangki_id', 
    'kode_produk', // Wajib ada
    'nomor_do',  // Wajib ada
    'volume_do', 
    'volume_diterima', 
    'harga_beli',
    'shift',
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
    public function beforeInsert(array $data) {
    $tangki = $this->tangkiModel->find($data['tangki_id']);
    if (($tangki['stok'] + $data['volume']) > $tangki['kapasitas']) {
        throw new \Exception("Melebihi kapasitas tangki");
    }
    return $data;
}
    
    public function afterInsert(array $data)
    {
        // Update stok tangki
        $penerimaan = $this->find($data['id']);
        $this->updateStokTangki($penerimaan['tangki_id'], $penerimaan['volume_diterima']);

        // Update stok_bbm
        $stokModel = new \App\Models\StokModel();
        $stokModel->updateStok($penerimaan['tangki_id'], $penerimaan['volume_diterima'], 'tambah');

        return $data;
    }
    
    protected function updateStokTangki($tangkiId, $volume)
    {
        $builder = $this->db->table('tangki');
        $builder->set('stok', 'stok + ' . $volume, false)
               ->where('id', $tangkiId)
               ->update();
    }

// Di PenerimaanModel.php
protected function updateStokAfterInsert(array $data)
{
    $penerimaan = $this->find($data['id']);
    $stokModel = new \App\Models\StokModel();
    
    // Pastikan ada record stok untuk hari ini
    $stokHariIni = $stokModel->where('tangki_id', $penerimaan['tangki_id'])
                            ->where('tanggal', date('Y-m-d'))
                            ->first();
    
    if (!$stokHariIni) {
        // Jika belum ada, buat record baru dengan stok_awal dari stok terakhir
        $lastStok = $stokModel->where('tangki_id', $penerimaan['tangki_id'])
                            ->orderBy('tanggal', 'DESC')
                            ->orderBy('shift', 'DESC')
                            ->first();
                            
        $stokModel->insert([
            'tanggal' => date('Y-m-d'),
            'shift' => '1', // Shift pertama sebagai default
            'kode_spbu' => $penerimaan['kode_spbu'],
            'tangki_id' => $penerimaan['tangki_id'],
            'stok_awal' => $lastStok ? $lastStok['stok_real'] : 0,
            'penerimaan' => $penerimaan['volume_diterima'],
            'penjualan' => 0,
            'stok_real' => 0,
            'created_by' => session()->get('user_id')
        ]);
    } else {
        // Jika sudah ada, update penerimaan
        $stokModel->update($stokHariIni['id'], [
            'penerimaan' => $stokHariIni['penerimaan'] + $penerimaan['volume_diterima']
        ]);
    }
    
    // Update stok di tabel tangki
    $this->updateStokTangki($penerimaan['tangki_id'], $penerimaan['volume_diterima']);
}

    // Helper method
    protected function getLastStok($tangkiId)
    {
        $stokModel = new \App\Models\StokModel();
        $lastStok = $stokModel->where('tangki_id', $tangkiId)
                             ->orderBy('tanggal', 'DESC')
                             ->orderBy('shift', 'DESC')
                             ->first();

        return $lastStok ? $lastStok['stok_real'] : 0;
    }
    // Tambahkan method berikut ke PenerimaanModel.php yang sudah ada
public function simpanDipping($penerimaanId, $volumeDipping)
{
    $this->db->transStart();
    
    // 1. Update record penerimaan
    $this->update($penerimaanId, [
        'volume_dipping' => $volumeDipping,
        'dipping_by' => session('user_id'),
        'dipping_time' => date('Y-m-d H:i:s'),
        'status' => 'completed'
    ]);
    
    // 2. Update stok real (tanpa mempengaruhi stok teoritis)
    $penerimaan = $this->find($penerimaanId);
    $stokModel = new StokModel();
    $stokModel->insert([
        'tanggal' => $penerimaan['tanggal'],
        'shift' => '0', // 0 untuk stok awal
        'kode_spbu' => $penerimaan['kode_spbu'],
        'tangki_id' => $penerimaan['tangki_id'],
        'stok_awal' => 0,
        'penerimaan' => 0,
        'penjualan' => 0,
        'stok_real' => $volumeDipping,
        'is_initial' => true,
        'created_by' => session('user_id')
    ]);
    
    $this->db->transComplete();
}
}