<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;

class ModeradorController extends Controller
{
    public function index()
    {
        return view('dashboard.moderador.index');
    }
}
