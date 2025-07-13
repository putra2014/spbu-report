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
        $uri = service('uri');
        $currentPath = $uri->getPath();

        // 1. Cek login dasar
        if (!$session->get('logged_in')) {
            return redirect()->to('/login')->with('error', 'Silakan login terlebih dahulu');
        }

        // 2. Cek kelengkapan data SPBU (khusus admin_spbu)
        if (
            $session->get('role') === 'admin_spbu' &&
            $session->get('spbu_incomplete') &&
            !preg_match('#^(spbu/edit/\d+|stok/input|stok/laporan)$#', $currentPath)
        ) {
            return redirect()->to('/spbu/edit/' . $session->get('kode_spbu'))
                ->with('warning', 'Lengkapi data SPBU Anda terlebih dahulu.');
        }

        // 3. RBAC untuk modul Stok
        if (strpos($currentPath, 'stok/') === 0) {
            $allowedRoles = $this->getAllowedRolesForRoute($currentPath);
            
            if (!in_array($session->get('role'), $allowedRoles)) {
                return redirect()->to('/unauthorized');
            }
        }
    }

    protected function getAllowedRolesForRoute(string $path): array
    {
        $routeRoles = [
            'stok/input' => ['admin_spbu'],
            'stok/simpan' => ['admin_spbu'],
            'stok/laporan' => ['admin_spbu'],
            'stok/audit' => ['admin_region'],
            'stok/audit/detail' => ['admin_region']
        ];

        foreach ($routeRoles as $route => $roles) {
            if (strpos($path, $route) === 0) {
                return $roles;
            }
        }

        return []; // Default: tidak ada akses
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Tidak perlu filter setelah request
    }
}