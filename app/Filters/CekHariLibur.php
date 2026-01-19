<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class CekHariLibur implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $hari_ini = date('N');
        
        if ($hari_ini == 7) {
            session()->setFlashdata('gagal', 'Hari ini adalah hari Minggu (Libur). Presensi tidak tersedia.');
            return redirect()->to(base_url('pegawai/dashboard'));
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
    }
}