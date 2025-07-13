<?php
namespace App\Services;

use App\Models\PenjualanModel;

class ResetService
{
    protected $penjualanModel;

    public function __construct()
    {
        $this->penjualanModel = new PenjualanModel();
    }

    public function adjustSubsequentShifts($penjualan_id, $new_meter)
    {
        $currentSale = $this->penjualanModel->find($penjualan_id);
        $nextSales = $this->penjualanModel
            ->where('nozzle_id', $currentSale['nozzle_id'])
            ->where('tanggal >=', $currentSale['tanggal'])
            ->where('shift >', $currentSale['shift'])
            ->orderBy('tanggal', 'ASC')
            ->orderBy('shift', 'ASC')
            ->findAll();

        foreach ($nextSales as $nextSale) {
            $this->penjualanModel->update($nextSale['id'], [
                'meter_awal' => $new_meter,
                'volume' => $nextSale['meter_akhir'] - $new_meter
            ]);
            $new_meter = $nextSale['meter_akhir'];
        }
    }
}