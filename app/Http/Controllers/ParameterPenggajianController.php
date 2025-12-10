<?php

namespace App\Http\Controllers;

use App\Models\ParameterPenggajian;
use Illuminate\Http\Request;

class ParameterPenggajianController extends Controller
{
    public function index()
    {
        $params = ParameterPenggajian::all();
        return view('parameter.index', compact('params'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_param' => 'required',
            'key' => 'required|unique:parameter_penggajian,key',
            'nilai' => 'required|numeric',
            'satuan' => 'nullable|string',
            'keterangan' => 'nullable|string'
        ]);

        ParameterPenggajian::create([
            'nama_param' => $request->nama_param,
            'key' => $request->key,
            'nilai' => $request->nilai,
            'satuan' => $request->satuan,
            'keterangan' => $request->keterangan
        ]);

        return redirect()->route('parameter.index')
            ->with('success', 'Parameter berhasil ditambahkan!');
    }

    public function update(Request $request, $id_param)
    {
        $param = ParameterPenggajian::findOrFail($id_param);

        $request->validate([
            'nama_param' => 'required|string|max:100',
            'key' => 'required|string|unique:parameter_penggajian,key,' . $id_param . ',id_param',
            'nilai' => 'required|numeric',
            'satuan' => 'nullable|string|max:50',
            'keterangan' => 'nullable|string',
        ]);

        $param->update([
            'nama_param' => $request->nama_param,
            'key' => $request->key,
            'nilai' => $request->nilai,
            'satuan' => $request->satuan,
            'keterangan' => $request->keterangan,
        ]);

        return redirect()->route('parameter.index')->with('success', 'Parameter berhasil diupdate!');
    }

    public function destroy($id_param)
    {
        ParameterPenggajian::findOrFail($id_param)->delete();

        return redirect()->route('parameter.index')
            ->with('success', 'Parameter berhasil dihapus!');
    }
}
