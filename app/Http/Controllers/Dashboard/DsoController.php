<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;

class DsoController extends Controller
{
    public function index()
    {
        return view('dashboard.dso.index');
    }
}
