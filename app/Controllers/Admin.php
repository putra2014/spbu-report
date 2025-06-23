<?php

namespace App\Controllers;

use App\Models\ResetModel;

class Dashboard extends BaseController
{
    protected $resetModel;

    
    public function approveReset($id) {
    $this->resetModel->update($id, [
        'status' => 'approved',
        'approved_by' => session()->get('user_id'),
        'approved_at' => date('Y-m-d H:i:s')
    ]);
    return redirect()->back()->with('success', 'Reset disetujui');
}
}
