<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;

class HomeController extends Controller
{
    /**
     * Diperbaiki: sebelumnya route ini nampilin view yang sama persis dengan
     * halaman login ('sesi/index'), padahal ini route dashboard yang wajib login.
     * Karena konten dashboard aslinya ada di /database, di sini cukup redirect.
     */
    public function index(): RedirectResponse
    {
        return redirect('/database');
    }
}
