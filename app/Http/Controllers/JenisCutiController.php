<?php

namespace App\Http\Controllers;

use App\Models\JenisCuti;
use Illuminate\Http\Request;

class JenisCutiController extends Controller
{
    public function index()
    {
        $jenisCutis = JenisCuti::all();
        return view('jenis_cuti.index', compact('jenisCutis'));
    }

    public function create()
    {
        return view('jenis_cuti.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_jenis' => 'required|string|max:255',
            'jatah_hari' => 'required|integer|min:1',
            'jenis_poh' => 'required|in:lokal,luar,lokal_luar',
            'keterangan' => 'nullable|string',
        ]);

        // Set default value for perlu_memo_kompensasi
        $validated['perlu_memo_kompensasi'] = 0;

        JenisCuti::create($validated);

        return redirect()->route('jenis-cutis.index')
            ->with('success', 'Jenis cuti berhasil ditambahkan.');
    }

    public function show(JenisCuti $jenisCuti)
    {
        return view('jenis_cuti.show', compact('jenisCuti'));
    }

    public function edit(JenisCuti $jenisCuti)
    {
        return view('jenis_cuti.edit', compact('jenisCuti'));
    }

    public function update(Request $request, JenisCuti $jenisCuti)
    {
        $validated = $request->validate([
            'nama_jenis' => 'required|string|max:255',
            'jatah_hari' => 'required|integer|min:1',
            'jenis_poh' => 'required|in:lokal,luar,lokal_luar',
            'keterangan' => 'nullable|string',
        ]);

        // Set default value for perlu_memo_kompensasi
        $validated['perlu_memo_kompensasi'] = 0;

        $jenisCuti->update($validated);

        return redirect()->route('jenis-cutis.index')
            ->with('success', 'Jenis cuti berhasil diperbarui.');
    }

    public function destroy(JenisCuti $jenisCuti)
    {
        $jenisCuti->delete();

        return redirect()->route('jenis-cutis.index')
            ->with('success', 'Jenis cuti berhasil dihapus.');
    }
}