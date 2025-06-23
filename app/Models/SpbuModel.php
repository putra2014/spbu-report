<?php

namespace App\Models;

use CodeIgniter\Model;

class SpbuModel extends Model
{
    protected $table = 'spbu';
    protected $allowedFields = [
        'kode_spbu', 'nama_spbu', 'nama_perusahaan', 'alamat_spbu',
        'telp_spbu', 'nama_direktur', 'telp_direktur','jenis_spbu_id', 'type_spbu_id', 'kelas_spbu_id',
        'nama_manager', 'telp_manager', 'sold_to_party', 'ship_to_party',
        'wilayah_id', 'area_id', 'provinsi_id', 'kabupaten_id',
        'alamat_lengkap', 'jumlah_tangki', 'jumlah_dispenser',
        'latitude', 'longitude', 'keterangan'
    ];

    protected $useTimestamps = true;
}
