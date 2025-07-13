<?php
namespace App\Models;

use CodeIgniter\Model;

class StokModel extends Model
{
    protected $table = 'stok_bbm';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'tanggal', 'shift', 'kode_spbu', 'tangki_id', 'stok_awal', 
        'penerimaan', 'penjualan', 'evaporation_loss', 'stok_real',
        'keterangan_loss', 'catatan', 'created_by', 'is_closed',
        'closing_time', 'is_closing', 'closing_shift'
    ];

    // Method untuk mencatat penerimaan BBM
    public function catatPenerimaan($tangkiId, $volumeDo, $volumeDiterima)
    {
        $kode_spbu = session()->get('kode_spbu');
        $shift = getCurrentShift();
        
        $this->db->transStart();
        
        try {
            // 1. Cek apakah sudah ada record stok hari ini
            $existingStok = $this->where('tangki_id', $tangkiId)
                                ->where('tanggal', date('Y-m-d'))
                                ->where('shift', $shift)
                                ->first();

            // 2. Jika belum ada, buat record baru
            if (!$existingStok) {
                $lastStok = $this->where('tangki_id', $tangkiId)
                                ->orderBy('tanggal', 'DESC')
                                ->orderBy('shift', 'DESC')
                                ->first();

                $stokAwal = $lastStok ? $lastStok['stok_real'] : 0;

                $this->insert([
                    'tanggal' => date('Y-m-d'),
                    'shift' => $shift,
                    'kode_spbu' => $kode_spbu,
                    'tangki_id' => $tangkiId,
                    'stok_awal' => $stokAwal,
                    'penerimaan' => $volumeDo,
                    'penjualan' => 0,
                    'stok_real' => $volumeDiterima,
                    'created_by' => session()->get('user_id')
                ]);
            } else {
                // 3. Jika sudah ada, update penerimaan dan stok real
                $this->update($existingStok['id'], [
                    'penerimaan' => $volumeDo,
                    'stok_real' => $volumeDiterima
                ]);
            }

            // 4. Update stok di tabel tangki
            $this->db->table('tangki')
                    ->where('id', $tangkiId)
                    ->update(['stok' => $volumeDiterima]);

            $this->db->transComplete();
            return true;
        } catch (\Exception $e) {
            $this->db->transRollback();
            log_message('error', 'Gagal mencatat penerimaan: '.$e->getMessage());
            return false;
        }
    }

    // Method untuk mencatat penjualan
    public function catatPenjualan($tangkiId, $volume)
    {
        $kode_spbu = session()->get('kode_spbu');
        $shift = getCurrentShift();
        
        $this->db->transStart();
        
        try {
            // 1. Cek apakah sudah ada record stok hari ini
            $existingStok = $this->where('tangki_id', $tangkiId)
                                ->where('tanggal', date('Y-m-d'))
                                ->where('shift', $shift)
                                ->first();

            // 2. Jika belum ada, buat record baru
            if (!$existingStok) {
                $lastStok = $this->where('tangki_id', $tangkiId)
                                ->orderBy('tanggal', 'DESC')
                                ->orderBy('shift', 'DESC')
                                ->first();

                $stokAwal = $lastStok ? $lastStok['stok_real'] : 0;

                $this->insert([
                    'tanggal' => date('Y-m-d'),
                    'shift' => $shift,
                    'kode_spbu' => $kode_spbu,
                    'tangki_id' => $tangkiId,
                    'stok_awal' => $stokAwal,
                    'penerimaan' => 0,
                    'penjualan' => $volume,
                    'stok_real' => $stokAwal - $volume,
                    'created_by' => session()->get('user_id')
                ]);
            } else {
                // 3. Jika sudah ada, update penjualan dan stok real
                $this->update($existingStok['id'], [
                    'penjualan' => $existingStok['penjualan'] + $volume,
                    'stok_real' => $existingStok['stok_real'] - $volume
                ]);
            }

            $this->db->transComplete();
            return true;
        } catch (\Exception $e) {
            $this->db->transRollback();
            log_message('error', 'Gagal mencatat penjualan: '.$e->getMessage());
            return false;
        }
    }

    // Method untuk closing harian
    public function closingHarian($kode_spbu, $tanggal, $stokReals)
    {
        $this->db->transStart();
        
        try {
            // 1. Get semua tangki di SPBU
            $tangkiList = $this->db->table('tangki')
                                 ->where('kode_spbu', $kode_spbu)
                                 ->get()
                                 ->getResultArray();

            foreach ($tangkiList as $tangki) {
                if (!isset($stokReals[$tangki['id']])) continue;

                // 2. Hitung total penerimaan dan penjualan per tangki
                $totalPenerimaan = $this->db->table('penerimaan')
                                          ->selectSum('volume_diterima', 'total')
                                          ->where('kode_spbu', $kode_spbu)
                                          ->where('tanggal', $tanggal)
                                          ->where('tangki_id', $tangki['id'])
                                          ->get()
                                          ->getRow()->total ?? 0;

                $totalPenjualan = $this->db->table('penjualan_harian ph')
                                         ->selectSum('ph.volume', 'total')
                                         ->join('nozzle n', 'n.id = ph.nozzle_id')
                                         ->where('ph.kode_spbu', $kode_spbu)
                                         ->where('DATE(ph.tanggal)', $tanggal)
                                         ->where('n.kode_tangki', $tangki['kode_tangki'])
                                         ->get()
                                         ->getRow()->total ?? 0;

                // 3. Buat record closing
                $this->insert([
                    'tanggal' => $tanggal,
                    'shift' => '0', // 0 untuk closing harian
                    'closing_shift' => getCurrentShift(),
                    'kode_spbu' => $kode_spbu,
                    'tangki_id' => $tangki['id'],
                    'stok_awal' => $this->_getStokAwalHariIni($tangki['id'], $tanggal),
                    'penerimaan' => $totalPenerimaan,
                    'penjualan' => $totalPenjualan,
                    'stok_real' => $stokReals[$tangki['id']],
                    'is_closing' => true,
                    'created_by' => session()->get('user_id')
                ]);

                // 4. Update stok di tabel tangki
                $this->db->table('tangki')
                        ->where('id', $tangki['id'])
                        ->update(['stok' => $stokReals[$tangki['id']]]);
            }

            $this->db->transComplete();
            return true;
        } catch (\Exception $e) {
            $this->db->transRollback();
            log_message('error', 'Gagal closing harian: '.$e->getMessage());
            return false;
        }
    }

    private function _getStokAwalHariIni($tangkiId, $tanggal)
    {
        $lastStok = $this->where('tangki_id', $tangkiId)
                        ->where('tanggal <', $tanggal)
                        ->orderBy('tanggal', 'DESC')
                        ->orderBy('shift', 'DESC')
                        ->first();

        return $lastStok ? $lastStok['stok_real'] : 0;
    }
// Di dalam StokModel.php
public function simpanStokTeoritis($kode_spbu, $tangki_id, $tanggal, $shift, $volume_do, $volume_diterima)
{
    // Cari record stok hari ini di shift yang sama
    $existing = $this->where('tangki_id', $tangki_id)
                    ->where('tanggal', $tanggal)
                    ->where('shift', $shift)
                    ->first();

    if ($existing) {
        // Update record yang ada
        return $this->update($existing['id'], [
            'stok_awal' => $existing['stok_awal'],
            'penerimaan' => $volume_do,
            'penjualan' => $existing['penjualan'],
            'stok_teoritis' => $existing['stok_awal'] + $volume_do - $existing['penjualan'],
            'stok_real' => $volume_diterima
        ]);
    } else {
        // Buat record baru
        $lastStok = $this->where('tangki_id', $tangki_id)
                        ->orderBy('tanggal', 'DESC')
                        ->orderBy('shift', 'DESC')
                        ->first();

        $stokAwal = $lastStok ? $lastStok['stok_real'] : 0;

        return $this->insert([
            'tanggal' => $tanggal,
            'shift' => $shift,
            'kode_spbu' => $kode_spbu,
            'tangki_id' => $tangki_id,
            'stok_awal' => $stokAwal,
            'penerimaan' => $volume_do,
            'penjualan' => 0,
            'stok_teoritis' => $stokAwal + $volume_do,
            'stok_real' => $volume_diterima,
            'created_by' => session()->get('user_id')
        ]);
    }
}

public function updateStokReal($kode_spbu, $tangki_id, $tanggal, $shift, $volume_diterima)
{
    // Cari record stok hari ini di shift yang sama
    $existing = $this->where('tangki_id', $tangki_id)
                    ->where('tanggal', $tanggal)
                    ->where('shift', $shift)
                    ->first();

    if ($existing) {
        // Hitung selisih
        $selisih = $existing['stok_teoritis'] - $volume_diterima;
        
        return $this->update($existing['id'], [
            'stok_real' => $volume_diterima,
            'selisih' => $selisih,
            'status_loss' => $this->_determineLossStatus($existing['kode_produk'], $selisih, $existing['stok_teoritis'])
        ]);
    }
    
    return false;
}

private function _determineLossStatus($kode_produk, $selisih, $stok_teoritis)
{
    $produk = $this->db->table('produk_bbm')->where('kode_produk', $kode_produk)->get()->getRow();
    
    if (!$produk) {
        return 'audit';
    }
    
    $toleransi = $stok_teoritis * ($produk->toleransi_loss / 100);
    
    if ($selisih > $toleransi) {
        return 'audit';
    } elseif ($selisih > 0) {
        return 'toleransi';
    }
    
    return 'normal';
}
}