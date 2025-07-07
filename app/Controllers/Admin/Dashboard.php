<?php

namespace App\Controllers\Admin;

use CodeIgniter\Controller;

class Dashboard extends Controller
{
    public function index()
    {
        $data['title'] = "Dashboard";

        echo view('templates_admin/header', $data);
        echo view('templates_admin/sidebar');
        echo view('admin/dashboard', $data);
        echo view('templates_admin/footer');
    }

}
