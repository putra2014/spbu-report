<?php

namespace App\Models;

use CodeIgniter\Model;

class PenjualanModel extends Model
{
    protected $table = 'penjualan_harian';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'kode_spbu',
        'tanggal',
        'shift',
        'nozzle_id',
        'meter_awal',
        'meter_akhir',
        'currentMeter',
        'volume',
        'harga_jual',
        'total_penjualan',
        'operator_id',
        'approved_by',
        'approval_status'
    ];

    public function getPenjualanHarian($kode_spbu)
    {
        return $this->select('penjualan_harian.*, nozzle.kode_nozzle, produk_bbm.nama_produk, operator.nama_operator')
            ->join('nozzle', 'nozzle.id = penjualan_harian.nozzle_id')
            ->join('produk_bbm', 'produk_bbm.kode_produk = nozzle.kode_produk', 'left')
            ->join('operator', 'operator.id = penjualan_harian.operator_id', 'left')
            ->where('penjualan_harian.kode_spbu', $kode_spbu)
            ->orderBy('tanggal', 'DESC')
            ->orderBy('shift', 'ASC')
            ->findAll();
    }

    public function getPenjualanById($id, $kode_spbu)
    {
        return $this->select('penjualan_harian.*, nozzle.kode_nozzle, produk_bbm.nama_produk, operator.nama_operator')
            ->join('nozzle', 'nozzle.id = penjualan_harian.nozzle_id')
            ->join('produk_bbm', 'produk_bbm.kode_produk = nozzle.kode_produk', 'left')
            ->join('operator', 'operator.id = penjualan_harian.operator_id', 'left')
            ->where('penjualan_harian.id', $id)
            ->where('penjualan_harian.kode_spbu', $kode_spbu)
            ->first();
    }


    public function getAll()
    {
        return $this->select('penjualan_harian.*, nozzle.kode_nozzle, produk_bbm.nama_produk, operator.nama_operator')
            ->join('nozzle', 'nozzle.id = penjualan_harian.nozzle_id')
            ->join('produk_bbm', 'produk_bbm.kode_produk = nozzle.kode_produk', 'left')
            ->join('operator', 'operator.id = penjualan_harian.operator_id', 'left')
            ->orderBy('tanggal DESC, shift ASC')
            ->findAll();
    }
    // File: app/Models/PenjualanModel.php
    public function getAdjustedVolume($nozzle_id, $start_date, $end_date)
    {
        $sales = $this->where('nozzle_id', $nozzle_id)
            ->where('tanggal >=', $start_date)
            ->where('tanggal <=', $end_date)
            ->orderBy('tanggal', 'ASC')
            ->orderBy('shift', 'ASC')
            ->findAll();

        $resets = $this->resetLogModel
            ->where('nozzle_id', $nozzle_id)
            ->where('created_at >=', $start_date)
            ->where('created_at <=', $end_date)
            ->orderBy('created_at', 'ASC')
            ->findAll();

        $result = [];
        $currentMeter = null;

        foreach ($sales as $sale) {
            // Find applicable reset
            $applicableReset = $this->findApplicableReset($resets, $sale['tanggal']);

            $startMeter = $applicableReset ? $applicableReset['new_meter'] : $sale['meter_awal'];
            $endMeter = $sale['meter_akhir'];

            $result[] = [
                'tanggal' => $sale['tanggal'],
                'shift' => $sale['shift'],
                'volume' => $endMeter - $startMeter,
                'is_adjusted' => ($applicableReset !== null)
            ];
        }

        return $result;
    }
    // File: app/Models/PenjualanModel.php (Tambahan)
    public function correctSaleData($penjualan_id, $new_meter, $approved_by, $alasan)
    {
        $db = \Config\Database::connect();
        $db->transStart();

        try {
            $sale = $this->find($penjualan_id);

            // 1. Update data penjualan
            $this->update($penjualan_id, [
                'meter_akhir' => $new_meter,
                'volume' => $new_meter - $sale['meter_awal'],
                'is_adjusted' => 1
            ]);
            $sale = $this->find($penjualan_id);

            $this->db->table('nozzle')
                ->where('id', $sale['nozzle_id'])
                ->update(['current_meter' => $new_meter]);

            // 2. Auto-update shift berikutnya (menggunakan fungsi yang sudah ada)
            $this->_adjustSubsequentShifts($penjualan_id, $new_meter);

            // 3. Catat di log (gunakan logModel yang sudah ada)
            $this->logModel->insert([
                'penjualan_id' => $penjualan_id,
                'action' => 'correction',
                'old_value' => json_encode($sale),
                'new_value' => json_encode($this->find($penjualan_id)),
                'created_by' => $approved_by,
                'notes' => $alasan
            ]);

            $db->transComplete();
        } catch (\Exception $e) {
            $db->transRollback();
            throw $e;
        }
    }
}
