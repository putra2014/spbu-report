<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class AuthCheck implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = session();

        if (!$session->get('logged_in')) {
            return redirect()->to('/login')->with('error', 'Silakan login terlebih dahulu');
        }

        // Pengecualian: jika SPBU belum lengkap, hanya boleh akses /spbu/edit/{id}
        
        
        // PERBAIKAN:
        // Ganti cara mendapatkan current URI dengan:
        $uri = service('uri');
        $currentPath = $uri->getPath();

        if (strpos($currentPath, 'admin/') === 0 && !$session->get('admin_region')) {
            return redirect()->to('/unauthorized');
        }
        if (
            $session->get('role') === 'admin_spbu' &&
            $session->get('spbu_incomplete') &&
            !preg_match('#^spbu/edit/\d+$#', $currentPath)
        ) {
            return redirect()->to('/spbu/edit/' . $session->get('kode_spbu'))
                ->with('warning', 'Lengkapi data SPBU Anda terlebih dahulu.');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Tidak perlu filter setelah request
    }
}