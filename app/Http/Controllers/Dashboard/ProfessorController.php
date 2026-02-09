<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;

class ProfessorController extends Controller
{
    public function index()
    {
        return view('dashboard.professor.index');
    }
}
