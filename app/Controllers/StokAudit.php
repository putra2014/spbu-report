<?php
namespace App\Controllers;

use App\Models\StokModel;
use App\Models\StokHistoryModel;

class StokAudit extends BaseController
{
    protected $stokModel;
    protected $historyModel;

    public function __construct()
    {
        $this->stokModel = new StokModel();
        $this->historyModel = new StokHistoryModel();
    }

    // List temuan audit
    public function index()
    {
        $data = [
            'title' => 'Temuan Audit Stok',
            'audit' => $this->stokModel->where('status_loss', 'audit')
                                      ->orderBy('tanggal', 'DESC')
                                      ->findAll()
        ];

        return view('stok/audit_list', $data);
    }

    // Detail investigasi
    public function detail($id)
    {
        $data = [
            'title' => 'Detail Audit',
            'stok' => $this->stokModel->find($id),
            'history' => $this->historyModel->getHistory($id)
        ];

        return view('stok/audit_detail', $data);
    }

    // Submit hasil investigasi
    public function simpanInvestigasi()
    {
        $rules = [
            'stok_id' => 'required|numeric',
            'kesimpulan' => 'required|string',
            'tindakan' => 'required|string'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = $this->request->getPost();
        $this->historyModel->logChange(
            $data['stok_id'],
            'investigasi_audit',
            null,
            json_encode(['kesimpulan' => $data['kesimpulan'], 'tindakan' => $data['tindakan']]),
            session()->get('user_id'),
            'Hasil investigasi temuan audit'
        );

        return redirect()->to('/stok/audit')->with('success', 'Hasil investigasi berhasil disimpan!');
    }
}