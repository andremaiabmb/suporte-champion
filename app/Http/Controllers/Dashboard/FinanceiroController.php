<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;

class FinanceiroController extends Controller
{
    public function index()
    {
        return view('dashboard.financeiro.index');
    }
}
