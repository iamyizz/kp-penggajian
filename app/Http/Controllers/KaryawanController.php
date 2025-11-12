<?php

namespace App\Http\Controllers;

use App\Models\Karyawan;
use App\Models\Jabatan;
use Illuminate\Http\Request;

class KaryawanController extends Controller
{
    public function index()
    {
        $data = Karyawan::with('jabatan')->get();
        $jabatans = Jabatan::all();

        // Generate NIP otomatis
        $lastKaryawan = Karyawan::orderBy('id_karyawan', 'desc')->first();
        if ($lastKaryawan && preg_match('/KLSM-(\d+)/', $lastKaryawan->nip, $matches)) {
            $newNumber = intval($matches[1]) + 1;
        } else {
            $newNumber = 1;
        }
        $nextNip = 'KLSM-' . str_pad($newNumber, 4, '0', STR_PAD_LEFT);

        return view('karyawan.index', compact('data', 'jabatans', 'nextNip'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:100',
            'jabatan_id' => 'required|exists:jabatan,id_jabatan',
            'tanggal_masuk' => 'required|date',
            'status_karyawan' => 'required|string|max:50',
            'rekening_bank' => 'nullable|string|max:100',
            'aktif' => 'required|boolean',
        ]);

        // Generate NIP otomatis saat penyimpanan
        $lastKaryawan = Karyawan::orderBy('id_karyawan', 'desc')->first();
        if ($lastKaryawan && preg_match('/KLSM-(\d+)/', $lastKaryawan->nip, $matches)) {
            $newNumber = intval($matches[1]) + 1;
        } else {
            $newNumber = 1;
        }
        $nip = 'KLSM-' . str_pad($newNumber, 4, '0', STR_PAD_LEFT);

        $validated['nip'] = $nip;

        Karyawan::create($validated);

        return redirect()->route('karyawan.index')->with('success', '✅ Karyawan berhasil ditambahkan!');
    }

    public function update(Request $request, $id)
    {
        $karyawan = Karyawan::findOrFail($id);

        $validated = $request->validate([
            'nama' => 'required|string|max:100',
            'jabatan_id' => 'required|exists:jabatan,id_jabatan',
            'tanggal_masuk' => 'required|date',
            'status_karyawan' => 'required|string|max:50',
            'rekening_bank' => 'nullable|string|max:100',
            'aktif' => 'required|boolean',
        ]);

        // NIP tidak berubah saat update
        $validated['nip'] = $karyawan->nip;

        $karyawan->update($validated);

        return redirect()->route('karyawan.index')->with('success', '✏️ Data karyawan berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $karyawan = Karyawan::findOrFail($id);
        $karyawan->delete();

        return redirect()->route('karyawan.index')->with('success', '🗑️ Data karyawan berhasil dihapus!');
    }
}
