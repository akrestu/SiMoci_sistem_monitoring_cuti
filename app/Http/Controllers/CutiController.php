<?php

namespace App\Http\Controllers;

use App\Models\Cuti;
use App\Models\Karyawan;
use App\Models\JenisCuti;
use App\Models\Transportasi;
use App\Models\TransportasiDetail;
use App\Models\CutiDetail;
use App\Services\CutiService;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Facades\Excel;

class CutiController extends Controller
{
    public function index(Request $request)
    {
        $query = Cuti::with(['karyawan', 'jenisCuti', 'transportasiDetails', 'cutiDetails.jenisCuti']);

        // Filter berdasarkan status
        if ($request->has('status_cuti') && $request->status_cuti != '') {
            $query->where('status_cuti', $request->status_cuti);
        }

        // Filter berdasarkan jenis cuti
        if ($request->has('jenis_cuti_id') && $request->jenis_cuti_id != '') {
            // Look for entries where either the primary jenis_cuti_id matches or any of the cutiDetails match
            $query->where(function($q) use ($request) {
                $q->where('jenis_cuti_id', $request->jenis_cuti_id)
                  ->orWhereHas('cutiDetails', function($sq) use ($request) {
                      $sq->where('jenis_cuti_id', $request->jenis_cuti_id);
                  });
            });
        }

        // Filter berdasarkan transportasi
        if ($request->has('transportasi_id') && $request->transportasi_id != '') {
            if ($request->transportasi_id == 'tanpa') {
                $query->whereDoesntHave('transportasiDetails');
            } else {
                $query->whereHas('transportasiDetails', function($q) use ($request) {
                    $q->where('transportasi_id', $request->transportasi_id);
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

        // Filter berdasarkan karyawan
        if ($request->has('karyawan_id') && $request->karyawan_id != '') {
            $query->where('karyawan_id', $request->karyawan_id);
        }

        // Sort parameter
        $sortColumn = $request->input('sort', 'tanggal_mulai');
        $sortDirection = $request->input('direction', 'asc');
        
        // Validasi kolom yang diizinkan untuk sorting
        $allowedColumns = ['id', 'tanggal_mulai', 'tanggal_selesai', 'lama_hari', 'status_cuti', 'created_at'];
        
        if (!in_array($sortColumn, $allowedColumns)) {
            $sortColumn = 'tanggal_mulai'; // default sort column
        }
        
        $query->orderBy($sortColumn, $sortDirection);

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

        $cutis = $query->paginate($perPage);
        $karyawans = Karyawan::all();
        $jenisCutis = JenisCuti::all();
        $transportasis = Transportasi::all();

        if ($request->anyFilled(['status_cuti', 'jenis_cuti_id', 'transportasi_id', 'karyawan_id', 'start_date', 'end_date', 'search', 'per_page', 'sort', 'direction'])) {
            $cutis->appends($request->all());
        }

        return view('cuti.index', compact('cutis', 'karyawans', 'jenisCutis', 'transportasis', 'sortColumn', 'sortDirection'));
    }

    public function create()
    {
        // Get all employees for the dropdown
        $karyawans = Karyawan::select('id', 'nama', 'nik', 'departemen', 'status', 'doh')
            ->orderBy('nama', 'asc')
            ->get();

        // Get all departments for filtering
        $departments = Karyawan::select('departemen')
            ->distinct()
            ->orderBy('departemen')
            ->pluck('departemen');

        $jenisCutis = JenisCuti::all();
        $transportasis = Transportasi::all();

        // Get periodic leave types for special handling
        $periodicLeaveTypes = JenisCuti::where('nama_jenis', 'like', '%periodik%')->get();
        $periodicLeaveIds = $periodicLeaveTypes->pluck('id')->toArray();

        return view('cuti.create', compact(
            'jenisCutis',
            'transportasis',
            'karyawans',
            'departments',
            'periodicLeaveTypes',
            'periodicLeaveIds'
        ));
    }

    public function store(Request $request)
    {
        if ($request->has('form_count') && $request->has('forms') && is_array($request->forms)) {
            try {
                DB::beginTransaction();

                $successCount = 0;
                $cutiService = new CutiService();

                foreach ($request->forms as $formIndex => $formData) {
                    // Skip incomplete forms
                    if (empty($formData['karyawan_id']) || empty($formData['jenis_cuti_details'])) {
                        continue;
                    }

                    // Calculate total days from all leave types
                    $totalHari = 0;
                    $primaryJenisCutiId = null;
                    $periodicLeaveFound = false;
                    $periodicLeaveId = null;

                    foreach ($formData['jenis_cuti_details'] as $detail) {
                        if (isset($detail['jenis_cuti_id']) && isset($detail['jumlah_hari']) && $detail['jumlah_hari'] > 0) {
                            $totalHari += $detail['jumlah_hari'];

                            if (!$primaryJenisCutiId) {
                                $primaryJenisCutiId = $detail['jenis_cuti_id'];
                            }

                            // Check if this is a periodic leave type
                            $jenisCuti = JenisCuti::find($detail['jenis_cuti_id']);
                            if ($jenisCuti && stripos($jenisCuti->nama_jenis, 'periodik') !== false) {
                                $periodicLeaveFound = true;
                                $periodicLeaveId = $detail['jenis_cuti_id'];
                            }
                        }
                    }

                    if ($totalHari <= 0) continue;

                    $tanggalMulai = Carbon::parse($formData['tanggal_mulai']);
                    $tanggalSelesai = (clone $tanggalMulai)->addDays($totalHari - 1);

                    // Validate period for periodic leave types
                    if ($periodicLeaveFound && $periodicLeaveId) {
                        $karyawan = Karyawan::findOrFail($formData['karyawan_id']);
                        $periodCheck = $cutiService->isWithinLeavePeriod($karyawan, $periodicLeaveId, $tanggalMulai);

                        // If not within period, add warning message but still allow submission
                        if (!$periodCheck['within_period']) {
                            // Store warning message in session
                            $warningMessage = "Peringatan untuk pengajuan cuti {$karyawan->nama}: " . $periodCheck['message'];
                            session()->flash('period_warning_' . $formIndex, $warningMessage);

                            // Store recommendation dates
                            if (isset($periodCheck['recommendation_dates'])) {
                                $recommendedDate = $periodCheck['recommendation_dates']['based_on_doh']->format('Y-m-d');
                                session()->flash('recommended_date_' . $formIndex, $recommendedDate);
                            }
                        }
                    }

                    // Set memo kompensasi status based on checkbox
                    $memoKompensasiStatus = null; // Default: Tidak Perlu
                    if (isset($formData['is_memo_needed']) && $formData['is_memo_needed'] == 1) {
                        // Jika checkbox dicentang dan nomor memo diisi
                        if (!empty($formData['memo_kompensasi_nomor']) && !empty($formData['memo_kompensasi_tanggal'])) {
                            $memoKompensasiStatus = true; // Sudah Diajukan
                            $memoKompensasiNomor = $formData['memo_kompensasi_nomor'];
                            $memoKompensasiTanggal = $formData['memo_kompensasi_tanggal'];
                        } else {
                            $memoKompensasiStatus = false; // Belum Diajukan
                            $memoKompensasiNomor = null;
                            $memoKompensasiTanggal = null;
                        }
                    } else {
                        // Kotak memo tidak dicentang
                        $memoKompensasiStatus = null; // Tidak Perlu
                        $memoKompensasiNomor = null;
                        $memoKompensasiTanggal = null;
                    }

                    $alasan = isset($formData['alasan']) && $formData['alasan'] !== null
                        ? $formData['alasan']
                        : '';

                    // Create the cuti record
                    $cuti = Cuti::create([
                        'karyawan_id' => $formData['karyawan_id'],
                        'jenis_cuti_id' => $primaryJenisCutiId,
                        'tanggal_mulai' => $tanggalMulai,
                        'tanggal_selesai' => $tanggalSelesai,
                        'lama_hari' => $totalHari,
                        'alasan' => $alasan,
                        'status_cuti' => 'pending',
                        'memo_kompensasi_status' => $memoKompensasiStatus,
                        'memo_kompensasi_nomor' => $memoKompensasiNomor ?? null,
                        'memo_kompensasi_tanggal' => $memoKompensasiTanggal ?? null,
                    ]);

                    // Create cuti details
                    foreach ($formData['jenis_cuti_details'] as $detail) {
                        if (isset($detail['jenis_cuti_id']) && isset($detail['jumlah_hari']) && $detail['jumlah_hari'] > 0) {
                            CutiDetail::create([
                                'cuti_id' => $cuti->id,
                                'jenis_cuti_id' => $detail['jenis_cuti_id'],
                                'jumlah_hari' => $detail['jumlah_hari']
                            ]);
                        }
                    }

                    // Process transportasi if any
                    if (isset($formData['transportasi_ids']) && is_array($formData['transportasi_ids'])) {
                        foreach ($formData['transportasi_ids'] as $transportasiId) {
                            // Construct the keys used for route input fields
                            $ruteAsalPergiKey = "rute_asal_pergi_{$transportasiId}";
                            $ruteTujuanPergiKey = "rute_tujuan_pergi_{$transportasiId}";
                            $ruteAsalKembaliKey = "rute_asal_kembali_{$transportasiId}";
                            $ruteTujuanKembaliKey = "rute_tujuan_kembali_{$transportasiId}";

                            // Check if values exist in the form data
                            if (isset($formData[$ruteAsalPergiKey]) && isset($formData[$ruteTujuanPergiKey])) {
                                // Create departure ticket
                                TransportasiDetail::create([
                                    'cuti_id' => $cuti->id,
                                    'transportasi_id' => $transportasiId,
                                    'jenis_perjalanan' => 'pergi',
                                    'rute_asal' => $formData[$ruteAsalPergiKey],
                                    'rute_tujuan' => $formData[$ruteTujuanPergiKey],
                                    'status_pemesanan' => 'belum_dipesan'
                                ]);
                            }

                            // Create return ticket
                            if (isset($formData[$ruteAsalKembaliKey]) && isset($formData[$ruteTujuanKembaliKey])) {
                                TransportasiDetail::create([
                                    'cuti_id' => $cuti->id,
                                    'transportasi_id' => $transportasiId,
                                    'jenis_perjalanan' => 'kembali',
                                    'rute_asal' => $formData[$ruteAsalKembaliKey],
                                    'rute_tujuan' => $formData[$ruteTujuanKembaliKey],
                                    'status_pemesanan' => 'belum_dipesan'
                                ]);
                            }
                        }
                    }

                    $successCount++;
                }

                Log::info("Successfully processed {$successCount} forms");

                // Recalculate leave balances for all affected employees
                $processedKaryawanIds = [];
                foreach ($request->forms as $formData) {
                    if (isset($formData['karyawan_id']) && !in_array($formData['karyawan_id'], $processedKaryawanIds)) {
                        $karyawan = Karyawan::find($formData['karyawan_id']);
                        if ($karyawan) {
                            // Force recalculation of all leave balances
                            Log::info('Recalculating leave balances after creating leave request', [
                                'karyawan_id' => $karyawan->id,
                                'karyawan_name' => $karyawan->nama
                            ]);

                            // Recalculate all leave balances
                            $cutiService->calculateAllLeaveBalances($karyawan);

                            $processedKaryawanIds[] = $karyawan->id;
                        }
                    }
                }

                DB::commit();

                $message = $successCount > 1
                    ? "Berhasil menambahkan {$successCount} pengajuan cuti."
                    : "Berhasil menambahkan pengajuan cuti.";

                return redirect()->route('cutis.index')
                    ->with('success', $message);
            } catch (\Exception $e) {
                DB::rollBack();
                Log::error("Error processing forms: " . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
                return redirect()->back()
                    ->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
                    ->withInput();
            }
        } else {
            // Old code to handle single form (for backward compatibility)
            $validated = $request->validate([
                'karyawan_id' => 'required|exists:karyawans,id',
                'tanggal_mulai' => 'required|date',
            ]);

            try {
                DB::beginTransaction();

                // Calculate total days from all leave types
                $totalHari = 0;
                $primaryJenisCutiId = null;

                foreach ($request->jenis_cuti_details as $detail) {
                    if (isset($detail['jenis_cuti_id']) && isset($detail['jumlah_hari']) && $detail['jumlah_hari'] > 0) {
                        $totalHari += $detail['jumlah_hari'];

                        // Use the first jenis_cuti_id as primary (for backward compatibility)
                        if (!$primaryJenisCutiId) {
                            $primaryJenisCutiId = $detail['jenis_cuti_id'];
                        }
                    }
                }

                // Calculate end date based on start date and total days
                $tanggalMulai = Carbon::parse($validated['tanggal_mulai']);
                $tanggalSelesai = (clone $tanggalMulai)->addDays($totalHari - 1);

                // Set memo kompensasi status based on is_memo_needed checkbox
                $memoKompensasiStatus = null;
                $memoKompensasiNomor = null;
                $memoKompensasiTanggal = null;

                if ($request->has('is_memo_needed')) {
                    $memoKompensasiStatus = $request->has('memo_kompensasi_status') && $request->memo_kompensasi_status === 'approved';
                    $memoKompensasiNomor = $request->memo_kompensasi_nomor ?? null;
                    $memoKompensasiTanggal = !empty($request->memo_kompensasi_tanggal) ? $request->memo_kompensasi_tanggal : null;
                }

                // Create the cuti record
                $cuti = Cuti::create([
                    'karyawan_id' => $validated['karyawan_id'],
                    'jenis_cuti_id' => $primaryJenisCutiId,
                    'tanggal_mulai' => $tanggalMulai,
                    'tanggal_selesai' => $tanggalSelesai,
                    'lama_hari' => $totalHari,
                    'alasan' => $request->alasan ?? '',
                    'status_cuti' => 'pending',
                    'memo_kompensasi_status' => $memoKompensasiStatus,
                    'memo_kompensasi_nomor' => $memoKompensasiNomor,
                    'memo_kompensasi_tanggal' => $memoKompensasiTanggal,
                ]);

                // Create cuti details
                foreach ($request->jenis_cuti_details as $detail) {
                    if (isset($detail['jenis_cuti_id']) && isset($detail['jumlah_hari']) && $detail['jumlah_hari'] > 0) {
                        CutiDetail::create([
                            'cuti_id' => $cuti->id,
                            'jenis_cuti_id' => $detail['jenis_cuti_id'],
                            'jumlah_hari' => $detail['jumlah_hari']
                        ]);
                    }
                }

                // Process transportasi if selected
                if ($request->has('transportasi_ids')) {
                    foreach ($request->transportasi_ids as $transportasiId) {
                        // Create departure ticket
                        TransportasiDetail::create([
                            'cuti_id' => $cuti->id,
                            'transportasi_id' => $transportasiId,
                            'jenis_perjalanan' => 'pergi',
                            'rute_asal' => $request->input("rute_asal_pergi_{$transportasiId}"),
                            'rute_tujuan' => $request->input("rute_tujuan_pergi_{$transportasiId}"),
                            'status_pemesanan' => 'belum_dipesan'
                        ]);

                        // Create return ticket
                        TransportasiDetail::create([
                            'cuti_id' => $cuti->id,
                            'transportasi_id' => $transportasiId,
                            'jenis_perjalanan' => 'kembali',
                            'rute_asal' => $request->input("rute_asal_kembali_{$transportasiId}"),
                            'rute_tujuan' => $request->input("rute_tujuan_kembali_{$transportasiId}"),
                            'status_pemesanan' => 'belum_dipesan'
                        ]);
                    }
                }

                // Recalculate leave balances for the employee
                $karyawan = Karyawan::find($validated['karyawan_id']);
                if ($karyawan) {
                    // Force recalculation of all leave balances
                    $cutiService = new CutiService();

                    // Log for debugging
                    \Illuminate\Support\Facades\Log::info('Recalculating leave balances after creating leave request (single form)', [
                        'cuti_id' => $cuti->id,
                        'karyawan_id' => $karyawan->id,
                        'karyawan_name' => $karyawan->nama
                    ]);

                    // Recalculate all leave balances
                    $cutiService->calculateAllLeaveBalances($karyawan);

                    // Also specifically recalculate the balance for this leave type
                    $cutiService->calculateLeaveBalance($karyawan, $primaryJenisCutiId);
                }

                DB::commit();

                return redirect()->route('cutis.index')
                    ->with('success', 'Berhasil menambahkan pengajuan cuti.');
            } catch (\Exception $e) {
                DB::rollBack();
                return redirect()->back()
                    ->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
                    ->withInput();
            }
        }
    }

    public function show(Cuti $cuti)
    {
        $cuti->load(['karyawan', 'jenisCuti', 'transportasiDetails.transportasi', 'cutiDetails.jenisCuti']);
        return view('cuti.show', compact('cuti'));
    }

    public function edit(Cuti $cuti)
    {
        $karyawans = Karyawan::all();
        $jenisCutis = JenisCuti::all();
        $transportasis = Transportasi::all();
        $cuti->load(['transportasiDetails', 'cutiDetails']);

        return view('cuti.edit', compact('cuti', 'karyawans', 'jenisCutis', 'transportasis'));
    }

    public function update(Request $request, Cuti $cuti)
    {
        $messages = [
            'karyawan_id.required' => 'Anda harus memilih karyawan.',
            'karyawan_id.exists' => 'Data karyawan yang dipilih tidak valid.',
            'jenis_cuti_id.required' => 'Jenis cuti harus dipilih.',
            'tanggal_mulai.required' => 'Tanggal mulai cuti harus diisi.',
            'tanggal_selesai.required' => 'Tanggal selesai cuti harus diisi.',
        ];

        $validated = $request->validate([
            'karyawan_id' => 'required|exists:karyawans,id',
            'jenis_cuti_id' => 'required|exists:jenis_cutis,id',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'alasan' => 'nullable|string',
            'transportasi_ids' => 'nullable|array',
            'transportasi_ids.*' => 'exists:transportasis,id',
            'status_cuti' => 'required|in:pending,disetujui,ditolak',
            'jenis_cuti_details' => 'required|array',
            'jenis_cuti_details.*.jenis_cuti_id' => 'required|exists:jenis_cutis,id',
            'jenis_cuti_details.*.jumlah_hari' => 'required|integer|min:1',
        ], $messages);

        // Calculate total days from all leave types
        $totalHari = 0;
        $primaryJenisCutiId = null;

        foreach ($request->jenis_cuti_details as $detail) {
            if (isset($detail['jenis_cuti_id']) && isset($detail['jumlah_hari']) && $detail['jumlah_hari'] > 0) {
                $totalHari += $detail['jumlah_hari'];

                // Set the first jenis_cuti_id as primary (for backward compatibility)
                if ($primaryJenisCutiId === null) {
                    $primaryJenisCutiId = $detail['jenis_cuti_id'];
                }
            }
        }

        // Calculate end date based on start date and total days
        $tanggalMulai = Carbon::parse($request->tanggal_mulai);
        $tanggalSelesai = (clone $tanggalMulai)->addDays($totalHari - 1);

        // Remove transportasi_ids from validated data
        $transportasiIds = $request->transportasi_ids ?? [];
        unset($validated['transportasi_ids']);
        unset($validated['jenis_cuti_details']);

        // Set memo kompensasi status based on checkbox
        $memoKompensasiStatus = null; // Default: Tidak Perlu
        if ($request->has('is_memo_needed') && $request->is_memo_needed == 1) {
            // If checkbox is checked, set to false (Belum Diajukan)
            $memoKompensasiStatus = false;
        }

        // Prepare update data
        $cutiData = [
            'karyawan_id' => $validated['karyawan_id'],
            'jenis_cuti_id' => $primaryJenisCutiId,
            'tanggal_mulai' => $validated['tanggal_mulai'],
            'tanggal_selesai' => $tanggalSelesai->format('Y-m-d'),
            'lama_hari' => $totalHari,
            'alasan' => $validated['alasan'] ?? '',
            'status_cuti' => $validated['status_cuti'],
            'memo_kompensasi_status' => $memoKompensasiStatus,
            'memo_kompensasi_nomor' => null,
            'memo_kompensasi_tanggal' => null,
        ];

        // Use DB transaction to ensure all related records are updated
        DB::beginTransaction();
        try {
            // Update the Cuti record
            $cuti->update($cutiData);

            // Update CutiDetail records
            // First, delete all existing records
            $cuti->cutiDetails()->delete();

            // Then create new records
            foreach ($request->jenis_cuti_details as $detail) {
                if (isset($detail['jenis_cuti_id']) && isset($detail['jumlah_hari']) && $detail['jumlah_hari'] > 0) {
                    CutiDetail::create([
                        'cuti_id' => $cuti->id,
                        'jenis_cuti_id' => $detail['jenis_cuti_id'],
                        'jumlah_hari' => $detail['jumlah_hari']
                    ]);
                }
            }

            // Get current transportasi_ids for this cuti
            $existingTransportasiIds = $cuti->transportasiDetails()
                                        ->pluck('transportasi_id')
                                        ->unique()
                                        ->toArray();

            // Find transportasi_ids to remove (in existing but not in request)
            $transportasiIdsToRemove = array_diff($existingTransportasiIds, $transportasiIds);

            // Remove transportation details for removed transportasi_ids
            if (!empty($transportasiIdsToRemove)) {
                $cuti->transportasiDetails()
                    ->whereIn('transportasi_id', $transportasiIdsToRemove)
                    ->delete();
            }

            // For each transportasi_id in request, update or create the records
            foreach ($transportasiIds as $transportasiId) {
                // Check if pergi record exists
                $pergiRecord = $cuti->transportasiDetails()
                                ->where('transportasi_id', $transportasiId)
                                ->where('jenis_perjalanan', 'pergi')
                                ->first();

                // Update or create departure ticket (pergi)
                if ($pergiRecord) {
                    $pergiRecord->update([
                        'rute_asal' => $request->input('rute_asal_pergi_' . $transportasiId, ''),
                        'rute_tujuan' => $request->input('rute_tujuan_pergi_' . $transportasiId, '')
                    ]);
                } else {
                    TransportasiDetail::create([
                        'cuti_id' => $cuti->id,
                        'transportasi_id' => $transportasiId,
                        'jenis_perjalanan' => 'pergi',
                        'rute_asal' => $request->input('rute_asal_pergi_' . $transportasiId, ''),
                        'rute_tujuan' => $request->input('rute_tujuan_pergi_' . $transportasiId, ''),
                        'status_pemesanan' => 'belum_dipesan'
                    ]);
                }

                // Check if kembali record exists
                $kembaliRecord = $cuti->transportasiDetails()
                                ->where('transportasi_id', $transportasiId)
                                ->where('jenis_perjalanan', 'kembali')
                                ->first();

                // Update or create return ticket (kembali)
                if ($kembaliRecord) {
                    $kembaliRecord->update([
                        'rute_asal' => $request->input('rute_asal_kembali_' . $transportasiId, ''),
                        'rute_tujuan' => $request->input('rute_tujuan_kembali_' . $transportasiId, '')
                    ]);
                } else {
                    TransportasiDetail::create([
                        'cuti_id' => $cuti->id,
                        'transportasi_id' => $transportasiId,
                        'jenis_perjalanan' => 'kembali',
                        'rute_asal' => $request->input('rute_asal_kembali_' . $transportasiId, ''),
                        'rute_tujuan' => $request->input('rute_tujuan_kembali_' . $transportasiId, ''),
                        'status_pemesanan' => 'belum_dipesan'
                    ]);
                }
            }

            // Recalculate leave balances for the employee
            $karyawan = Karyawan::find($validated['karyawan_id']);
            if ($karyawan) {
                // Force recalculation of all leave balances
                $cutiService = new CutiService();

                // Log for debugging
                \Illuminate\Support\Facades\Log::info('Recalculating leave balances after updating leave request', [
                    'cuti_id' => $cuti->id,
                    'karyawan_id' => $karyawan->id,
                    'karyawan_name' => $karyawan->nama,
                    'jenis_cuti_id' => $primaryJenisCutiId,
                    'jenis_cuti' => JenisCuti::find($primaryJenisCutiId) ? JenisCuti::find($primaryJenisCutiId)->nama_jenis : 'Unknown'
                ]);

                // Recalculate all leave balances
                $cutiService->calculateAllLeaveBalances($karyawan);

                // Also specifically recalculate the balance for this leave type
                $cutiService->calculateLeaveBalance($karyawan, $primaryJenisCutiId);
            }

            DB::commit();

            return redirect()->route('cutis.index')
                ->with('success', 'Pengajuan cuti berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function destroy(Cuti $cuti)
    {
        // Get karyawan and jenis_cuti_id before deleting
        $karyawan = $cuti->karyawan;
        $jenisCutiId = $cuti->jenis_cuti_id;
        $jenisCutiName = $cuti->jenisCuti ? $cuti->jenisCuti->nama_jenis : 'Unknown';

        // Delete the cuti
        $cuti->delete();

        // Recalculate leave balances if karyawan exists
        if ($karyawan) {
            // Force recalculation of all leave balances
            $cutiService = new \App\Services\CutiService();

            // Log for debugging
            \Illuminate\Support\Facades\Log::info('Recalculating leave balances after deleting leave request', [
                'karyawan_id' => $karyawan->id,
                'karyawan_name' => $karyawan->nama,
                'jenis_cuti_id' => $jenisCutiId,
                'jenis_cuti' => $jenisCutiName
            ]);

            // Recalculate all leave balances
            $cutiService->calculateAllLeaveBalances($karyawan);

            // Also specifically recalculate the balance for this leave type
            if ($jenisCutiId) {
                $cutiService->calculateLeaveBalance($karyawan, $jenisCutiId);
            }
        }

        return redirect()->route('cutis.index')
            ->with('success', 'Data cuti berhasil dihapus.');
    }

    // Tambahan method untuk approval
    public function approve(Cuti $cuti)
    {
        // Update status cuti
        $cuti->update(['status_cuti' => 'disetujui']);

        // Clear cache for leave balances for all leave types
        $karyawan = $cuti->karyawan;
        if ($karyawan) {
            // Force recalculation of all leave balances
            $cutiService = new \App\Services\CutiService();

            // Log for debugging
            \Illuminate\Support\Facades\Log::info('Recalculating leave balances after approval', [
                'cuti_id' => $cuti->id,
                'karyawan_id' => $karyawan->id,
                'jenis_cuti_id' => $cuti->jenis_cuti_id,
                'jenis_cuti' => $cuti->jenisCuti ? $cuti->jenisCuti->nama_jenis : 'Unknown'
            ]);

            // Recalculate all leave balances
            $cutiService->calculateAllLeaveBalances($karyawan);

            // Also specifically recalculate the balance for this leave type
            $cutiService->calculateLeaveBalance($karyawan, $cuti->jenis_cuti_id);
        }

        return redirect()->route('cutis.index')
            ->with('success', 'Pengajuan cuti berhasil disetujui.');
    }

    public function reject(Cuti $cuti)
    {
        $cuti->update(['status_cuti' => 'ditolak']);

        // Clear cache for leave balances for all leave types
        $karyawan = $cuti->karyawan;
        if ($karyawan) {
            // Force recalculation of all leave balances
            $cutiService = new \App\Services\CutiService();

            // Log for debugging
            \Illuminate\Support\Facades\Log::info('Recalculating leave balances after rejection', [
                'cuti_id' => $cuti->id,
                'karyawan_id' => $karyawan->id,
                'jenis_cuti_id' => $cuti->jenis_cuti_id,
                'jenis_cuti' => $cuti->jenisCuti ? $cuti->jenisCuti->nama_jenis : 'Unknown'
            ]);

            // Recalculate all leave balances
            $cutiService->calculateAllLeaveBalances($karyawan);

            // Also specifically recalculate the balance for this leave type
            $cutiService->calculateLeaveBalance($karyawan, $cuti->jenis_cuti_id);
        }

        return redirect()->route('cutis.index')
            ->with('success', 'Pengajuan cuti berhasil ditolak.');
    }

    // Update method to handle ticket status updates
    public function updateTicketStatus(Request $request, TransportasiDetail $transportasiDetail)
    {
        $validated = $request->validate([
            'nomor_tiket' => 'required|string',
            'provider' => 'required|string',
            'waktu_berangkat' => 'required|date',
            'waktu_kembali' => 'required|date|after:waktu_berangkat',
            'biaya_aktual' => 'required|numeric|min:0',
            'perlu_hotel' => 'boolean',
            'hotel_nama' => 'required_if:perlu_hotel,1',
            'hotel_biaya' => 'required_if:perlu_hotel,1|numeric|min:0',
            'status_pemesanan' => 'required|in:belum_dipesan,dipesan,dibayar,selesai',
            'catatan' => 'nullable|string',
        ]);

        $transportasiDetail->update($validated);

        return redirect()->back()
            ->with('success', 'Status tiket berhasil diperbarui.');
    }

    public function calendar()
    {
        $cutis = Cuti::with(['karyawan', 'jenisCuti', 'cutiDetails.jenisCuti'])
            ->get()
            ->map(function ($cuti) {
                // Create a more descriptive title
                $title = $cuti->karyawan->nama;

                // If we have cuti details, use those
                if ($cuti->cutiDetails->count() > 0) {
                    $jenisCuti = $cuti->cutiDetails->map(function($detail) {
                        return $detail->jenisCuti->nama_jenis;
                    })->implode(', ');
                    $title .= ' - ' . $jenisCuti;
                } else {
                    // Fallback to the primary jenis_cuti
                    $title .= ' - ' . $cuti->jenisCuti->nama_jenis;
                }

                return [
                    'id' => $cuti->id,
                    'title' => $title,
                    'start' => $cuti->tanggal_mulai,
                    'end' => Carbon::parse($cuti->tanggal_selesai)->addDay()->format('Y-m-d'), // Add a day for proper display
                    'url' => route('cutis.show', $cuti->id),
                    'backgroundColor' => $this->getStatusColor($cuti->status_cuti),
                    'borderColor' => $this->getStatusColor($cuti->status_cuti),
                ];
            });

        return view('cuti.calendar', compact('cutis'));
    }

    private function getStatusColor($status)
    {
        switch ($status) {
            case 'pending':
                return '#ffc107'; // Warning/yellow
            case 'disetujui':
                return '#28a745'; // Success/green
            case 'ditolak':
                return '#dc3545'; // Danger/red
            default:
                return '#007bff'; // Primary/blue
        }
    }

    public function export()
    {
        $cutis = Cuti::with(['karyawan', 'jenisCuti', 'transportasiDetails.transportasi', 'cutiDetails.jenisCuti'])
            ->get();

        $csvFileName = 'data_cuti_' . date('Y-m-d') . '.csv';

        $headers = [
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=$csvFileName",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];

        $columns = [
            'ID', 'Nama Karyawan', 'NIK', 'Departemen', 'Jenis Cuti',
            'Tanggal Mulai', 'Tanggal Selesai', 'Lama Hari', 'Detail Jenis Cuti', 'Alasan',
            'Transportasi', 'Status Cuti', 'Tanggal Pengajuan'
        ];

        $callback = function() use ($cutis, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($cutis as $cuti) {
                $transportasi = $cuti->transportasiDetails->map(function($detail) {
                    return $detail->transportasi->jenis;
                })->unique()->implode(', ');

                if (empty($transportasi)) {
                    $transportasi = 'Tidak Ada';
                }

                // Format cuti details
                $cutiDetailText = '';
                if ($cuti->cutiDetails->count() > 0) {
                    $cutiDetailText = $cuti->cutiDetails->map(function($detail) {
                        return $detail->jenisCuti->nama_jenis . ' (' . $detail->jumlah_hari . ' hari)';
                    })->implode(', ');
                } else {
                    $cutiDetailText = $cuti->jenisCuti->nama_jenis . ' (' . $cuti->lama_hari . ' hari)';
                }

                fputcsv($file, [
                    $cuti->id,
                    $cuti->karyawan->nama,
                    $cuti->karyawan->nik,
                    $cuti->karyawan->departemen,
                    $cuti->jenisCuti->nama_jenis,
                    $cuti->tanggal_mulai,
                    $cuti->tanggal_selesai,
                    $cuti->lama_hari,
                    $cutiDetailText,
                    $cuti->alasan,
                    $transportasi,
                    $cuti->status_cuti,
                    $cuti->created_at->format('Y-m-d')
                ]);
            }

            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }

    /**
     * Batch approve multiple cuti requests
     */
    public function batchApprove(Request $request)
    {
        $ids = json_decode($request->ids);

        if (!$ids || empty($ids)) {
            return redirect()->route('cutis.index')
                ->with('error', 'Tidak ada data cuti yang dipilih.');
        }

        // Log request untuk debugging
        Log::info('Batch Approve Request', [
            'user' => Auth::check() ? Auth::user()->name : 'System',
            'ids' => $ids
        ]);

        $count = 0;
        $failed = 0;
        $failedIds = [];

        DB::beginTransaction();
        try {
            foreach ($ids as $id) {
                $cuti = Cuti::findOrFail($id);
                if ($cuti->status_cuti === 'pending') {
                    $cuti->status_cuti = 'disetujui';
                    $cuti->tanggal_persetujuan = now();
                    $cuti->approved_by = Auth::check() ? Auth::id() : null;
                    $cuti->save();
                    $count++;

                    // Clear cache for leave balances if this is an annual leave
                    $jenisCuti = $cuti->jenisCuti;
                    if ($jenisCuti && stripos($jenisCuti->nama_jenis, 'tahunan') !== false) {
                        // You can add cache clearing here if you implement caching in the future
                        // For now, we'll just make sure the balance is recalculated
                        $karyawan = $cuti->karyawan;
                        if ($karyawan) {
                            // Force recalculation of leave balance
                            $cutiService = new \App\Services\CutiService();
                            $cutiService->calculateLeaveBalance($karyawan, $jenisCuti->id);
                        }
                    }

                    // Removed CutiApproved event since it doesn't exist
                    // If you have a notification system, you can add it here
                    // Notify the employee that their leave has been approved
                    // if (config('app.env') !== 'testing') {
                    //     event(new \App\Events\CutiApproved($cuti));
                    // }
                }
            }

            DB::commit();
            return redirect()->route('cutis.index')
                ->with('success', "Berhasil menyetujui {$count} pengajuan cuti.");

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error approving cuti batch: ' . $e->getMessage(), [
                'ids' => $ids,
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->route('cutis.index')
                ->with('error', 'Terjadi kesalahan saat menyetujui pengajuan cuti: ' . $e->getMessage());
        }
    }

    /**
     * Batch delete multiple cuti requests
     */
    public function batchDelete(Request $request)
    {
        // Log request untuk debugging
        Log::info('Batch Delete Request', [
            'method' => $request->method(),
            'url' => $request->url(),
            'path' => $request->path(),
            'all_data' => $request->all(),
            'ids' => $request->ids
        ]);

        $ids = json_decode($request->ids);

        if (!$ids || empty($ids)) {
            return redirect()->route('cutis.index')
                ->with('error', 'Tidak ada data cuti yang dipilih.');
        }

        $count = 0;
        foreach ($ids as $id) {
            try {
                $cuti = Cuti::find($id);
                if ($cuti) {
                    $cuti->delete();
                    $count++;
                }
            } catch (\Exception $e) {
                Log::error('Error deleting cuti: ' . $e->getMessage(), ['id' => $id]);
            }
        }

        return redirect()->route('cutis.index')
            ->with('success', "Berhasil menghapus {$count} pengajuan cuti.");
    }

    /**
     * Process batch deletion of leave applications via POST method
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function batchDeletePost(Request $request)
    {
        // Get IDs from comma-separated string
        $ids = $request->input('delete_ids');

        if (empty($ids)) {
            return redirect()->route('cutis.index')
                ->with('warning', 'Tidak ada data yang dipilih untuk dihapus.');
        }

        $idArray = explode(',', $ids);

        try {
            // Attempt to delete the records
            $deleted = Cuti::whereIn('id', $idArray)->delete();

            if ($deleted) {
                return redirect()->route('cutis.index')
                    ->with('success', "Berhasil menghapus {$deleted} pengajuan cuti.");
            } else {
                return redirect()->route('cutis.index')
                    ->with('error', 'Gagal menghapus data. Data mungkin sudah dihapus atau tidak ditemukan.');
            }
        } catch (\Exception $e) {
            // Log the error
            Log::error('Batch delete error: ' . $e->getMessage());

            return redirect()->route('cutis.index')
                ->with('error', 'Terjadi kesalahan saat menghapus data: ' . $e->getMessage());
        }
    }

    /**
     * Process batch approval of leave applications via POST method
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function batchApprovePost(Request $request)
    {
        // Get IDs from comma-separated string
        $ids = $request->input('approve_ids');

        // Log raw input for debugging
        Log::info('Batch Approve Post Raw Input', [
            'approve_ids' => $ids,
            'all_data' => $request->all(),
            'user' => Auth::check() ? Auth::user()->name : 'System'
        ]);

        if (empty($ids)) {
            return redirect()->route('cutis.index')
                ->with('warning', 'Tidak ada data yang dipilih untuk disetujui.');
        }

        $idArray = explode(',', $ids);

        // Filter out any empty values and convert to integers
        $idArray = array_filter(array_map('trim', $idArray));
        $idArray = array_map('intval', $idArray);

        // Log processed IDs
        Log::info('Batch Approve Post Processed IDs', [
            'idArray' => $idArray,
            'count' => count($idArray),
            'user' => Auth::check() ? Auth::user()->name : 'System'
        ]);

        if (empty($idArray)) {
            return redirect()->route('cutis.index')
                ->with('warning', 'Format ID tidak valid. Silakan coba lagi.');
        }

        DB::beginTransaction();
        try {
            $count = 0;

            // Find only pending leave requests
            $pendingCutis = Cuti::whereIn('id', $idArray)
                ->where('status_cuti', 'pending')
                ->get();

            // Log found records
            Log::info('Pending Cutis Found', [
                'count' => $pendingCutis->count(),
                'ids' => $pendingCutis->pluck('id')->toArray(),
                'user' => Auth::check() ? Auth::user()->name : 'System'
            ]);

            foreach ($pendingCutis as $cuti) {
                $cuti->status_cuti = 'disetujui';
                $cuti->tanggal_persetujuan = now();
                $cuti->approved_by = Auth::check() ? Auth::id() : null;
                $cuti->save();
                $count++;

                // Clear cache for leave balances if this is an annual leave
                $jenisCuti = $cuti->jenisCuti;
                if ($jenisCuti && stripos($jenisCuti->nama_jenis, 'tahunan') !== false) {
                    // You can add cache clearing here if you implement caching in the future
                    // For now, we'll just make sure the balance is recalculated
                    $karyawan = $cuti->karyawan;
                    if ($karyawan) {
                        // Force recalculation of leave balance
                        $cutiService = new \App\Services\CutiService();
                        $cutiService->calculateLeaveBalance($karyawan, $jenisCuti->id);
                    }
                }

                // Log each approved record
                Log::info('Cuti Approved', [
                    'id' => $cuti->id,
                    'user' => Auth::check() ? Auth::user()->name : 'System'
                ]);
            }

            DB::commit();

            if ($count > 0) {
                return redirect()->route('cutis.index')
                    ->with('success', "Berhasil menyetujui {$count} pengajuan cuti.");
            } else {
                return redirect()->route('cutis.index')
                    ->with('warning', 'Tidak ada pengajuan cuti yang dapat disetujui. Pastikan pengajuan berstatus "Pending".');
            }

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Batch approve error: ' . $e->getMessage(), [
                'idArray' => $idArray,
                'trace' => $e->getTraceAsString(),
                'user' => Auth::check() ? Auth::user()->name : 'System'
            ]);

            return redirect()->route('cutis.index')
                ->with('error', 'Terjadi kesalahan saat menyetujui pengajuan cuti: ' . $e->getMessage());
        }
    }

    /**
     * Menampilkan halaman monitoring cuti dengan tampilan kalender
     *
     * @return \Illuminate\Http\Response
     */
    public function calendarMonitoring()
    {
        // Mendapatkan SEMUA cuti, bukan hanya yang disetujui
        $cutis = Cuti::with(['karyawan', 'jenisCuti', 'transportasiDetails', 'cutiDetails.jenisCuti'])
                    // ->where('status_cuti', 'disetujui') // Hapus filter ini
                    ->get();

        // Hitung total memo kompensasi bulan ini untuk statistik (dari semua cuti yang ditampilkan)
        $totalMemoKompensasi = Cuti::where(function($query) {
                $query->where('memo_kompensasi_status', true)
                      ->orWhere('memo_kompensasi_status', false);
            })
            ->whereMonth('created_at', now()->month)
            ->count();

        // Memformat data untuk kalender
        $calendarEvents = [];

        // Define colors
        $purpleColor = '#6f42c1'; // Bootstrap Purple
        $blueColor = '#3498db';   // Bootstrap Info (Blue)
        $secondaryColor = '#6c757d'; // Bootstrap Secondary (Gray)

        foreach ($cutis as $cuti) {
            // Check if this leave request has any transportation details
            $hasTransportation = $cuti->transportasiDetails->count() > 0;

            // Determine event color based on new priority rules
            $bgColor = $secondaryColor; // Default to Secondary (no memo, no transport)

            if ($cuti->memo_kompensasi_status !== null) {
                // Priority 1: Memo Kompensasi
                $bgColor = $purpleColor;
            } elseif ($hasTransportation) {
                // Priority 2: With Transportation (and no memo)
                $bgColor = $blueColor;
            }
            // Priority 3 (Secondary) is the default if the above conditions are not met

            // Determine ticket status (for display purposes, not color)
            $ticketStatus = 'Tidak Ada Transportasi';
            if ($hasTransportation) {
                $hasTicket = $cuti->transportasiDetails->contains('status_pemesanan', 'tiket_terbit');
                $ticketStatus = $hasTicket ? 'Tiket Tersedia' : 'Belum Ada Tiket';
            }

            // Get detailed leave types from cutiDetails
            $jenisCutiDetails = [];
            if ($cuti->cutiDetails->count() > 0) {
                foreach ($cuti->cutiDetails as $detail) {
                    $jenisCutiDetails[] = [
                        'nama' => $detail->jenisCuti->nama_jenis ?? 'N/A',
                        'jumlah_hari' => $detail->jumlah_hari
                    ];
                }
            } else {
                // Fallback to the primary jenis_cuti
                $jenisCutiDetails[] = [
                    'nama' => $cuti->jenisCuti->nama_jenis ?? 'N/A', // Handle potential null
                    'jumlah_hari' => $cuti->lama_hari
                ];
            }

            // Check for memo kompensasi status
            $hasKompensasi = false;
            $kompensasiStatus = '';
            $kompensasiInfo = '';

            if ($cuti->memo_kompensasi_status === true) {
                $hasKompensasi = true;
                $kompensasiStatus = 'Sudah Diajukan';
                $kompensasiInfo = "Memo Kompensasi Nomor: " . ($cuti->memo_kompensasi_nomor ?? 'N/A');
            } elseif ($cuti->memo_kompensasi_status === false) {
                $hasKompensasi = true;
                $kompensasiStatus = 'Belum Diajukan';
                $kompensasiInfo = "Perlu mengajukan memo kompensasi";
            }

            $calendarEvents[] = [
                'id' => $cuti->id,
                'title' => $cuti->karyawan->nama,
                'start' => $cuti->tanggal_mulai,
                'end' => date('Y-m-d', strtotime($cuti->tanggal_selesai . ' +1 day')), // End date is exclusive in FullCalendar
                'description' => $cuti->alasan,
                'backgroundColor' => $bgColor, // Use the determined color
                'borderColor' => $bgColor,     // Use the determined color
                'extendedProps' => [
                    'departemen' => $cuti->karyawan->departemen,
                    'jenisCutiDetails' => $jenisCutiDetails,
                    'jenisCuti' => $cuti->cutiDetails->count() > 0
                        ? implode(', ', $cuti->cutiDetails->map(function($detail) {
                            return $detail->jenisCuti->nama_jenis ?? 'N/A'; // Handle potential null
                          })->toArray())
                        : ($cuti->jenisCuti->nama_jenis ?? 'N/A'), // Handle potential null
                    'lamaCuti' => $cuti->lama_hari . ' hari',
                    'ticketStatus' => $ticketStatus,
                    'hasTransportation' => $hasTransportation,
                    'hasKompensasi' => $hasKompensasi, // Keep this for modal info
                    'kompensasiStatus' => $kompensasiStatus, // Keep this for modal info
                    'kompensasiInfo' => $kompensasiInfo, // Keep this for modal info
                    'statusCuti' => $cuti->status_cuti // Tambahkan status cuti ke extendedProps
                ]
            ];
        }

        return view('cuti.calendar', compact('calendarEvents', 'totalMemoKompensasi'));
    }

    /**
     * Menampilkan form untuk mengimpor data cuti
     *
     * @return \Illuminate\Http\Response
     */
    public function importForm()
    {
        return view('cuti.import');
    }

    /**
     * Memproses upload file import cuti
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function processImport(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:2048',
        ], [
            'file.required' => 'File Excel harus diupload',
            'file.mimes' => 'File harus dalam format Excel (xlsx, xls) atau CSV',
            'file.max' => 'Ukuran file maksimal 2MB',
        ]);

        try {
            $import = new \App\Imports\CutiImport;
            $result = $import->import($request->file('file'));

            if ($result['success']) {
                $message = 'Berhasil mengimpor ' . $result['imported'] . ' data cuti';

                // Hanya tampilkan pesan error jika ada baris valid yang gagal diimpor
                if (!empty($result['failures']) && $result['total_rows'] > $result['imported']) {
                    // Hitung jumlah baris valid yang gagal diimpor
                    $failedValidRows = $result['total_rows'] - $result['imported'];
                    if ($failedValidRows > 0) {
                        $message .= ' dan ada ' . $failedValidRows . ' data gagal diimpor.';
                    }
                }

                return redirect()->route('cutis.index')->with('success', $message);
            } else {
                return redirect()->route('cutis.import')
                    ->with('error', $result['message'])
                    ->withInput();
            }
        } catch (\Exception $e) {
            return redirect()->route('cutis.import')
                ->with('error', 'Terjadi kesalahan saat mengimpor data: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Download template import cuti
     *
     * @return \Illuminate\Http\Response
     */
    public function downloadTemplate()
    {
        return Excel::download(new \App\Exports\CutiTemplateExport, 'template_import_cuti.xlsx');
    }
}
