<?php

namespace App\Http\Controllers;

use App\Models\Jabatan;
use Illuminate\Http\Request;

class JabatanController extends Controller
{
    public function index()
    {
        $jabatan = Jabatan::all();
        return view('jabatan.index', compact('jabatan'));
    }

    public function create()
    {
        return view('jabatan.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_jabatan' => 'required|string|max:100',
            'gaji_pokok' => 'required|numeric',
            'tunjangan_jabatan' => 'nullable|numeric',
        ]);

        Jabatan::create($request->all());

        return redirect()->route('jabatan.index')->with('success', 'Data jabatan berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $jabatan = Jabatan::findOrFail($id);
        return view('jabatan.edit', compact('jabatan'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_jabatan' => 'required|string|max:100',
            'gaji_pokok' => 'required|numeric',
            'tunjangan_jabatan' => 'nullable|numeric',
        ]);

        $jabatan = Jabatan::findOrFail($id);
        $jabatan->update($request->all());

        return redirect()->route('jabatan.index')->with('success', 'Data jabatan berhasil diupdate.');
    }

    public function destroy($id)
    {
        Jabatan::findOrFail($id)->delete();
        return redirect()->route('jabatan.index')->with('success', 'Data jabatan berhasil dihapus.');
    }
}
