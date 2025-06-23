<?php

namespace App\Models;

use CodeIgniter\Model;

class PerusahaanModel extends Model
{
    protected $table = 'perusahaan';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'kode_spbu',
        'nama_perusahaan',
        'nama_pengusaha',
        'jabatan',
        'alamat',
        'kabupaten_kota',
        'provinsi',
        'no_handphone'
    ];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    public function getBySpbu($kode_spbu)
    {
        return $this->where('kode_spbu', $kode_spbu)->findAll();
    }

    public function search($keyword)
    {
        return $this->table('perusahaan')->like('nama_perusahaan', $keyword)
            ->orLike('nama_pengusaha', $keyword)
            ->orLike('kabupaten_kota', $keyword);
    }
}