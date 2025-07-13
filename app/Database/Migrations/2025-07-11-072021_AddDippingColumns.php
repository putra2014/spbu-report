<?php
namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddDippingColumns extends Migration
{
    public function up()
    {
        // Tambahkan kolom volume_dipping jika belum ada
        if (!$this->db->fieldExists('volume_dipping', 'penerimaan')) {
            $this->forge->addColumn('penerimaan', [
                'volume_dipping' => [
                    'type' => 'DECIMAL',
                    'constraint' => '10,2',
                    'after' => 'volume_diterima'
                ]
            ]);
        }

        // Tambahkan kolom dipping_by jika belum ada
        if (!$this->db->fieldExists('dipping_by', 'penerimaan')) {
            $this->forge->addColumn('penerimaan', [
                'dipping_by' => [
                    'type' => 'INT',
                    'null' => true,
                    'after' => 'volume_dipping'
                ]
            ]);
        }

        // Tambahkan kolom dipping_time jika belum ada
        if (!$this->db->fieldExists('dipping_time', 'penerimaan')) {
            $this->forge->addColumn('penerimaan', [
                'dipping_time' => [
                    'type' => 'DATETIME',
                    'null' => true,
                    'after' => 'dipping_by'
                ]
            ]);
        }

        // Modifikasi kolom status jika sudah ada, atau tambahkan jika belum ada
        if ($this->db->fieldExists('status', 'penerimaan')) {
            $this->forge->modifyColumn('penerimaan', [
                'status' => [
                    'type' => 'ENUM',
                    'constraint' => ['pending','draft','completed'],
                    'default' => 'pending',
                    'after' => 'dipping_time'
                ]
            ]);
        } else {
            $this->forge->addColumn('penerimaan', [
                'status' => [
                    'type' => 'ENUM',
                    'constraint' => ['pending','draft','completed'],
                    'default' => 'pending',
                    'after' => 'dipping_time'
                ]
            ]);
        }

        // Tambahkan kolom is_initial jika belum ada
        if (!$this->db->fieldExists('is_initial', 'stok_bbm')) {
            $this->forge->addColumn('stok_bbm', [
                'is_initial' => [
                    'type' => 'TINYINT',
                    'default' => 0,
                    'after' => 'is_closed'
                ]
            ]);
        }

        // Tambahkan kolom is_closing jika belum ada
        if (!$this->db->fieldExists('is_closing', 'stok_bbm')) {
            $this->forge->addColumn('stok_bbm', [
                'is_closing' => [
                    'type' => 'TINYINT',
                    'default' => 0,
                    'after' => 'is_initial'
                ]
            ]);
        }
    }

    public function down()
    {
        // Hapus kolom hanya jika kolom tersebut ada
        if ($this->db->fieldExists('volume_dipping', 'penerimaan')) {
            $this->forge->dropColumn('penerimaan', 'volume_dipping');
        }
        
        if ($this->db->fieldExists('dipping_by', 'penerimaan')) {
            $this->forge->dropColumn('penerimaan', 'dipping_by');
        }
        
        if ($this->db->fieldExists('dipping_time', 'penerimaan')) {
            $this->forge->dropColumn('penerimaan', 'dipping_time');
        }
        
        if ($this->db->fieldExists('status', 'penerimaan')) {
            $this->forge->dropColumn('penerimaan', 'status');
        }
        
        if ($this->db->fieldExists('is_initial', 'stok_bbm')) {
            $this->forge->dropColumn('stok_bbm', 'is_initial');
        }
        
        if ($this->db->fieldExists('is_closing', 'stok_bbm')) {
            $this->forge->dropColumn('stok_bbm', 'is_closing');
        }
    }
}