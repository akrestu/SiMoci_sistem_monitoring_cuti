<?php

namespace App\Http\Controllers;

use App\Models\Cuti;
use App\Models\Transportasi;
use App\Models\TransportasiDetail;
use App\Exports\TransportasiDetailExport;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;

class TransportasiDetailController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 10);

        // Handle 'all' option
        if ($perPage == 'all') {
            $transportasiDetails = TransportasiDetail::with(['cuti.karyawan', 'transportasi'])
                ->orderBy('created_at', 'desc')
                ->get();

            // Create a LengthAwarePaginator manually with all items
            $transportasiDetails = new \Illuminate\Pagination\LengthAwarePaginator(
                $transportasiDetails,
                $transportasiDetails->count(),
                $transportasiDetails->count(),
                1,
                ['path' => $request->url(), 'query' => $request->query()]
            );
        } else {
            $transportasiDetails = TransportasiDetail::with(['cuti.karyawan', 'transportasi'])
                ->orderBy('created_at', 'desc')
                ->paginate($perPage);

            if ($request->has('per_page')) {
                $transportasiDetails->appends(['per_page' => $perPage]);
            }
        }

        return view('transportasi_detail.index', compact('transportasiDetails'));
    }

    public function create(Cuti $cuti)
    {
        // Get transportasi types that aren't already used for this cuti
        $usedTransportasiIds = $cuti->transportasiDetails()->pluck('transportasi_id')->toArray();
        $transportasis = Transportasi::whereNotIn('id', $usedTransportasiIds)->get();

        return view('transportasi_detail.create', compact('cuti', 'transportasis'));
    }

    public function store(Request $request, Cuti $cuti)
    {
        $validated = $request->validate([
            'transportasi_id' => 'required|exists:transportasis,id',
            'jenis_perjalanan' => 'required|in:pergi,kembali',
            'rute_asal' => 'required|string',
            'rute_tujuan' => 'required|string',
            'waktu_berangkat' => 'nullable|date',
            'waktu_kembali' => 'nullable|date|after:waktu_berangkat',
            'provider' => 'nullable|string',
            'biaya_aktual' => 'nullable|numeric',
            'perlu_hotel' => 'boolean',
            'hotel_nama' => 'nullable|required_if:perlu_hotel,1|string',
            'hotel_biaya' => 'nullable|required_if:perlu_hotel,1|numeric',
            'catatan' => 'nullable|string',
        ]);

        $validated['cuti_id'] = $cuti->id;
        $validated['status_pemesanan'] = 'belum_dipesan';

        // Handle boolean checkbox for perlu_hotel
        $validated['perlu_hotel'] = $request->has('perlu_hotel') ? 1 : 0;

        // Jika perlu_hotel tidak dicentang, berikan default nilai 0 untuk hotel_biaya
        if (!$validated['perlu_hotel']) {
            $validated['hotel_nama'] = null;
            $validated['hotel_biaya'] = 0;
        }

        // Pastikan biaya_aktual tidak null
        if (!isset($validated['biaya_aktual']) || $validated['biaya_aktual'] === null) {
            $validated['biaya_aktual'] = 0;
        }

        TransportasiDetail::create($validated);

        return redirect()->route('cutis.show', $cuti->id)
            ->with('success', 'Detail transportasi berhasil ditambahkan.');
    }

    public function show(TransportasiDetail $transportasiDetail)
    {
        $transportasiDetail->load(['cuti.karyawan', 'transportasi']);
        return view('transportasi_detail.show', compact('transportasiDetail'));
    }

    public function edit(TransportasiDetail $transportasiDetail)
    {
        $transportasis = Transportasi::all();
        $cuti = $transportasiDetail->cuti;

        return view('transportasi_detail.edit', compact('transportasiDetail', 'transportasis', 'cuti'));
    }

    public function update(Request $request, TransportasiDetail $transportasiDetail)
    {
        $validated = $request->validate([
            'transportasi_id' => 'required|exists:transportasis,id',
            'nomor_tiket' => 'nullable|string',
            'rute_asal' => 'required|string',
            'rute_tujuan' => 'required|string',
            'waktu_berangkat' => 'nullable|date',
            'provider' => 'required|string',
            'biaya_aktual' => 'nullable|numeric',
            'perlu_hotel' => 'boolean',
            'hotel_nama' => 'nullable|required_if:perlu_hotel,1|string',
            'hotel_biaya' => 'nullable|required_if:perlu_hotel,1|numeric',
            'status_pemesanan' => 'required|in:belum_dipesan,proses_pemesanan,tiket_terbit,dibatalkan',
            'catatan' => 'nullable|string',
        ]);

        // Preserve existing jenis_perjalanan
        $validated['jenis_perjalanan'] = $transportasiDetail->jenis_perjalanan;
        // Set waktu_kembali to 1 hour after waktu_berangkat to maintain compatibility
        $validated['waktu_kembali'] = isset($validated['waktu_berangkat']) ?
            Carbon::parse($validated['waktu_berangkat'])->addHour() : null;

        // Handle boolean checkbox for perlu_hotel
        $validated['perlu_hotel'] = $request->has('perlu_hotel') ? 1 : 0;

        // Jika perlu_hotel tidak dicentang, berikan default nilai 0 untuk hotel_biaya
        if (!$validated['perlu_hotel']) {
            $validated['hotel_nama'] = null;
            $validated['hotel_biaya'] = 0;
        }

        // Pastikan biaya_aktual tidak null
        if (!isset($validated['biaya_aktual']) || $validated['biaya_aktual'] === null) {
            $validated['biaya_aktual'] = 0;
        }

        $transportasiDetail->update($validated);

        return redirect()->route('transportasi_details.dashboard')
            ->with('success', 'Detail transportasi berhasil diperbarui.');
    }

    public function destroy(TransportasiDetail $transportasiDetail)
    {
        $cutiId = $transportasiDetail->cuti_id;
        $transportasiDetail->delete();

        return redirect()->route('cutis.show', $cutiId)
            ->with('success', 'Detail transportasi berhasil dihapus.');
    }

    /**
     * Show upcoming ticket booking deadlines
     */
    public function upcomingDeadlines()
    {
        // Get upcoming ticket booking deadlines (tickets that need to be booked soon)
        $upcomingDeadlines = TransportasiDetail::whereHas('cuti', function ($query) {
                // For departure tickets, check if the leave start date is approaching
                // For return tickets, check if the leave end date is approaching
                $query->where(function ($q) {
                    $q->where(function ($innerQ) {
                        $innerQ->where('transportasi_details.jenis_perjalanan', '=', 'pergi')
                               ->whereDate('cutis.tanggal_mulai', '>=', now())
                               ->whereDate('cutis.tanggal_mulai', '<=', now()->addDays(10));
                    })->orWhere(function ($innerQ) {
                        $innerQ->where('transportasi_details.jenis_perjalanan', '=', 'kembali')
                               ->whereDate('cutis.tanggal_selesai', '>=', now())
                               ->whereDate('cutis.tanggal_selesai', '<=', now()->addDays(10));
                    });
                });
            })
            ->where(function($query) {
                $query->whereNull('nomor_tiket')
                      ->orWhere('status_pemesanan', '!=', 'tiket_terbit');
            })
            ->orderBy('created_at', 'desc')
            ->get();

        // Get booking costs by month
        $biayaTransportasi = TransportasiDetail::selectRaw('MONTH(created_at) as bulan, SUM(biaya_aktual) as total')
            ->whereYear('created_at', date('Y'))
            ->where('status_pemesanan', 'tiket_terbit')
            ->groupBy('bulan')
            ->get();

        return view('transportasi_detail.upcoming_deadlines', compact('upcomingDeadlines', 'biayaTransportasi'));
    }

    /**
     * Show the dashboard for transportation management
     */
    public function dashboard()
    {
        // Get upcoming ticket booking deadlines (tickets that need to be booked soon)
        $upcomingDeadlines = TransportasiDetail::whereHas('cuti', function ($query) {
                // For departure tickets, check if the leave start date is approaching
                // For return tickets, check if the leave end date is approaching
                $query->where(function ($q) {
                    $q->where(function ($innerQ) {
                        $innerQ->where('transportasi_details.jenis_perjalanan', '=', 'pergi')
                               ->whereDate('cutis.tanggal_mulai', '>=', now())
                               ->whereDate('cutis.tanggal_mulai', '<=', now()->addDays(10));
                    })->orWhere(function ($innerQ) {
                        $innerQ->where('transportasi_details.jenis_perjalanan', '=', 'kembali')
                               ->whereDate('cutis.tanggal_selesai', '>=', now())
                               ->whereDate('cutis.tanggal_selesai', '<=', now()->addDays(10));
                    });
                });
            })
            ->where(function($query) {
                $query->whereNull('nomor_tiket')
                      ->orWhere('status_pemesanan', '!=', 'tiket_terbit');
            })
            ->orderBy('created_at', 'desc')
            ->get();

        // Get pending bookings that need to be processed
        $pendingBookings = TransportasiDetail::with(['cuti.karyawan', 'transportasi'])
            ->where('status_pemesanan', '!=', 'tiket_terbit')
            ->orderBy('created_at', 'desc')
            ->get();

        // Get booking costs by month
        $biayaTransportasi = TransportasiDetail::selectRaw('MONTH(created_at) as bulan, SUM(biaya_aktual) as total')
            ->whereYear('created_at', date('Y'))
            ->where('status_pemesanan', 'tiket_terbit')
            ->groupBy('bulan')
            ->get();

        return view('transportasi_detail.dashboard', compact('upcomingDeadlines', 'pendingBookings', 'biayaTransportasi'));
    }

    /**
     * Export transportasi details to Excel
     */
    public function export()
    {
        return Excel::download(new TransportasiDetailExport, 'daftar-tiket-transportasi.xlsx');
    }

    public function batchDelete(Request $request)
    {
        $validated = $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:transportasi_details,id'
        ]);

        TransportasiDetail::whereIn('id', $validated['ids'])->delete();

        return redirect()->route('transportasi_details.index')
            ->with('success', 'Detail transportasi berhasil dihapus.');
    }
}