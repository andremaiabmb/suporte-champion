<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;

class AlunoController extends Controller
{
    public function index()
    {
        return view('dashboard.aluno.index');
    }
}
