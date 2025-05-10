<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cuti;
use App\Models\JenisCuti;
use Carbon\Carbon;

class MemoKompensasiController extends Controller
{
    /**
     * Menampilkan halaman monitoring memo kompensasi
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $baseQuery = Cuti::with(['karyawan', 'jenisCuti', 'cutiDetails.jenisCuti'])
            ->where('status_cuti', 'disetujui')
            ->where(function($query) {
                $query->where('memo_kompensasi_status', true)
                      ->orWhere('memo_kompensasi_status', false);
            });

        // Get total counts for statistics (before applying pagination)
        $totalMemoCount = (clone $baseQuery)->count();
        $sudahDiajukanCount = (clone $baseQuery)
            ->where('memo_kompensasi_status', true)
            ->whereNotNull('memo_kompensasi_nomor')
            ->whereNotNull('memo_kompensasi_tanggal')
            ->count();
        $belumDiajukanCount = $totalMemoCount - $sudahDiajukanCount;
        $bulanIniCount = (clone $baseQuery)
            ->where('memo_kompensasi_status', true)
            ->whereNotNull('memo_kompensasi_tanggal')
            ->whereMonth('memo_kompensasi_tanggal', Carbon::now()->month)
            ->whereYear('memo_kompensasi_tanggal', Carbon::now()->year)
            ->count();

        // Continue with filtering for the paginated list
        $query = clone $baseQuery;
            
        // Filter berdasarkan status memo kompensasi
        if ($request->has('status_memo') && $request->status_memo != '') {
            if($request->status_memo == 'sudah') {
                $query->where('memo_kompensasi_status', true)
                      ->whereNotNull('memo_kompensasi_nomor')
                      ->whereNotNull('memo_kompensasi_tanggal');
            } elseif($request->status_memo == 'belum') {
                $query->where(function($q) {
                    $q->where('memo_kompensasi_status', false)
                      ->orWhereNull('memo_kompensasi_status')
                      ->orWhereNull('memo_kompensasi_nomor')
                      ->orWhereNull('memo_kompensasi_tanggal');
                });
            }
        }
        
        // Filter berdasarkan tanggal
        if ($request->has('start_date') && $request->start_date != '') {
            $query->where('tanggal_mulai', '>=', $request->start_date);
        }
        
        if ($request->has('end_date') && $request->end_date != '') {
            $query->where('tanggal_selesai', '<=', $request->end_date);
        }
        
        // Filter berdasarkan nama karyawan
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->whereHas('karyawan', function($q) use ($search) {
                $q->where('nama', 'like', '%' . $search . '%')
                  ->orWhere('nik', 'like', '%' . $search . '%');
            });
        }
        
        // Handle per_page parameter for pagination
        $perPage = 15; // Default value
        if ($request->has('per_page')) {
            if ($request->per_page == 'all') {
                // If 'all' is selected, we need to set a very large number
                $perPage = $query->count();
            } else {
                $perPage = (int)$request->per_page;
            }
        }
        
        $cutis = $query->latest()->paginate($perPage);
        
        // Maintain query parameters in pagination links
        if ($request->anyFilled(['search', 'status_memo', 'start_date', 'end_date', 'per_page'])) {
            $cutis->appends($request->all());
        }
        
        // Calculate percentages
        $persentaseSudah = $totalMemoCount > 0 ? ($sudahDiajukanCount / $totalMemoCount) * 100 : 0;
        $persentaseBelum = $totalMemoCount > 0 ? ($belumDiajukanCount / $totalMemoCount) * 100 : 0;
        
        return view('memo_kompensasi.index', compact(
            'cutis', 
            'totalMemoCount', 
            'sudahDiajukanCount', 
            'belumDiajukanCount', 
            'bulanIniCount',
            'persentaseSudah',
            'persentaseBelum'
        ));
    }
    
    /**
     * Update status memo kompensasi
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Cuti  $cuti
     * @return \Illuminate\Http\Response
     */
    public function updateStatus(Request $request, Cuti $cuti)
    {
        $validated = $request->validate([
            'memo_kompensasi_status' => 'required|boolean',
            'memo_kompensasi_nomor' => 'nullable|string|required_if:memo_kompensasi_status,1',
            'memo_kompensasi_tanggal' => 'nullable|date|required_if:memo_kompensasi_status,1',
        ]);
        
        // Update status memo kompensasi
        $cuti->update([
            'memo_kompensasi_status' => $validated['memo_kompensasi_status'],
            'memo_kompensasi_nomor' => $validated['memo_kompensasi_nomor'] ?? null,
            'memo_kompensasi_tanggal' => $validated['memo_kompensasi_tanggal'] ?? null,
        ]);
        
        return redirect()->route('memo-kompensasi.index')
            ->with('success', 'Status memo kompensasi berhasil diperbarui.');
    }
}