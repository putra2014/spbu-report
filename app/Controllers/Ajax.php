<?php

namespace App\Controllers;

use App\Models\ProvinsiModel;
use App\Models\KabupatenModel;
use App\Models\AreaModel;

class Ajax extends BaseController
{
    public function provinsiByWilayah($wilayah_id)
    {
        $model = new ProvinsiModel();
        $data = $model->where('wilayah_id', $wilayah_id)->findAll();
        foreach ($data as $row) {
            echo "<option value='{$row['id']}'>{$row['nama']}</option>";
        }
    }

    public function areaByWilayah($wilayah_id)
    {
        $model = new \App\Models\AreaModel();
        $data = $model->where('wilayah_id', $wilayah_id)->findAll();
    
        foreach ($data as $row) {
            echo "<option value='{$row['id']}'>{$row['nama_area']}</option>";
        }
    }


    public function kabupatenByProvinsi($provinsi_id)
    {
        $model = new KabupatenModel();
        $data = $model->where('provinsi_id', $provinsi_id)->findAll();
        foreach ($data as $row) {
            echo "<option value='{$row['id']}'>{$row['nama_kabupaten']}</option>";
        }
    }

    public function kabupatenByArea($area_id)
    {
        $model = new KabupatenModel();
        $data = $model->where('area_id', $area_id)->findAll();
        foreach ($data as $row) {
            echo "<option value='{$row['id']}'>{$row['nama_kabupaten']}</option>";
        }
    }
    public function jumlahNozzle($dispenserId)
    {
        $model = new \App\Models\NozzleModel();
        $dispenserModel = new \App\Models\DispenserModel();
    
        $jumlah = $model->where('dispenser_id', $dispenserId)->countAllResults();
        $dispenser = $dispenserModel->find($dispenserId);
    
        return $this->response->setJSON([
            'jumlah' => $jumlah,
            'maksimal' => $dispenser['jumlah_nozzle'] ?? 0,
        ]);
    }

}
