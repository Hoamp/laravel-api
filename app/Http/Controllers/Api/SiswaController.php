<?php

namespace App\Http\Controllers\Api;

use App\Models\Siswa;
use App\Http\Resources\SiswaResource;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SiswaController extends Controller
{
    public function index()
    {
        // tampung semua data siswa
        $siswas = Siswa::latest()->paginate(5);

        // kembalikan collection dari siswas ke resource
        return new SiswaResource(true, 'List Data Siswa', $siswas);
    }
}
