<?php
namespace App\Models;

use CodeIgniter\Model;

class NozzleTestModel extends Model
{
    protected $table = 'nozzle_tests';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'penjualan_id',
        'kode_spbu',
        'nozzle_id',
        'shift',
        'tanggal',
        'volume_penjualan',
        'volume_test',
        'created_by'
    ];

    protected $beforeInsert = ['updateTangkiStock'];
    protected $beforeUpdate = ['updateTangkiStock'];

    /**
     * Update stok tangki setelah test nozzle
     */
    protected function updateTangkiStock(array $data)
    {
        if (!isset($data['data']['nozzle_id']) || !isset($data['data']['volume_test'])) {
            return $data;
        }

        // Dapatkan data nozzle dan tangki terkait
        $nozzle = model('NozzleModel')->find($data['data']['nozzle_id']);
        $tangki = model('TangkiModel')
            ->where('kode_spbu', $data['data']['kode_spbu'])
            ->where('kode_tangki', $nozzle['kode_tangki'])
            ->first();

        if ($tangki) {
            // Hitung stok baru (tambah volume test ke tangki)
            $newStock = $tangki['kapasitas'] + $data['data']['volume_test'];
            
            // Update stok tangki
            model('TangkiModel')->update($tangki['id'], ['kapasitas' => $newStock]);
        }

        return $data;
    }

    /**
     * Get tests by SPBU
     */
    public function getBySpbu($kode_spbu, $limit = null)
    {
        $builder = $this->select('nozzle_tests.*, nozzle.kode_nozzle, produk_bbm.nama_produk')
            ->join('nozzle', 'nozzle.id = nozzle_tests.nozzle_id')
            ->join('produk_bbm', 'produk_bbm.kode_produk = nozzle.kode_produk')
            ->where('nozzle_tests.kode_spbu', $kode_spbu)
            ->orderBy('nozzle_tests.tanggal', 'DESC')
            ->orderBy('nozzle_tests.shift', 'DESC');

        if ($limit) {
            $builder->limit($limit);
        }

        return $builder->findAll();
    }

    /**
     * Get test summary for dashboard
     */
    public function getTestSummary($kode_spbu)
    {
        return $this->select('
                COUNT(*) as total_tests,
                SUM(volume_test) as total_volume,
                MAX(tanggal) as last_test_date
            ')
            ->where('kode_spbu', $kode_spbu)
            ->first();
    }

    /**
     * Get tests by nozzle
     */
    public function getByNozzle($nozzle_id, $limit = 10)
    {
        return $this->where('nozzle_id', $nozzle_id)
            ->orderBy('tanggal', 'DESC')
            ->limit($limit)
            ->findAll();
    }
}