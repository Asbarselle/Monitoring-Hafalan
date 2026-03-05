<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class HomeController extends Controller
{
    /**
     * Tampilkan halaman beranda
     */
    public function index(): View
    {
        return view('home.index');
    }

    /**
     * Tampilkan halaman tentang kami
     */
    public function tentang(): View
    {
        return view('home.tentang');
    }

    /**
     * Tampilkan halaman info
     */
    public function info(): View
    {
        return view('home.info');
    }
}
