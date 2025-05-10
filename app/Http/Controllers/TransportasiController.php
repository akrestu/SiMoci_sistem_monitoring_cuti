<?php

namespace App\Http\Controllers;

use App\Models\Transportasi;
use Illuminate\Http\Request;

class TransportasiController extends Controller
{
    public function index()
    {
        $transportasis = Transportasi::all();
        return view('transportasi.index', compact('transportasis'));
    }

    public function create()
    {
        return view('transportasi.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'jenis' => 'required|string|max:255',
            'keterangan' => 'nullable|string',
        ]);

        Transportasi::create($validated);

        return redirect()->route('transportasis.index')
            ->with('success', 'Data transportasi berhasil ditambahkan.');
    }

    public function show(Transportasi $transportasi)
    {
        return view('transportasi.show', compact('transportasi'));
    }

    public function edit(Transportasi $transportasi)
    {
        return view('transportasi.edit', compact('transportasi'));
    }

    public function update(Request $request, Transportasi $transportasi)
    {
        $validated = $request->validate([
            'jenis' => 'required|string|max:255',
            'keterangan' => 'nullable|string',
        ]);

        $transportasi->update($validated);

        return redirect()->route('transportasis.index')
            ->with('success', 'Data transportasi berhasil diperbarui.');
    }

    public function destroy(Transportasi $transportasi)
    {
        // Check if this transportasi is being used in any transportasi_details
        if ($transportasi->transportasiDetails()->exists()) {
            return redirect()->route('transportasis.index')
                ->with('error', 'Tidak dapat menghapus ' . $transportasi->jenis . ' karena sedang digunakan dalam pengajuan cuti. Hapus semua detail transportasi terkait terlebih dahulu.');
        }

        $transportasi->delete();

        return redirect()->route('transportasis.index')
            ->with('success', 'Data transportasi berhasil dihapus.');
    }
}