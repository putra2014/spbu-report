<?php 
namespace App\Models;

use CodeIgniter\Model;

class AuditModel extends Model
{
    protected $table = 'stok_audit';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'stok_id',
        'tangki_id',
        'kode_spbu',
        'selisih',
        'keterangan',
        'status',
        'verified_by',
        'verified_at',
        'tindakan',
        'catatan_tindakan'
    ];
    
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    // Aturan validasi
    protected $validationRules = [
        'stok_id' => 'required|numeric',
        'tangki_id' => 'required|numeric',
        'selisih' => 'required|numeric',
        'status' => 'required|in_list[pending,verified,rejected]'
    ];

    // Pesan validasi
    protected $validationMessages = [
        'stok_id' => [
            'required' => 'ID Stok harus diisi',
            'numeric' => 'ID Stok harus berupa angka'
        ],
        'selisih' => [
            'required' => 'Nilai selisih harus diisi',
            'numeric' => 'Nilai selisih harus berupa angka'
        ]
    ];

    /**
     * Mendapatkan data audit berdasarkan SPBU
     */
    public function getAuditBySPBU($kode_spbu, $status = null)
    {
        $builder = $this->db->table($this->table)
            ->select('stok_audit.*, tangki.kode_tangki, produk_bbm.nama_produk')
            ->join('tangki', 'tangki.id = stok_audit.tangki_id')
            ->join('produk_bbm', 'produk_bbm.kode_produk = tangki.kode_produk')
            ->where('stok_audit.kode_spbu', $kode_spbu);
        
        if ($status) {
            $builder->where('stok_audit.status', $status);
        }
        
        return $builder->orderBy('created_at', 'DESC')->get()->getResultArray();
    }

    /**
     * Membuat record audit otomatis ketika ada selisih
     */
    public function createAuditAutomatically($stokData)
    {
        // Cek apakah sudah ada audit untuk stok ini
        $existing = $this->where('stok_id', $stokData['id'])->first();
        
        if (!$existing && $stokData['status_loss'] === 'audit') {
            $this->insert([
                'stok_id' => $stokData['id'],
                'tangki_id' => $stokData['tangki_id'],
                'kode_spbu' => $stokData['kode_spbu'],
                'selisih' => $stokData['selisih'],
                'keterangan' => 'Selisih melebihi toleransi',
                'status' => 'pending',
                'created_at' => date('Y-m-d H:i:s')
            ]);
            
            // Kirim notifikasi
            $this->sendAuditNotification($stokData);
            
            return true;
        }
        
        return false;
    }

    /**
     * Mengirim notifikasi audit
     */
    private function sendAuditNotification($stokData)
    {
        $notifikasiModel = new \App\Models\NotifikasiModel();
        $adminRegion = $this->db->table('users')
            ->where('role', 'admin_region')
            ->where('kode_spbu IS NULL')
            ->get()
            ->getRowArray();
        
        if ($adminRegion) {
            $notifikasiModel->insert([
                'user_id' => $adminRegion['id'],
                'tipe' => 'audit_stok',
                'pesan' => "Temuan audit stok di SPBU {$stokData['kode_spbu']}",
                'link' => "/audit/detail/{$stokData['id']}",
                'created_at' => date('Y-m-d H:i:s')
            ]);
        }
    }

    /**
     * Verifikasi temuan audit
     */
    public function verifyAudit($id, $userId, $data)
    {
        return $this->update($id, [
            'status' => $data['status'],
            'verified_by' => $userId,
            'verified_at' => date('Y-m-d H:i:s'),
            'tindakan' => $data['tindakan'],
            'catatan_tindakan' => $data['catatan']
        ]);
    }
}