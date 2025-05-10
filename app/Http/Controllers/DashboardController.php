<?php

namespace App\Http\Controllers;

use App\Models\Cuti;
use App\Models\Karyawan;
use App\Models\JenisCuti;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Models\TransportasiDetail;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // Get filter parameters with defaults
        $departemen = $request->input('departemen', 'all');
        $startDate = $request->input('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', now()->endOfMonth()->format('Y-m-d'));
        
        // Find upcoming deadlines for departure and return tickets that haven't been purchased
        $upcomingDeadlines = $this->getUpcomingTicketDeadlines();
        
        // Data untuk dashboard umum
        $totalKaryawan = Karyawan::count();
        $totalCutiPending = Cuti::where('status_cuti', 'pending')->count();
        $totalCutiDisetujui = Cuti::where('status_cuti', 'disetujui')->count();
        $totalCutiDitolak = Cuti::where('status_cuti', 'ditolak')->count();
        
        // Karyawan per departemen
        $karyawanPerDepartemen = Karyawan::select('departemen', DB::raw('count(*) as total'))
                                ->groupBy('departemen')
                                ->orderBy('total', 'desc')
                                ->get();
        
        // Pengajuan cuti per departemen
        $cutiPerDepartemen = Cuti::join('karyawans', 'cutis.karyawan_id', '=', 'karyawans.id')
                          ->where('cutis.status_cuti', 'pending')
                          ->select('karyawans.departemen', DB::raw('count(*) as total'))
                          ->groupBy('karyawans.departemen')
                          ->orderBy('total', 'desc')
                          ->get();
        
        // Status cuti per departemen
        $statusCutiPerDepartemen = Cuti::join('karyawans', 'cutis.karyawan_id', '=', 'karyawans.id')
                                ->select('karyawans.departemen', 'cutis.status_cuti', DB::raw('count(*) as total'))
                                ->groupBy('karyawans.departemen', 'cutis.status_cuti')
                                ->get();
                                
        // Check if there's any data, if not, set to empty array to ensure chart doesn't show data
        if ($statusCutiPerDepartemen->isEmpty()) {
            $statusCutiPerDepartemen = collect([]);
        } else {
            $statusCutiPerDepartemen = $statusCutiPerDepartemen
                                ->groupBy('departemen')
                                ->map(function ($items) {
                                    return [
                                        'disetujui' => $items->where('status_cuti', 'disetujui')->sum('total'),
                                        'ditolak' => $items->where('status_cuti', 'ditolak')->sum('total'),
                                        'pending' => $items->where('status_cuti', 'pending')->sum('total')
                                    ];
                                });
        }
        
        // Total pengajuan cuti (bulan ini)
        $totalCutiMonthly = Cuti::whereMonth('created_at', now()->month)
                            ->whereYear('created_at', now()->year)
                            ->count();
                            
        // Total pengajuan cuti (tahun ini)
        $totalCutiYearly = Cuti::whereYear('created_at', now()->year)
                          ->count();
                          
        // Rata-rata hari cuti per karyawan
        $avgLeaveDays = Cuti::where('status_cuti', 'disetujui')
                      ->whereYear('tanggal_mulai', now()->year)
                      ->avg('lama_hari') ?? 0;
        
        // Cuti terbaru
        $recentCutis = Cuti::with(['karyawan', 'jenisCuti', 'cutiDetails.jenisCuti'])
                        ->orderBy('created_at', 'desc')
                        ->take(5)
                        ->get();
                        
        // Karyawan yang sedang cuti hari ini
        $today = now()->format('Y-m-d');
        $onLeaveTodayCount = Cuti::where('status_cuti', 'disetujui')
                              ->where('tanggal_mulai', '<=', $today)
                              ->where('tanggal_selesai', '>=', $today)
                              ->count();
        
        // Base queries untuk filter HR
        $karyawanQuery = Karyawan::query();
        $cutiQuery = Cuti::query();
        
        // Apply department filter if not 'all'
        if ($departemen !== 'all') {
            $karyawanQuery->where('departemen', $departemen);
            $cutiQuery->whereHas('karyawan', function($q) use ($departemen) {
                $q->where('departemen', $departemen);
            });
        }
        
        // Date range filter for cuti data
        $cutiQuery->whereBetween('tanggal_mulai', [$startDate, $endDate]);
        
        // Get department list for filter dropdown
        $departements = Karyawan::select('departemen')->distinct()->pluck('departemen');
        
        // Get statistics
        $totalKaryawanByDept = $karyawanQuery->count();
        $cutiPendingByDept = (clone $cutiQuery)->where('status_cuti', 'pending')->count();
        $cutiDisetujuiByDept = (clone $cutiQuery)->where('status_cuti', 'disetujui')->count();
        $cutiDitolakByDept = (clone $cutiQuery)->where('status_cuti', 'ditolak')->count();
        
        // Get employees currently on leave with department grouping
        $karyawanCutiToday = Karyawan::whereHas('cutis', function($q) use ($today) {
            $q->where('status_cuti', 'disetujui')
              ->where('tanggal_mulai', '<=', $today)
              ->where('tanggal_selesai', '>=', $today);
        })->when($departemen !== 'all', function($q) use ($departemen) {
            $q->where('departemen', $departemen);
        })->with(['cutis' => function($q) use ($today) {
            $q->where('status_cuti', 'disetujui')
              ->where('tanggal_mulai', '<=', $today)
              ->where('tanggal_selesai', '>=', $today);
        }, 'cutis.jenisCuti'])->get();
        
        // Group employees on leave by department
        $karyawanCutiByDepartement = $karyawanCutiToday->groupBy('departemen');
        
        // Get employees who will be on leave this week and month
        $oneWeekLater = Carbon::now()->addDays(7)->format('Y-m-d');
        $oneMonthLater = Carbon::now()->addDays(30)->format('Y-m-d');
        $nextMonthStart = Carbon::now()->addMonth()->startOfMonth()->format('Y-m-d');
        $nextMonthEnd = Carbon::now()->addMonth()->endOfMonth()->format('Y-m-d');
        
        $karyawanCutiThisWeek = Karyawan::whereHas('cutis', function($q) use ($today, $oneWeekLater) {
            $q->where('status_cuti', 'disetujui')
              ->where('tanggal_mulai', '>', $today)
              ->where('tanggal_mulai', '<=', $oneWeekLater);
        })->with(['cutis' => function($q) use ($today, $oneWeekLater) {
            $q->where('status_cuti', 'disetujui')
              ->where('tanggal_mulai', '>', $today)
              ->where('tanggal_mulai', '<=', $oneWeekLater);
        }, 'cutis.jenisCuti'])->take(5)->get();
        
        $karyawanCutiThisMonth = Karyawan::whereHas('cutis', function($q) use ($oneWeekLater, $oneMonthLater) {
            $q->where('status_cuti', 'disetujui')
              ->where('tanggal_mulai', '>', $oneWeekLater)
              ->where('tanggal_mulai', '<=', $oneMonthLater);
        })->with(['cutis' => function($q) use ($oneWeekLater, $oneMonthLater) {
            $q->where('status_cuti', 'disetujui')
              ->where('tanggal_mulai', '>', $oneWeekLater)
              ->where('tanggal_mulai', '<=', $oneMonthLater);
        }, 'cutis.jenisCuti'])->take(5)->get();
        
        // Get employees who will be on leave next month
        $karyawanCutiNextMonth = Karyawan::whereHas('cutis', function($q) use ($nextMonthStart, $nextMonthEnd) {
            $q->where('status_cuti', 'disetujui')
              ->where(function($query) use ($nextMonthStart, $nextMonthEnd) {
                  // Either start date is in next month
                  $query->whereBetween('tanggal_mulai', [$nextMonthStart, $nextMonthEnd])
                  // Or end date is in next month
                  ->orWhereBetween('tanggal_selesai', [$nextMonthStart, $nextMonthEnd])
                  // Or the leave spans the entire next month
                  ->orWhere(function($q2) use ($nextMonthStart, $nextMonthEnd) {
                      $q2->where('tanggal_mulai', '<', $nextMonthStart)
                         ->where('tanggal_selesai', '>', $nextMonthEnd);
                  });
              });
        })->with(['cutis' => function($q) use ($nextMonthStart, $nextMonthEnd) {
            $q->where('status_cuti', 'disetujui')
              ->where(function($query) use ($nextMonthStart, $nextMonthEnd) {
                  $query->whereBetween('tanggal_mulai', [$nextMonthStart, $nextMonthEnd])
                  ->orWhereBetween('tanggal_selesai', [$nextMonthStart, $nextMonthEnd])
                  ->orWhere(function($q2) use ($nextMonthStart, $nextMonthEnd) {
                      $q2->where('tanggal_mulai', '<', $nextMonthStart)
                         ->where('tanggal_selesai', '>', $nextMonthEnd);
                  });
              });
        }, 'cutis.jenisCuti'])->get();
        
        // Group next month's leave by department
        $karyawanCutiNextMonthByDept = $karyawanCutiNextMonth->groupBy('departemen');
        
        // Get cuti data by department for chart with proper average calculation
        $cutiByDepartment = Cuti::join('karyawans', 'cutis.karyawan_id', '=', 'karyawans.id')
                       ->when($departemen !== 'all', function($q) use ($departemen) {
                           $q->where('karyawans.departemen', $departemen);
                       })
                       ->where('cutis.status_cuti', 'disetujui')
                       ->selectRaw('
                           karyawans.departemen, 
                           COUNT(*) as total,
                           SUM(cutis.lama_hari) as total_hari,
                           AVG(cutis.lama_hari) as rata_rata_hari
                       ')
                       ->groupBy('karyawans.departemen')
                       ->orderBy('total', 'desc')
                       ->get();
                       
        // If no data, get all-time data instead without date filtering
        if ($cutiByDepartment->isEmpty()) {
            $cutiByDepartment = Cuti::join('karyawans', 'cutis.karyawan_id', '=', 'karyawans.id')
                       ->where('cutis.status_cuti', 'disetujui')
                       ->when($departemen !== 'all', function($q) use ($departemen) {
                           $q->where('karyawans.departemen', $departemen);
                       })
                       ->selectRaw('
                           karyawans.departemen, 
                           COUNT(*) as total,
                           SUM(cutis.lama_hari) as total_hari,
                           AVG(cutis.lama_hari) as rata_rata_hari
                       ')
                       ->groupBy('karyawans.departemen')
                       ->orderBy('total', 'desc')
                       ->get();
        }
        
        // Get monthly trend data
        $monthlyTrend = Cuti::selectRaw('MONTH(tanggal_mulai) as bulan, COUNT(*) as total')
                       ->whereYear('tanggal_mulai', now()->year)
                       ->groupBy('bulan')
                       ->orderBy('bulan')
                       ->get()
                       ->pluck('total', 'bulan')
                       ->toArray();
                       
        // Prepare monthly data array with all months
        $monthlyData = [];
        for ($i = 1; $i <= 12; $i++) {
            $monthlyData[$i] = $monthlyTrend[$i] ?? 0;
        }
                       
        // Cuti terbaru yang menunggu persetujuan dengan pagination    
        $pendingCutis = Cuti::with(['karyawan', 'jenisCuti'])
                        ->where('status_cuti', 'pending')
                        ->orderBy('created_at', 'asc')
                        ->paginate(5, ['*'], 'pending_page');
        
        // Temporarily disabled - Saldo Cuti Karyawan
        /*
        $saldoCuti = JenisCuti::where('jatah_hari', '>', 0)
                    ->with(['cutis' => function($q) {
                        $q->select('jenis_cuti_id', 'karyawan_id', 
                                DB::raw('SUM(CASE WHEN cutis.status_cuti = "disetujui" THEN cutis.lama_hari ELSE 0 END) as total_used'))
                        ->whereYear('tanggal_mulai', now()->year)
                        ->groupBy('jenis_cuti_id', 'karyawan_id');
                    }, 'cutis.karyawan'])
                    ->get()
                    ->map(function($jenisCuti) {
                        $karyawans = Karyawan::get(['id', 'nama', 'departemen']);
                        
                        return [
                            'jenis_cuti' => $jenisCuti->nama_jenis,
                            'kuota' => $jenisCuti->jatah_hari,
                            'karyawans' => $karyawans->map(function($karyawan) use ($jenisCuti) {
                                $cutiKaryawan = $jenisCuti->cutis->firstWhere('karyawan_id', $karyawan->id);
                                $used = $cutiKaryawan ? $cutiKaryawan->total_used : 0;
                                $remaining = $jenisCuti->jatah_hari - $used;
                                
                                return [
                                    'id' => $karyawan->id,
                                    'nama' => $karyawan->nama,
                                    'departemen' => $karyawan->departemen,
                                    'used' => $used,
                                    'remaining' => $remaining
                                ];
                            })
                        ];
                    });
        */
        // Empty placeholder for the disabled feature
        $saldoCuti = [];
                    
        // Temporarily disabled - Notifikasi: cuti yang melebihi kuota
        /*
        $overQuotaCutis = Cuti::with(['karyawan', 'jenisCuti'])
                        ->where('status_cuti', 'pending')
                        ->whereHas('jenisCuti', function($q) {
                            $q->where('jatah_hari', '>', 0);
                        })
                        ->get()
                        ->filter(function($cuti) {
                            $jenisCuti = $cuti->jenisCuti;
                            
                            $usedDays = Cuti::where('karyawan_id', $cuti->karyawan_id)
                                        ->where('jenis_cuti_id', $cuti->jenis_cuti_id)
                                        ->where('status_cuti', 'disetujui')
                                        ->whereYear('tanggal_mulai', now()->year)
                                        ->sum('lama_hari');
                                        
                            return ($usedDays + $cuti->lama_hari) > $jenisCuti->jatah_hari;
                        });
        */
        // Empty placeholder for the disabled feature
        $overQuotaCutis = collect([]);
                        
        // Karyawan yang belum ambil cuti sama sekali tahun ini
        $noLeaveTaken = Karyawan::whereDoesntHave('cutis', function($q) {
                        $q->whereYear('tanggal_mulai', now()->year)
                          ->where('status_cuti', 'disetujui');
                      })
                      ->take(5)
                      ->get();
        
        return view('dashboard', compact(
            'totalKaryawan', 
            'totalCutiPending', 
            'totalCutiDisetujui', 
            'totalCutiDitolak',
            'totalCutiMonthly',
            'totalCutiYearly',
            'avgLeaveDays',
            'recentCutis',
            'onLeaveTodayCount',
            'departements', 
            'departemen',
            'startDate',
            'endDate',
            'totalKaryawanByDept',
            'cutiPendingByDept',
            'cutiDisetujuiByDept',
            'cutiDitolakByDept',
            'karyawanCutiToday',
            'karyawanCutiThisWeek',
            'karyawanCutiThisMonth',
            'karyawanCutiNextMonth',
            'karyawanCutiNextMonthByDept',
            'cutiByDepartment',
            'pendingCutis',
            'saldoCuti',
            'overQuotaCutis',
            'noLeaveTaken',
            'monthlyData',
            'upcomingDeadlines',
            'karyawanPerDepartemen',
            'cutiPerDepartemen',
            'statusCutiPerDepartemen',
            'karyawanCutiByDepartement'
        ));
    }

    public function report()
    {
        $tahun = request('tahun', date('Y'));
        $bulan = request('bulan', date('m'));
        
        // Data statistik bulanan
        $monthlyCuti = Cuti::whereYear('tanggal_mulai', $tahun)
                    ->selectRaw('MONTH(tanggal_mulai) as bulan, COUNT(*) as total')
                    ->groupBy('bulan')
                    ->get()
                    ->pluck('total', 'bulan')
                    ->toArray();
        
        // Statistik per departemen
        $departmentStats = Cuti::join('karyawans', 'cutis.karyawan_id', '=', 'karyawans.id')
                    ->whereYear('tanggal_mulai', $tahun)
                    ->selectRaw('karyawans.departemen, COUNT(*) as total')
                    ->groupBy('karyawans.departemen')
                    ->get();
        
        return view('reports.index', compact('monthlyCuti', 'departmentStats', 'tahun', 'bulan'));
    }

    /**
     * Get upcoming deadlines for tickets that haven't been purchased
     * Includes both departure and return tickets
     * 
     * @return \Illuminate\Database\Eloquent\Collection
     */
    private function getUpcomingTicketDeadlines()
    {
        // Get tickets that haven't been purchased and are within the next 7 days
        $oneWeekLater = now()->addDays(7);
        $today = now();
        
        // Get approved leave requests with transportation details
        $tickets = TransportasiDetail::with(['cuti.karyawan', 'transportasi'])
            ->whereHas('cuti', function($query) {
                $query->where('status_cuti', 'disetujui');
            })
            ->where('status_pemesanan', 'belum_dipesan')
            ->where(function($query) use ($oneWeekLater, $today) {
                // For departure tickets (pergi) nearing the departure date
                $query->where(function($q) use ($oneWeekLater, $today) {
                    $q->where('jenis_perjalanan', 'pergi')
                      ->whereHas('cuti', function($q2) use ($oneWeekLater, $today) {
                          $q2->whereBetween('tanggal_mulai', [$today, $oneWeekLater]);
                      });
                })
                // For return tickets (kembali) nearing the return date
                ->orWhere(function($q) use ($oneWeekLater, $today) {
                    $q->where('jenis_perjalanan', 'kembali')
                      ->whereHas('cuti', function($q2) use ($oneWeekLater, $today) {
                          $q2->whereBetween('tanggal_selesai', [$today, $oneWeekLater]);
                      });
                });
            })
            ->get();
            
        // Sort the collection after fetching the data
        return $tickets->sortBy(function($ticket) {
            if ($ticket->jenis_perjalanan == 'pergi') {
                return $ticket->cuti->tanggal_mulai;
            } else {
                return $ticket->cuti->tanggal_selesai;
            }
        });
    }
}