<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AdminsController extends Controller
{
    public function render()
    {
        return view('layouts.dashboard');
    }
}