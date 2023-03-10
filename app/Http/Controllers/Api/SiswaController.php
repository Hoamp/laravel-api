<?php

namespace App\Http\Controllers\Api;

use App\Models\Siswa;
use App\Http\Resources\SiswaResource;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class SiswaController extends Controller
{
    public function index()
    {
        // tampung semua data siswa
        $siswas = Siswa::latest()->paginate(5);

        // kembalikan collection dari siswas ke resource
        return new SiswaResource(true, 'List Data Siswa', $siswas);
    }

    public function store(Request $request)
    {
        // rules
        $validator = Validator::make($request->all(), [
            'foto' => 'required|image|mimes:jpeg,png,jpg,gif,svg',
            'nama' => 'required',
            'nis' => 'required',
        ]);

        // jika gagal
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // store gambar 
        $foto = $request->file('foto');
        $foto->storeAs('public/siswa', $foto->hashName());

        // buat siswa baru
        $siswa = Siswa::create([
            'foto' => $foto->hashName(),
            'nama' => $request->nama,
            'nis' => $request->nis,
        ]);

        // kembalikan pesan dan resource
        return new SiswaResource(true, 'Siswa Berhasil Ditambahkan', $siswa);
    }

    public function show(Siswa $siswa)
    {
        // kembalikan 1 data siswa
        return new SiswaResource(true, "Data Siswa Ditemukan", $siswa);
    }

    public function update(Request $request, Siswa $siswa)
    {
        // validasi tanpa foto
        $validator = Validator::make($request->all(), [
            'nama' => 'required',
            'nis' => 'required'
        ]);

        // jika validasi gagal
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // cek gambar
        if ($request->hasFile('foto')) {
            // upload gambar
            $foto = $request->file('foto');
            $foto->storeAs('public/siswa', $foto->hashName());

            // hapus gambar lama
            Storage::delete('public/siswa' . $foto->hashName());

            // update data siswa
            $siswa->update([
                'foto' => $foto->hashName(),
                'nama' => $request->nama,
                'nis' => $request->nis
            ]);
        } else {
            // updata tanpa upload gambar
            $siswa->update([
                'nama' => $request->nama,
                'nis' => $request->nis
            ]);
        }

        // kembalikan response data
        return new SiswaResource(true, "Data Siswa Diubah", $siswa);
    }

    public function destroy(Siswa $siswa)
    {
        // hapus gambar dari folder siswa
        Storage::delete('public/siswa' . $siswa->foto);

        // hapus data siswa
        $siswa->delete();

        // kembalikan response
        return new SiswaResource(true, "Data Berhasil Dihapus", null);
    }
}
